/**
 * Chart.js: doughnut (accomplishment) + bar (financial variance) charts.
 */

function recalculateFinancialBarChart(
  totalAllocated,
  totalUtilized,
  remainingBudget,
  budgetUtilizedRate,
) {
  if (!barChartInstance) return;

  const officesList = Object.keys(officeAllocations);
  const allocatedData = [];
  const utilizedData = [];

  officesList.forEach((office) => {
    const pmtAlloc = officeAllocations[office];
    const utilizedSum = db
      .filter((t) => t.province === office)
      .reduce((sum, t) => sum + parseFloat(t.budget_utilized || 0), 0);

    allocatedData.push(pmtAlloc.budget);
    utilizedData.push(utilizedSum);
  });

  barChartInstance.data.labels = officesList;
  barChartInstance.data.datasets[0].data = allocatedData;
  barChartInstance.data.datasets[1].data = utilizedData;
  barChartInstance.update();

  const finAllocatedEl = document.getElementById("fin-allocated-val");
  const finUtilizedEl = document.getElementById("fin-utilized-val");
  const finRemainingEl = document.getElementById("fin-remaining-val");
  const finPctEl = document.getElementById("fin-utilized-pct-val");
  const finUtilBar = document.getElementById("fin-utilized-progress-bar");

  if (finAllocatedEl) finAllocatedEl.innerText = formatCurrency(totalAllocated);
  if (finUtilizedEl) finUtilizedEl.innerText = formatCurrency(totalUtilized);
  if (finRemainingEl)
    finRemainingEl.innerText = formatCurrency(remainingBudget);
  if (finPctEl) finPctEl.innerText = budgetUtilizedRate + "%";
  if (finUtilBar)
    finUtilBar.style.width = Math.min(100, budgetUtilizedRate) + "%";
}

// REST API simulator

function initializeCharts() {
  const barCtx = document.getElementById("financialVarianceChart");
  if (!barCtx) return;
  barChartInstance = new Chart(barCtx.getContext("2d"), {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          label: "Allocated",
          data: [],
          backgroundColor: "#1E40AF",
          borderRadius: 6,
        },
        {
          label: "Utilized",
          data: [],
          backgroundColor: "#8B5CF6",
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "top",
          labels: { boxWidth: 10, font: { size: 10 } },
        },
      },
      scales: {
        x: { ticks: { font: { size: 9 } } },
        y: {
          beginAtZero: true,
          ticks: {
            font: { size: 9 },
            callback: function (value) {
              return "₱" + value.toLocaleString();
            },
          },
        },
      },
    },
  });
}

function initializeProvincialChart() {
  const ctx = document.getElementById("provincialOfficeChart");
  if (!ctx) return;
  provincialChartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          label: "Target",
          data: [],
          backgroundColor: "#3B82F6",
          borderRadius: 4,
        },
        {
          label: "Accomplished",
          data: [],
          backgroundColor: "#10B981",
          borderRadius: 4,
        },
        {
          label: "Remaining",
          data: [],
          backgroundColor: "#F59E0B",
          borderRadius: 4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "top",
          labels: { font: { size: 10 }, boxWidth: 10 },
        },
      },
      scales: {
        x: { ticks: { font: { size: 9 } }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { font: { size: 9 }, precision: 0 } },
      },
    },
  });
}

function recalculateProvincialChart(labels, targets, accomplished, remaining) {
  if (!provincialChartInstance) return;
  provincialChartInstance.data.labels = labels;
  provincialChartInstance.data.datasets[0].data = targets;
  provincialChartInstance.data.datasets[1].data = accomplished;
  provincialChartInstance.data.datasets[2].data = remaining;
  provincialChartInstance.update();
}

function initializeParticipantsChart() {
  const ctx = document.getElementById("participantsProvinceChart");
  if (!ctx) return;
  participantsChartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          label: "Male",
          data: [],
          backgroundColor: "#3B82F6",
          borderRadius: 4,
        },
        {
          label: "Female",
          data: [],
          backgroundColor: "#EC4899",
          borderRadius: 4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "top",
          labels: { font: { size: 10 }, boxWidth: 10 },
        },
      },
      scales: {
        x: { ticks: { font: { size: 9 } }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { font: { size: 9 }, precision: 0 } },
      },
    },
  });
}

function recalcParticipantsChart(labels, maleData, femaleData) {
  if (!participantsChartInstance) return;
  participantsChartInstance.data.labels = labels;
  participantsChartInstance.data.datasets[0].data = maleData;
  participantsChartInstance.data.datasets[1].data = femaleData;
  participantsChartInstance.update();
}
