/**
 * Executive Dashboard: pulling data from the API and rendering
 * the summary cards, provincial ledger, and simulated API panel.
 */

function synchronizeDashboardState() {
  Promise.all([
    fetch("api/dashboard_data.php", { cache: "no-store" }).then((r) =>
      r.json(),
    ),
    fetch("api/downloads_get.php", { cache: "no-store" }).then((r) => r.json()),
  ])
    .then(([dashRes, downloadsRes]) => {
      if (dashRes.status === "success") {
        officeAllocations = {};
        dashRes.office_allocations.forEach((alloc) => {
          officeAllocations[alloc.office_name] = {
            target: parseInt(alloc.target),
            budget: parseFloat(alloc.budget),
          };
        });

        db = dashRes.trainings;

        if (downloadsRes.status === "success") {
          pmtDownloads = downloadsRes.downloads;
        }

        calculateBaselineTotals();

        const target = globalTarget;
        const conducted = db.filter((t) => t.status === "completed").length;
        const remaining = Math.max(0, target - conducted);

        let totalAllocated = pmtDownloads.reduce(
          (sum, dl) =>
            sum +
            parseFloat(dl.target_trainings || 0) *
              parseFloat(dl.unit_budget || 0),
          0,
        );
        let totalUtilized = db.reduce(
          (sum, t) => sum + parseFloat(t.budget_utilized || 0),
          0,
        );
        const remainingBudget = totalAllocated - totalUtilized;

        const accomplishmentRate =
          target > 0 ? parseFloat(((conducted / target) * 100).toFixed(1)) : 0;
        const budgetUtilizedRate =
          totalAllocated > 0
            ? parseFloat(((totalUtilized / totalAllocated) * 100).toFixed(1))
            : 0;

        const provinceFilter =
          document.getElementById("map-province").value ||
          document.getElementById("tracker-province-select").value;
        const statusFilter =
          document.getElementById("map-status").value ||
          document.getElementById("tracker-status-select").value;
        const searchKeyword = (
          document.getElementById("map-search-input").value ||
          document.getElementById("tracker-search-input").value ||
          ""
        ).toLowerCase();

        const filteredData = db.filter((t) => {
          const matchesProvince =
            provinceFilter === "" || t.province === provinceFilter;
          const matchesStatus =
            statusFilter === "" || t.status === statusFilter;
          const matchesKeyword =
            searchKeyword === "" ||
            t.training_title.toLowerCase().includes(searchKeyword) ||
            t.course_code.toLowerCase().includes(searchKeyword) ||
            t.venue.toLowerCase().includes(searchKeyword) ||
            t.course_officer.toLowerCase().includes(searchKeyword);
          return matchesProvince && matchesStatus && matchesKeyword;
        });

        refreshGISMapMarkers(filteredData);
        populateTrackerTable(filteredData);
        recalculateFinancialBarChart(
          totalAllocated,
          totalUtilized,
          remainingBudget,
          budgetUtilizedRate,
        );

        // Draw course metrics, provincial ledger, financial summary, and the chart
        rebuildPMTWidgets();

        // Re-render the Downloads Registry tab itself
        rebuildDownloadsRegistry();

        rebuildSimulatedAPIOutput(
          filteredData,
          target,
          conducted,
          remaining,
          totalAllocated,
          totalUtilized,
          remainingBudget,
          accomplishmentRate,
          budgetUtilizedRate,
        );
      }
    })
    .catch((err) => {
      console.error("AJAX Fetch error:", err);
    });
}

function calculateBaselineTotals() {
  globalTarget = pmtDownloads.reduce(
    (sum, dl) => sum + (parseInt(dl.target_trainings) || 0),
    0,
  );
  document.getElementById("annual-target-display").innerText = globalTarget;
}

// This is the ONLY rebuildPMTWidgets() in the whole app now — it belongs
// here in dashboard.js, matching the new tab_dashboard.php layout
// (ict-total-badge, provincial-summary-tbody, provincialOfficeChart,
// Financial Expenditure Summary cards).
function rebuildPMTWidgets() {
  let ictTotal = 0,
    ict16 = 0,
    ict20 = 0,
    ict40 = 0;
  let webTotal = 0,
    web2 = 0,
    web3 = 0,
    web4 = 0;

  pmtDownloads.forEach((dl) => {
    const dlTarget = parseInt(dl.target_trainings) || 0;
    const durationNum =
      parseInt((dl.duration_hours || "").replace(/[^0-9]/g, "")) || 0;

    if (dl.course_type === "ICT Training") {
      ictTotal += dlTarget;
      if (durationNum === 16) ict16 += dlTarget;
      else if (durationNum === 20) ict20 += dlTarget;
      else if (durationNum === 40) ict40 += dlTarget;
    } else if (dl.course_type === "Webinar") {
      webTotal += dlTarget;
      if (durationNum === 2) web2 += dlTarget;
      else if (durationNum === 3) web3 += dlTarget;
      else if (durationNum === 4) web4 += dlTarget;
    }
  });

  document.getElementById("ict-total-badge").innerText = `${ictTotal} Total`;
  document.getElementById("ict-16h-val").innerText = ict16;
  document.getElementById("ict-20h-val").innerText = ict20;
  document.getElementById("ict-40h-val").innerText = ict40;

  document.getElementById("webinar-total-badge").innerText =
    `${webTotal} Total`;
  document.getElementById("web-2h-val").innerText = web2;
  document.getElementById("web-3h-val").innerText = web3;
  document.getElementById("web-4h-val").innerText = web4;

  const totalAllocated = pmtDownloads.reduce(
    (sum, dl) =>
      sum +
      parseFloat(dl.target_trainings || 0) * parseFloat(dl.unit_budget || 0),
    0,
  );
  const totalUtilizedAll = db.reduce(
    (sum, t) => sum + parseFloat(t.budget_utilized || 0),
    0,
  );
  const remainingBudgetAll = totalAllocated - totalUtilizedAll;

  document.getElementById("card-budget-allocated").innerText =
    formatCurrency(totalAllocated);
  document.getElementById("card-budget-utilized").innerText =
    formatCurrency(totalUtilizedAll);
  document.getElementById("card-budget-remaining").innerText =
    formatCurrency(remainingBudgetAll);

  const officeOrder = Object.keys(CARAGA_PROVINCE_COORDINATES).filter(
    (name) => officeAllocations[name],
  );
  const officeCount = officeOrder.length;
  const baseShare =
    officeCount > 0 ? Math.floor(globalTarget / officeCount) : 0;
  const remainderShare = officeCount > 0 ? globalTarget % officeCount : 0;

  const summaryTbody = document.getElementById("provincial-summary-tbody");
  summaryTbody.innerHTML = "";

  const chartLabels = [],
    chartTargets = [],
    chartAccomplished = [],
    chartRemaining = [];

  officeOrder.forEach((officeName, index) => {
    const officeTarget = baseShare + (index < remainderShare ? 1 : 0);
    const officeTrainings = db.filter((t) => t.province === officeName);
    const completedCount = officeTrainings.filter(
      (t) => t.status === "completed",
    ).length;
    const remainingCount = Math.max(0, officeTarget - completedCount);

    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td class="py-2 font-semibold text-slate-800">${officeName}</td>
            <td class="py-2 text-center font-bold text-slate-800">${officeTarget}</td>
            <td class="py-2 text-center font-extrabold text-emerald-600">${completedCount}</td>
            <td class="py-2 text-center font-semibold ${remainingCount > 0 ? "text-amber-500" : "text-slate-400"}">${remainingCount}</td>
        `;
    summaryTbody.appendChild(tr);

    chartLabels.push(officeName);
    chartTargets.push(officeTarget);
    chartAccomplished.push(completedCount);
    chartRemaining.push(remainingCount);
  });

  recalculateProvincialChart(
    chartLabels,
    chartTargets,
    chartAccomplished,
    chartRemaining,
  );
}

function updatePMTAllocation(officeName, field, value) {
  const numVal = parseFloat(value) || 0;
  let formData = new FormData();
  formData.append("office_name", officeName);
  formData.append("field", field);
  formData.append("value", numVal);

  fetch("api/allocation_update.php", { method: "POST", body: formData })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        synchronizeDashboardState();
        Swal.fire({
          title: "Framework Reallocated",
          text: `${officeName} ${field} parameters adjusted dynamically in SQL database.`,
          icon: "success",
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 2000,
        });
      }
    });
}

function rebuildSimulatedAPIOutput(
  trainings,
  target,
  conducted,
  remaining,
  totalAllocated,
  totalUtilized,
  remainingBudget,
  accomplishmentRate,
  budgetUtilizedRate,
) {
  const apiOutput = {
    status: "success",
    timestamp: new Date().toISOString(),
    framework_versions: { laravel: "12.0.2", php: "8.3.6" },
    kpis: {
      consolidated_regional_target: target,
      completed_trainings: conducted,
      remaining_projects: remaining,
      overall_budget_allotted: totalAllocated,
      overall_budget_utilized: totalUtilized,
      remaining_fund_balance: remainingBudget,
      regional_accomplishment_pct: accomplishmentRate,
      overall_burn_pct: budgetUtilizedRate,
    },
    operating_units_allocation_ledger: officeAllocations,
    trainings: trainings.map((t) => ({
      id: t.id,
      training_title: t.training_title,
      course_code: t.course_code,
      course_type: t.course_type || "Webinar",
      duration_hours: t.duration_hours || 3,
      location: {
        province_or_office: t.province,
        municipality: t.municipality,
        venue: t.venue,
        coordinates: { latitude: t.latitude, longitude: t.longitude },
      },
      participants_profile: {
        target: t.target_participants,
        registered_males: t.male_participants,
        registered_females: t.female_participants,
        actual_total: t.actual_participants,
      },
      budget: {
        allocated: t.budget_allocated,
        utilized: t.budget_utilized,
        saved: Math.max(0, t.budget_allocated - t.budget_utilized),
      },
      document_drive_link: t.drive_link || "",
      status: t.status,
    })),
  };
  document.getElementById("api-output-payload").textContent = JSON.stringify(
    apiOutput,
    null,
    4,
  );
}
