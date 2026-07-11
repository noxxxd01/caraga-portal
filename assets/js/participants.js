/**
 * Participants Penetration tab: CSV bulk import, single-registrant CRUD,
 * ledger filtering, and the provincial Male/Female breakdown chart.
 */

function fetchParticipants() {
  fetch("api/participants_get.php", { cache: "no-store" })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        participantsData = res.participants;
        renderParticipantsLedgerTable();
        updateParticipantsSummary();
      }
    })
    .catch((err) => console.error("Failed to load participants:", err));
}

function updateParticipantsSummary() {
  const total = participantsData.length;
  const maleCount = participantsData.filter((p) => p.sex === "Male").length;
  const femaleCount = participantsData.filter((p) => p.sex === "Female").length;
  const linkedCourses = new Set(
    participantsData.map((p) => p.training_id).filter(Boolean),
  ).size;

  document.getElementById("part-total-count").innerText = total;
  document.getElementById("part-male-count").innerText = maleCount;
  document.getElementById("part-female-count").innerText = femaleCount;
  document.getElementById("part-linked-courses").innerText = linkedCourses;

  const caragaProvinces = Object.keys(CARAGA_PROVINCE_COORDINATES);
  const outsideProvinces = [
    ...new Set(
      participantsData
        .map((p) => p.province)
        .filter((p) => p && p.trim() !== "" && !caragaProvinces.includes(p)),
    ),
  ].sort();

  // Chart: the 7 Caraga provinces get their own bar each; everything
  // outside Caraga is aggregated into one combined "Other" bar so the
  // chart stays readable no matter how many distinct outside provinces
  // get entered over time.
  const chartLabels = [...caragaProvinces, "Other"];

  const maleByProvince = caragaProvinces.map(
    (name) =>
      participantsData.filter((p) => p.province === name && p.sex === "Male")
        .length,
  );
  const femaleByProvince = caragaProvinces.map(
    (name) =>
      participantsData.filter((p) => p.province === name && p.sex === "Female")
        .length,
  );

  const otherMaleCount = participantsData.filter(
    (p) => outsideProvinces.includes(p.province) && p.sex === "Male",
  ).length;
  const otherFemaleCount = participantsData.filter(
    (p) => outsideProvinces.includes(p.province) && p.sex === "Female",
  ).length;

  maleByProvince.push(otherMaleCount);
  femaleByProvince.push(otherFemaleCount);

  recalcParticipantsChart(chartLabels, maleByProvince, femaleByProvince);

  // Ledger filter dropdown still lists each outside province individually,
  // so you can still filter down to a specific one if needed
  refreshParticipantsProvinceFilterOptions(caragaProvinces, outsideProvinces);
}

function refreshParticipantsProvinceFilterOptions(
  caragaProvinces,
  outsideProvinces,
) {
  const filterSelect = document.getElementById("part-province-filter");
  if (!filterSelect) return;
  const previousSelection = filterSelect.value;

  let optionsHtml = '<option value="">All Provinces (Auto-Resolved)</option>';
  caragaProvinces.forEach((name) => {
    optionsHtml += `<option value="${name}">${name}</option>`;
  });
  if (outsideProvinces.length > 0) {
    optionsHtml += `<optgroup label="Outside Caraga Region">`;
    outsideProvinces.forEach((name) => {
      optionsHtml += `<option value="${name}">${name}</option>`;
    });
    optionsHtml += `</optgroup>`;
  }
  filterSelect.innerHTML = optionsHtml;

  if (
    [...filterSelect.options].some((opt) => opt.value === previousSelection)
  ) {
    filterSelect.value = previousSelection;
  }
}

function downloadCSVTemplate() {
  const headers = [
    "Participant Name",
    "Project",
    "Program",
    "Training Title",
    "Training Date",
    "Training ID",
    "CertID",
    "Certificate Type",
    "Resource Person",
    "Sex",
  ];
  const sampleRow = [
    "Juan Dela Cruz",
    "Free Wi-Fi for All",
    "ICT Literacy",
    "ISMS Compliance Auditing",
    "2026-06-01",
    "tr-101",
    "CERT-2026-0001",
    "Certificate of Completion",
    "Engr. Ricardo Salvador",
    "Male",
  ];
  const escapeCsv = (val) => `"${String(val).replace(/"/g, '""')}"`;
  const csvContent = [headers, sampleRow]
    .map((row) => row.map(escapeCsv).join(","))
    .join("\r\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "dict-caraga-participants-template.csv";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function handleCSVUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  Papa.parse(file, {
    header: true,
    skipEmptyLines: true,
    complete: function (results) {
      const rows = results.data;
      if (!rows || rows.length === 0) {
        Swal.fire("Empty File", "No rows were found in that CSV.", "warning");
        event.target.value = "";
        return;
      }
      fetch("api/participants_bulk_import.php", {
        method: "POST",
        body: JSON.stringify(rows),
      })
        .then((res) => res.json())
        .then((data) => {
          event.target.value = "";
          if (data.status === "success") {
            fetchParticipants();
            Swal.fire({
              title: "Import Complete",
              text: `${data.inserted} registrant(s) imported${data.skipped ? `, ${data.skipped} row(s) skipped (missing name)` : ""}.`,
              icon: "success",
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timer: 3000,
            });
          } else {
            Swal.fire(
              "Import Failed",
              data.message || "An error occurred while importing.",
              "error",
            );
          }
        })
        .catch((err) => {
          event.target.value = "";
          console.error("CSV import error:", err);
          Swal.fire("Import Failed", "Could not reach the server.", "error");
        });
    },
    error: function (err) {
      event.target.value = "";
      Swal.fire(
        "Parse Error",
        "Could not read that CSV file: " + err.message,
        "error",
      );
    },
  });
}

function openParticipantModal(id) {
  const form = document.getElementById("participant-form");
  form.reset();
  document.getElementById("participant-id").value = "";
  document.getElementById("participant-training-match").innerText = "";

  if (id) {
    const record = participantsData.find((p) => p.id === id);
    if (!record) return;
    document.getElementById("participant-modal-title").innerText =
      "Edit Registrant";
    document.getElementById("participant-id").value = record.id;
    document.getElementById("participant-name").value = record.participant_name;
    document.getElementById("participant-sex").value = record.sex;
    document.getElementById("participant-project").value = record.project || "";
    document.getElementById("participant-program").value = record.program || "";
    document.getElementById("participant-training-id").value =
      record.training_id || "";
    document.getElementById("participant-training-title").value =
      record.training_title || "";
    document.getElementById("participant-training-date").value =
      record.training_date || "";
    document.getElementById("participant-resource-person").value =
      record.resource_person || "";
    document.getElementById("participant-cert-id").value = record.cert_id || "";
    document.getElementById("participant-cert-type").value =
      record.certificate_type || "";
    const knownProvinces = Object.keys(CARAGA_PROVINCE_COORDINATES);
    if (record.province && !knownProvinces.includes(record.province)) {
      document.getElementById("participant-province").value = "__OTHER__";
      document.getElementById("participant-province-other").value =
        record.province;
    } else {
      document.getElementById("participant-province").value =
        record.province || "";
    }
    onParticipantProvinceSelectChange();
    document.getElementById("participant-municipality").value =
      record.municipality || "";
  } else {
    document.getElementById("participant-modal-title").innerText =
      "Add Single Registrant";
  }

  document.getElementById("participant-modal").classList.remove("hidden");
}

function closeParticipantModal() {
  document.getElementById("participant-modal").classList.add("hidden");
}

function onParticipantProvinceSelectChange() {
  const select = document.getElementById("participant-province");
  const otherInput = document.getElementById("participant-province-other");
  if (select.value === "__OTHER__") {
    otherInput.classList.remove("hidden");
    otherInput.setAttribute("required", "required");
  } else {
    otherInput.classList.add("hidden");
    otherInput.removeAttribute("required");
    otherInput.value = "";
  }
}

function onParticipantTrainingIdChange() {
  const trainingId = document
    .getElementById("participant-training-id")
    .value.trim();
  const matchEl = document.getElementById("participant-training-match");
  if (!trainingId) {
    matchEl.innerText = "";
    return;
  }

  const match = (db || []).find((t) => t.id === trainingId);
  if (match) {
    matchEl.innerHTML = `<span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check"></i> Matched: ${match.training_title} — ${match.province}</span>`;
    document.getElementById("participant-training-title").value =
      match.training_title;
    document.getElementById("participant-province").value = match.province;
    document.getElementById("participant-municipality").value =
      match.municipality;
    onParticipantProvinceSelectChange();
  } else {
    matchEl.innerHTML = `<span class="text-amber-600 font-bold"><i class="fa-solid fa-triangle-exclamation"></i> No matching Training ID found in the Tracker — Province/Municipality will need to be set manually.</span>`;
  }
}

function handleParticipantSubmit(event) {
  event.preventDefault();
  let formData = new FormData();
  formData.append("id", document.getElementById("participant-id").value);
  formData.append(
    "participant_name",
    document.getElementById("participant-name").value,
  );
  formData.append("sex", document.getElementById("participant-sex").value);
  formData.append(
    "project",
    document.getElementById("participant-project").value,
  );
  formData.append(
    "program",
    document.getElementById("participant-program").value,
  );
  formData.append(
    "training_id",
    document.getElementById("participant-training-id").value,
  );
  formData.append(
    "training_title",
    document.getElementById("participant-training-title").value,
  );
  formData.append(
    "training_date",
    document.getElementById("participant-training-date").value,
  );
  formData.append(
    "resource_person",
    document.getElementById("participant-resource-person").value,
  );
  formData.append(
    "cert_id",
    document.getElementById("participant-cert-id").value,
  );
  formData.append(
    "certificate_type",
    document.getElementById("participant-cert-type").value,
  );
  const provinceSelectValue = document.getElementById(
    "participant-province",
  ).value;
  const provinceOtherValue = document
    .getElementById("participant-province-other")
    .value.trim();
  const finalProvince =
    provinceSelectValue === "__OTHER__"
      ? provinceOtherValue
      : provinceSelectValue;
  formData.append("province", finalProvince);
  formData.append(
    "municipality",
    document.getElementById("participant-municipality").value,
  );

  fetch("api/participants_save.php", { method: "POST", body: formData })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        closeParticipantModal();
        fetchParticipants();
        Swal.fire({
          title: "Registrant Saved",
          text: "Participant record saved to the SQL database.",
          icon: "success",
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 2200,
        });
      } else {
        Swal.fire(
          "Save Failed",
          data.message || "An error occurred while saving.",
          "error",
        );
      }
    })
    .catch((err) => {
      console.error("Save participant error:", err);
      Swal.fire("Save Failed", "Could not reach the server.", "error");
    });
}

function deleteParticipant(id) {
  Swal.fire({
    title: "Delete Registrant?",
    text: "This will permanently remove this participant record.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#EF4444",
    cancelButtonColor: "#6B7280",
    confirmButtonText: "Yes, delete it",
  }).then((result) => {
    if (result.isConfirmed) {
      let formData = new FormData();
      formData.append("id", id);
      fetch("api/participants_delete.php", { method: "POST", body: formData })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            fetchParticipants();
            Swal.fire(
              "Deleted!",
              "Participant record removed from the database.",
              "success",
            );
          }
        });
    }
  });
}

function renderParticipantsLedgerTable() {
  const tbody = document.getElementById("participants-table-body");
  const emptyState = document.getElementById("participants-empty-state");
  if (!tbody) return;

  const keyword = (
    document.getElementById("part-search-input").value || ""
  ).toLowerCase();
  const provinceFilter = document.getElementById("part-province-filter").value;
  const sexFilter = document.getElementById("part-sex-filter").value;

  const filtered = participantsData.filter((p) => {
    const matchesProvince =
      provinceFilter === "" || p.province === provinceFilter;
    const matchesSex = sexFilter === "" || p.sex === sexFilter;
    const matchesKeyword =
      keyword === "" ||
      (p.participant_name || "").toLowerCase().includes(keyword) ||
      (p.project || "").toLowerCase().includes(keyword) ||
      (p.program || "").toLowerCase().includes(keyword) ||
      (p.cert_id || "").toLowerCase().includes(keyword) ||
      (p.resource_person || "").toLowerCase().includes(keyword);
    return matchesProvince && matchesSex && matchesKeyword;
  });

  tbody.innerHTML = "";

  if (filtered.length === 0) {
    emptyState.classList.remove("hidden");
    return;
  } else {
    emptyState.classList.add("hidden");
  }

  filtered.forEach((p) => {
    const tr = document.createElement("tr");
    tr.className = "hover:bg-slate-50 transition-colors";
    tr.innerHTML = `
            <td class="p-4 font-bold text-slate-900">${p.participant_name}</td>
            <td class="p-4 text-slate-600">${p.project || "—"}</td>
            <td class="p-4 text-slate-600">${p.program || "—"}</td>
            <td class="p-4 text-slate-600">${p.training_title || "—"}</td>
            <td class="p-4 text-slate-600 whitespace-nowrap">${p.training_date || "—"}</td>
            <td class="p-4 font-mono text-slate-500">${p.training_id || "—"}</td>
            <td class="p-4 font-mono text-purple-600">${p.cert_id || "—"}</td>
            <td class="p-4 text-slate-600">${p.certificate_type || "—"}</td>
            <td class="p-4 text-slate-600">${p.resource_person || "—"}</td>
            <td class="p-4 text-slate-700 font-semibold">${p.province || "—"}</td>
            <td class="p-4 text-slate-600">${p.municipality || "—"}</td>
            <td class="p-4">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${p.sex === "Female" ? "bg-pink-50 text-pink-700" : "bg-blue-50 text-blue-700"}">
                    <i class="fa-solid ${p.sex === "Female" ? "fa-venus" : "fa-mars"}"></i> ${p.sex || "—"}
                </span>
            </td>
            <td class="p-4 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1.5">
                    <button onclick="openParticipantModal('${p.id}')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button onclick="deleteParticipant('${p.id}')" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg" title="Delete">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </td>
        `;
    tbody.appendChild(tr);
  });
}

function clearParticipantsFilters() {
  document.getElementById("part-search-input").value = "";
  document.getElementById("part-province-filter").value = "";
  document.getElementById("part-sex-filter").value = "";
  renderParticipantsLedgerTable();
}
