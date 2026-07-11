/**
 * Training Tracker tab: table rendering, CRUD modal, CSV export.
 */

function populateTrackerTable(dataList) {
  const tbody = document.getElementById("tracker-table-body");
  const emptyState = document.getElementById("tracker-empty-state");
  tbody.innerHTML = "";

  if (dataList.length === 0) {
    emptyState.classList.remove("hidden");
    return;
  } else {
    emptyState.classList.add("hidden");
  }

  dataList.forEach((t) => {
    const color = STATUS_HEX_COLORS[t.status] || "#3B82F6";
    const budgetSaved = Math.max(
      0,
      parseFloat(t.budget_allocated || 0) - parseFloat(t.budget_utilized || 0),
    );
    const totalParticipants =
      t.actual_participants ||
      Number(t.male_participants || 0) + Number(t.female_participants || 0);

    const row = document.createElement("tr");
    row.className =
      "hover:bg-slate-50 border-b border-slate-100 transition-all";
    row.innerHTML = `
            <td class="p-4">
                <span class="text-slate-400 font-mono block text-[10px]">${t.course_code}</span>
                <span class="font-extrabold text-slate-900 block">${t.training_title}</span>
            </td>
            <td class="p-4 font-semibold text-slate-700">${t.course_type || "Webinar"} (${t.duration_hours || 3}h)</td>
            <td class="p-4 text-slate-700 font-bold">${t.province}</td>
            <td class="p-4 text-slate-600 font-medium"><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i>${t.venue}, ${t.province}</td>
            <td class="p-4">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900">${totalParticipants} Total</span>
                    <span class="text-[10px] text-slate-400 font-bold">(♂ ${t.male_participants || 0} | ♀ ${t.female_participants || 0})</span>
                </div>
            </td>
            <td class="p-4 text-right">
                <span class="block font-black text-slate-900">${formatCurrency(t.budget_utilized)}</span>
                <span class="block text-[10px] text-emerald-600 font-bold">Saved: ${formatCurrency(budgetSaved)}</span>
            </td>
            <td class="p-4">
                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] tracking-wide uppercase text-white" style="background-color: ${color}">${t.status}</span>
            </td>
            <td class="p-4 text-center">
                ${t.drive_link ? `<a href="${t.drive_link}" target="_blank" class="text-dict-bright hover:underline font-bold"><i class="fa-solid fa-folder-open mr-1"></i>Drive Link</a>` : `<span class="text-slate-300 italic">None</span>`}
            </td>
            <td class="p-4 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button onclick="openFormModal('${t.id}')" class="p-1 text-slate-400 hover:text-dict-bright" title="Edit"><i class="fa-solid fa-pen"></i></button>
                    <button onclick="cloneRecord('${t.id}')" class="p-1 text-slate-400 hover:text-blue-500" title="Clone"><i class="fa-solid fa-copy"></i></button>
                    <button onclick="deleteRecord('${t.id}')" class="p-1 text-slate-400 hover:text-red-500" title="Delete"><i class="fa-solid fa-trash"></i></button>
                </div>
            </td>
        `;
    tbody.appendChild(row);
  });
}

// Financial Variance Calculations based on actual PMT allocated lists

function handleFormSubmission(event) {
  event.preventDefault();
  const id = document.getElementById("form-id").value;
  const lat = parseFloat(document.getElementById("form-latitude").value);
  const lng = parseFloat(document.getElementById("form-longitude").value);

  if (lat < 7.5 || lat > 11.5 || lng < 124.5 || lng > 127.0) {
    Swal.fire({
      title: "Out of Bounds GPS",
      text: "Warning: Coordinates must reside within standard Caraga regional limits.",
      icon: "warning",
      confirmButtonColor: "#1E40AF",
    });
    return;
  }

  let formData = new FormData();
  formData.append("id", id);
  formData.append(
    "training_title",
    document.getElementById("form-title").value,
  );
  formData.append("course_code", document.getElementById("form-code").value);
  formData.append(
    "course_name",
    document.getElementById("form-description").value,
  );
  formData.append(
    "implementation_mode",
    document.getElementById("form-implementation-mode").value,
  );
  formData.append("province", document.getElementById("form-province").value);
  formData.append(
    "municipality",
    document.getElementById("form-municipality").value,
  );
  formData.append("barangay", document.getElementById("form-barangay").value);
  formData.append("venue", document.getElementById("venue").value);
  formData.append("latitude", lat);
  formData.append("longitude", lng);
  formData.append(
    "start_date",
    document.getElementById("form-start-date").value,
  );
  formData.append("end_date", document.getElementById("form-end-date").value);
  formData.append(
    "course_officer",
    document.getElementById("form-course-officer").value,
  );
  formData.append(
    "resource_person",
    document.getElementById("form-resource-person").value,
  );
  formData.append(
    "target_participants",
    document.getElementById("form-target-participants").value,
  );
  formData.append(
    "male_participants",
    document.getElementById("form-male-participants").value,
  );
  formData.append(
    "female_participants",
    document.getElementById("form-female-participants").value,
  );
  formData.append(
    "budget_allocated",
    document.getElementById("form-budget-allocated").value,
  );
  formData.append(
    "budget_utilize",
    document.getElementById("form-budget-utilized").value,
  );
  formData.append("status", document.getElementById("form-status").value);
  formData.append(
    "course_type",
    document.getElementById("form-course-type").value,
  );
  formData.append(
    "duration_hours",
    document.getElementById("form-duration").value,
  );
  formData.append(
    "drive_link",
    document.getElementById("form-drive-link").value,
  );

  if (document.getElementById("form-photos").checked)
    formData.append("photos", "1");
  if (document.getElementById("form-documents").checked)
    formData.append("documents", "1");

  fetch("api/training_save.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        closeFormModal();
        synchronizeDashboardState();
        Swal.fire({
          title: id ? "Training Updated" : "Training Registered",
          text: "Record saved to the SQL database.",
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
      console.error("Save training error:", err);
      Swal.fire("Save Failed", "Could not reach the server.", "error");
    });
}

//==================================================================
// TAB NAVIGATION CONTROLLER
//==================================================================

function openFormModal(id) {
  const form = document.getElementById("crud-form");
  form.reset();
  document.getElementById("form-id").value = "";
  document.getElementById("form-actual-participants").value = "0";
  document.getElementById("form-budget-saved").value = "0.00";

  if (id) {
    const record = db.find((t) => t.id === id);
    if (!record) return;

    document.getElementById("modal-form-title").innerText =
      "Edit Training Record";
    document.getElementById("form-id").value = record.id;
    document.getElementById("form-title").value = record.training_title;
    document.getElementById("form-code").value = record.course_code;
    document.getElementById("form-description").value =
      record.course_name || "";
    document.getElementById("form-implementation-mode").value =
      record.implementation_mode || "Face-to-Face";
    document.getElementById("form-course-type").value =
      record.course_type || "Webinar";
    document.getElementById("form-province").value = record.province;
    document.getElementById("form-municipality").value = record.municipality;
    document.getElementById("form-barangay").value = record.barangay;
    document.getElementById("venue").value = record.venue;
    document.getElementById("form-latitude").value = record.latitude;
    document.getElementById("form-longitude").value = record.longitude;
    document.getElementById("form-start-date").value = record.start_date;
    document.getElementById("form-end-date").value = record.end_date;
    document.getElementById("form-course-officer").value =
      record.course_officer;
    document.getElementById("form-resource-person").value =
      record.resource_person;
    document.getElementById("form-target-participants").value =
      record.target_participants;
    document.getElementById("form-male-participants").value =
      record.male_participants;
    document.getElementById("form-female-participants").value =
      record.female_participants;
    document.getElementById("form-budget-allocated").value =
      record.budget_allocated;
    document.getElementById("form-budget-utilized").value =
      record.budget_utilized;
    document.getElementById("form-status").value = record.status;
    document.getElementById("form-drive-link").value = record.drive_link || "";
    document.getElementById("form-photos").checked = record.photos == 1;
    document.getElementById("form-documents").checked = record.documents == 1;

    onFormTypeChange(record.duration_hours);
  } else {
    document.getElementById("modal-form-title").innerText =
      "Add New Training Record";
    document.getElementById("form-province").value = "Regional Office";
    document.getElementById("form-course-type").value = "ICT Training";
    onFormTypeChange();
    onModalProvinceChange();
  }

  computeBudgetSavings();
  computeDisaggregatedPaxTotal();

  document.getElementById("crud-modal").classList.remove("hidden");
}

function closeFormModal() {
  document.getElementById("crud-modal").classList.add("hidden");
}

function cloneRecord(id) {
  const record = db.find((t) => t.id === id);
  if (!record) return;

  openFormModal(null);
  document.getElementById("modal-form-title").innerText =
    "Clone Training Record";
  document.getElementById("form-title").value =
    record.training_title + " (Copy)";
  document.getElementById("form-code").value = record.course_code;
  document.getElementById("form-description").value = record.course_name || "";
  document.getElementById("form-implementation-mode").value =
    record.implementation_mode || "Face-to-Face";
  document.getElementById("form-course-type").value =
    record.course_type || "Webinar";
  document.getElementById("form-province").value = record.province;
  document.getElementById("form-municipality").value = record.municipality;
  document.getElementById("form-barangay").value = record.barangay;
  document.getElementById("venue").value = record.venue;
  document.getElementById("form-latitude").value = record.latitude;
  document.getElementById("form-longitude").value = record.longitude;
  document.getElementById("form-start-date").value = record.start_date;
  document.getElementById("form-end-date").value = record.end_date;
  document.getElementById("form-course-officer").value = record.course_officer;
  document.getElementById("form-resource-person").value =
    record.resource_person;
  document.getElementById("form-target-participants").value =
    record.target_participants;
  document.getElementById("form-male-participants").value =
    record.male_participants;
  document.getElementById("form-female-participants").value =
    record.female_participants;
  document.getElementById("form-budget-allocated").value =
    record.budget_allocated;
  document.getElementById("form-budget-utilized").value =
    record.budget_utilized;
  document.getElementById("form-status").value = "upcoming";
  document.getElementById("form-drive-link").value = "";
  document.getElementById("form-photos").checked = false;
  document.getElementById("form-documents").checked = false;

  onFormTypeChange(record.duration_hours);
  computeBudgetSavings();
  computeDisaggregatedPaxTotal();
}

function deleteRecord(id) {
  Swal.fire({
    title: "Delete Training Record?",
    text: "This will permanently remove this training from the database.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#EF4444",
    cancelButtonColor: "#6B7280",
    confirmButtonText: "Yes, delete it",
  }).then((result) => {
    if (result.isConfirmed) {
      let formData = new FormData();
      formData.append("id", id);
      fetch("api/training_delete.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            synchronizeDashboardState();
            Swal.fire(
              "Deleted!",
              "Training record removed from the database.",
              "success",
            );
          }
        });
    }
  });
}

//==================================================================
// FORM HELPER CALCULATIONS
//==================================================================

function computeBudgetSavings() {
  const allocated =
    parseFloat(document.getElementById("form-budget-allocated").value) || 0;
  const utilized =
    parseFloat(document.getElementById("form-budget-utilized").value) || 0;
  const saved = Math.max(0, allocated - utilized);
  document.getElementById("form-budget-saved").value = saved.toFixed(2);
}

function computeDisaggregatedPaxTotal() {
  const male =
    parseInt(document.getElementById("form-male-participants").value) || 0;
  const female =
    parseInt(document.getElementById("form-female-participants").value) || 0;
  document.getElementById("form-actual-participants").value = male + female;
}

function onModalProvinceChange() {
  const province = document.getElementById("form-province").value;
  const coords = CARAGA_PROVINCE_COORDINATES[province];
  if (coords) {
    document.getElementById("form-latitude").value = coords.lat;
    document.getElementById("form-longitude").value = coords.lng;
  }
}

function onFormTypeChange(presetDuration) {
  const type = document.getElementById("form-course-type").value;
  const durationSelect = document.getElementById("form-duration");
  const options = type === "ICT Training" ? ["16", "20", "40"] : ["3", "4"];

  durationSelect.innerHTML = options
    .map((h) => `<option value="${h}">${h} Hours</option>`)
    .join("");

  if (presetDuration !== undefined && presetDuration !== null) {
    const numeric = String(presetDuration).replace(/[^0-9]/g, "");
    const match = Array.from(durationSelect.options).find(
      (o) => o.value === numeric,
    );
    if (match) durationSelect.value = numeric;
  }
}

//==================================================================
// CENTRAL PMT DOWNLOADS MODAL (CREATE / EDIT)
//==================================================================

function exportToCSV() {
  if (!db || db.length === 0) {
    Swal.fire("No Data", "There are no training records to export.", "info");
    return;
  }

  const headers = [
    "ID",
    "Training Title",
    "Course Code",
    "Course Type",
    "Duration (Hrs)",
    "Province",
    "Municipality",
    "Barangay",
    "Venue",
    "Start Date",
    "End Date",
    "Course Officer",
    "Resource Person",
    "Target Pax",
    "Male",
    "Female",
    "Actual Pax",
    "Budget Allocated",
    "Budget Utilized",
    "Status",
    "Drive Link",
  ];

  const rows = db.map((t) => [
    t.id,
    t.training_title,
    t.course_code,
    t.course_type || "Webinar",
    t.duration_hours || 3,
    t.province,
    t.municipality,
    t.barangay,
    t.venue,
    t.start_date,
    t.end_date,
    t.course_officer,
    t.resource_person,
    t.target_participants,
    t.male_participants,
    t.female_participants,
    t.actual_participants,
    t.budget_allocated,
    t.budget_utilized,
    t.status,
    t.drive_link || "",
  ]);

  const escapeCsv = (val) => `"${String(val).replace(/"/g, '""')}"`;
  const csvContent = [headers, ...rows]
    .map((row) => row.map(escapeCsv).join(","))
    .join("\r\n");

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `dict-caraga-trainings-${new Date().toISOString().slice(0, 10)}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

//==================================================================
// TRAINING CALENDAR (PDF) GENERATOR
//==================================================================

function generateTrainingCalendarPDF() {
  if (!db || db.length === 0) {
    Swal.fire(
      "No Data",
      "There are no training records to include in the calendar.",
      "info",
    );
    return;
  }

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "landscape", unit: "pt", format: "a4" });
  const pageWidth = doc.internal.pageSize.getWidth();

  // Quarter/year banner, derived from today's date (edit here if you
  // want it tied to a specific reporting period instead)
  const now = new Date();
  const quarterNames = ["FIRST", "SECOND", "THIRD", "FOURTH"];
  const quarterLabel = quarterNames[Math.floor(now.getMonth() / 3)];
  const fyYear = now.getFullYear();

  // Header block
  doc.setFont("times", "bold");
  doc.setFontSize(13);
  doc.text(
    "ICT LITERACY AND COMPETENCY DEVELOPMENT BUREAU",
    pageWidth / 2,
    40,
    { align: "center" },
  );
  doc.text("TRAINING CALENDAR", pageWidth / 2, 58, { align: "center" });
  doc.setFontSize(11);
  doc.text(`${quarterLabel} QUARTER OF FY ${fyYear}`, pageWidth / 2, 76, {
    align: "center",
  });
  doc.text("CARAGA REGION", pageWidth / 2, 92, { align: "center" });

  const rows = db.map((t) => {
    const schedule =
      t.end_date && t.end_date !== t.start_date
        ? `${t.start_date} to\n${t.end_date}`
        : `${t.start_date}`;
    return [
      t.training_title || "",
      `${t.duration_hours || 3} hours\n${(t.implementation_mode || "Face-to-Face").toUpperCase()}`,
      schedule,
      t.course_name || "",
      String(t.target_participants || 0),
      t.resource_person || "",
      Number(t.budget_allocated || 0).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }),
      t.drive_link || "",
    ];
  });

  doc.autoTable({
    startY: 108,
    head: [
      [
        "COURSE",
        "NO. OF HOURS AND\nTRAINING MODALITY",
        "SCHEDULE\n(DATE AND TIME)",
        "COURSE DESCRIPTION",
        "TARGET\nPARTICIPANTS",
        "RESOURCE PERSON /\nILCDB PARTNER",
        "BUDGET\nALLOCATION",
        "ZOOM /\nGMEET LINK",
      ],
    ],
    body: rows,
    theme: "grid",
    styles: {
      font: "times",
      fontSize: 8,
      cellPadding: 5,
      valign: "middle",
      halign: "center",
      lineColor: [0, 0, 0],
      lineWidth: 0.5,
    },
    headStyles: {
      fillColor: [155, 197, 235],
      textColor: [0, 0, 0],
      fontStyle: "bold",
    },
    columnStyles: {
      0: { halign: "left", cellWidth: 95 },
      3: { halign: "left", cellWidth: 190 },
      7: { cellWidth: 75 },
    },
  });

  // Footer notes + signature blocks, placed below wherever the table
  // ended. If there isn't enough room left on the page, start a
  // fresh page instead of letting it run off the bottom.
  const pageHeight = doc.internal.pageSize.getHeight();
  const tableEndY = doc.lastAutoTable.finalY || 108;
  const footerHeight = 190; // rough space the notes + signatures block needs
  let footerY = tableEndY + 28;
  if (footerY + footerHeight > pageHeight - 30) {
    doc.addPage();
    footerY = 50;
  }

  doc.setFont("times", "italic");
  doc.setFontSize(9);
  doc.text("Notes:", 40, footerY);
  doc.text(
    "•  Training schedules may be adjusted as necessary based on operational requirements and participant availability.",
    55,
    footerY + 16,
  );
  doc.text(
    "•  All concerned personnel will be informed accordingly of any changes.",
    55,
    footerY + 32,
  );

  // Contact line mixes italic + bold-italic ("[email address]" is bold),
  // so it's drawn as two adjacent text segments rather than one string.
  const contactPrefix =
    "•  For inquiries, coordination, and confirmation, please contact: ";
  doc.setFont("times", "italic");
  doc.text(contactPrefix, 55, footerY + 48);
  const prefixWidth = doc.getTextWidth(contactPrefix);
  doc.setFont("times", "bolditalic");
  doc.text("[email address]", 55 + prefixWidth, footerY + 48);

  // Signature block: two columns, "Recommending Approval" (TOD Chief)
  // on the left and "Approved By" (Regional Director) on the right
  const sigLabelY = footerY + 90;
  const leftX = 40;
  const rightX = pageWidth / 2 + 20;

  doc.setFont("times", "normal");
  doc.setFontSize(10);
  doc.text("Recommending Approval:", leftX, sigLabelY);
  doc.text("Approved By:", rightX, sigLabelY);

  const lineY = sigLabelY + 40;
  doc.setLineWidth(1);
  doc.line(leftX, lineY, leftX + 180, lineY);
  doc.line(rightX, lineY, rightX + 180, lineY);

  doc.text("TOD Chief", leftX, lineY + 14);
  doc.text("Regional Director", rightX, lineY + 14);

  doc.save(
    `dict-caraga-training-calendar-${quarterLabel.toLowerCase()}-quarter-fy${fyYear}.pdf`,
  );
}
