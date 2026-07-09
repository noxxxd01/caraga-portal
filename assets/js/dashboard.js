/**
 * Executive Dashboard: pulling data from the API and rendering
 * the summary cards, provincial ledger, and simulated API panel.
 */

// Pull Real Data from PHP Server side PDO state
function synchronizeDashboardState() {
  // Fetch trainings/allocations AND the PMT Downloads Registry together,
  // since the dashboard's target baseline now depends on the registry.
  Promise.all([
    fetch('api/dashboard_data.php', { cache: 'no-store' }).then((r) =>
      r.json(),
    ),
    fetch('api/downloads_get.php', { cache: 'no-store' }).then((r) => r.json()),
  ])
    .then(([dashRes, downloadsRes]) => {
      if (dashRes.status === 'success') {
        // Rebuild allocations dictionary (still used by the provincial ledger)
        officeAllocations = {};
        dashRes.office_allocations.forEach((alloc) => {
          officeAllocations[alloc.office_name] = {
            target: parseInt(alloc.target),
            budget: parseFloat(alloc.budget),
          };
        });

        db = dashRes.trainings;

        // NEW: keep pmtDownloads in sync before computing the baseline target
        if (downloadsRes.status === 'success') {
          pmtDownloads = downloadsRes.downloads;
        }

        // Recalculate PMT baseline numbers (now sourced from PMT Downloads Registry)
        calculateBaselineTotals();

        // Master calculations
        const target = globalTarget;
        const conducted = db.filter((t) => t.status === 'completed').length;
        const remaining = Math.max(0, target - conducted);

        // Consolidated Budget now comes from the PMT Downloads Registry
        // (Total Budget Allocation = target_trainings × unit_budget, summed across all downloads)
        let totalAllocated = pmtDownloads.reduce(
          (sum, dl) =>
            sum +
            parseFloat(dl.target_trainings || 0) *
              parseFloat(dl.unit_budget || 0),
          0,
        );
        // Budget Utilized still comes from the Training Database Tracker (unchanged)
        let totalUtilized = db.reduce(
          (sum, t) => sum + parseFloat(t.budget_utilized || 0),
          0,
        );
        // Remaining Balance = Consolidated Budget − Budget Utilized (unchanged formula)
        const remainingBudget = totalAllocated - totalUtilized;

        const accomplishmentRate =
          target > 0 ? parseFloat(((conducted / target) * 100).toFixed(1)) : 0;
        const budgetUtilizedRate =
          totalAllocated > 0
            ? parseFloat(((totalUtilized / totalAllocated) * 100).toFixed(1))
            : 0;

        // Render top KPI metrics
        document.getElementById('card-target-count').innerText = target;
        document.getElementById('card-conducted-count').innerText = conducted;
        document.getElementById('card-remaining-count').innerText = remaining;
        document.getElementById('card-budget-allocated').innerText =
          formatCurrency(totalAllocated);
        document.getElementById('card-budget-utilized').innerText =
          formatCurrency(totalUtilized);
        document.getElementById('card-budget-remaining').innerText =
          formatCurrency(remainingBudget);

        // Progress bar mapping
        document.getElementById('rate-badge').innerText =
          accomplishmentRate + '%';
        document.getElementById('radial-gauge-text').innerText =
          Math.round(accomplishmentRate) + '%';

        const progressBar = document.getElementById('horizontal-progress-bar');
        progressBar.style.width = Math.min(100, accomplishmentRate) + '%';
        progressBar.className =
          'h-full rounded-full transition-all duration-500 ';

        let colorHex = '#10B981';
        if (accomplishmentRate < 50) {
          colorHex = '#EF4444';
          progressBar.classList.add('bg-red-500');
        } else if (accomplishmentRate < 75) {
          colorHex = '#F59E0B';
          progressBar.classList.add('bg-amber-500');
        } else {
          progressBar.classList.add('bg-emerald-500');
        }

        // Burn percentage
        document.getElementById('card-budget-utilized-pct').innerText =
          budgetUtilizedRate + '%';
        const budgetProgressBar = document.getElementById(
          'budget-utilized-progress-bar',
        );
        if (budgetProgressBar) {
          budgetProgressBar.style.width =
            Math.min(100, budgetUtilizedRate) + '%';
        }

        // Redraw doughnut
        doughnutChartInstance.data.datasets[0].data = [
          accomplishmentRate,
          Math.max(0, 100 - accomplishmentRate),
        ];
        doughnutChartInstance.data.datasets[0].backgroundColor = [
          colorHex,
          '#E2E8F0',
        ];
        doughnutChartInstance.update();

        // Filter variables mapping
        const provinceFilter =
          document.getElementById('map-province').value ||
          document.getElementById('tracker-province-select').value;
        const statusFilter =
          document.getElementById('map-status').value ||
          document.getElementById('tracker-status-select').value;
        const searchKeyword = (
          document.getElementById('map-search-input').value ||
          document.getElementById('tracker-search-input').value ||
          ''
        ).toLowerCase();

        const filteredData = db.filter((t) => {
          const matchesProvince =
            provinceFilter === '' || t.province === provinceFilter;
          const matchesStatus =
            statusFilter === '' || t.status === statusFilter;
          const matchesKeyword =
            searchKeyword === '' ||
            t.training_title.toLowerCase().includes(searchKeyword) ||
            t.course_code.toLowerCase().includes(searchKeyword) ||
            t.venue.toLowerCase().includes(searchKeyword) ||
            t.course_officer.toLowerCase().includes(searchKeyword);
          return matchesProvince && matchesStatus && matchesKeyword;
        });

        // Refresh Map, Table, and charts
        refreshGISMapMarkers(filteredData);
        populateTrackerTable(filteredData);
        recalculateFinancialBarChart(
          totalAllocated,
          totalUtilized,
          remainingBudget,
          budgetUtilizedRate,
        );

        // Draw course metrics and provincial ledgers cards
        rebuildPMTWidgets();

        // Re-render the Downloads Registry tab itself (table rows + its own summary deck)
        rebuildDownloadsRegistry();

        // Rebuild API sim
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
      console.error('AJAX Fetch error:', err);
    });
}

// Dynamically compute the baseline target from the PMT Downloads Registry
// (sum of "Training Target Volume" / target_trainings across all registered downloads)
function calculateBaselineTotals() {
  globalTarget = pmtDownloads.reduce(
    (sum, dl) => sum + (parseInt(dl.target_trainings) || 0),
    0,
  );
  document.getElementById('annual-target-display').innerText = globalTarget;
}

// Leaflet GIS Mapping initialization

function rebuildPMTWidgets() {
  // Course Type Classifications
  const ictList = db.filter((t) => t.course_type === 'ICT Training');
  const webinarList = db.filter(
    (t) => t.course_type === 'Webinar' || !t.course_type,
  );

  const ictCompleted = ictList.filter((t) => t.status === 'completed').length;
  const webinarCompleted = webinarList.filter(
    (t) => t.status === 'completed',
  ).length;

  // Target split ratios calculated from global pool allocations
  let ictTarget = 0;
  let webinarTarget = 0;
  pmtDownloads.forEach((dl) => {
    const dlTarget = parseInt(dl.target_trainings) || 0;
    if (dl.course_type === 'ICT Training') {
      ictTarget += dlTarget;
    } else if (dl.course_type === 'Webinar') {
      webinarTarget += dlTarget;
    } else {
      ictTarget += dlTarget / 2;
      webinarTarget += dlTarget / 2;
    }
  });
  ictTarget = Math.round(ictTarget);
  webinarTarget = Math.round(webinarTarget);

  document.getElementById('ict-overall-badge').innerText =
    `${ictCompleted} / ${ictTarget}`;
  document.getElementById('ict-target-val').innerText = ictTarget;
  document.getElementById('ict-accomplished-val').innerText = ictCompleted;
  document.getElementById('ict-remaining-val').innerText = Math.max(
    0,
    ictTarget - ictCompleted,
  );

  document.getElementById('webinar-overall-badge').innerText =
    `${webinarCompleted} / ${webinarTarget}`;
  document.getElementById('webinar-target-val').innerText = webinarTarget;
  document.getElementById('webinar-accomplished-val').innerText =
    webinarCompleted;
  document.getElementById('webinar-remaining-val').innerText = Math.max(
    0,
    webinarTarget - webinarCompleted,
  );

  // REBUILT PROVINCIAL PERFORMANCE LEDGER MATRIX Table
  const provTbody = document.getElementById('provincial-dashboard-tbody');
  provTbody.innerHTML = '';

  // Region-wide budget figures (same source as the top KPI cards)
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
  // "Remaining budget per single unit of target" — used to proportionally
  // spread the region-wide remaining balance across provincial units.
  const perTargetRemainingBudget =
    globalTarget > 0 ? remainingBudgetAll / globalTarget : 0;

  // Fixed, deterministic province ordering (same order they were seeded in)
  const officeOrder = Object.keys(CARAGA_PROVINCE_COORDINATES).filter(
    (name) => officeAllocations[name],
  );
  const officeCount = officeOrder.length;

  // Evenly distribute the Total Target Baseline (globalTarget, sourced
  // from the PMT Downloads Registry's Training Target Volume) across
  // every Operating Provincial Unit. Any remainder that doesn't divide
  // evenly is handed out one at a time to the first units in the list
  // (e.g. Target Volume 5 across 7 units -> first 5 units get 1, rest get 0).
  const baseShare =
    officeCount > 0 ? Math.floor(globalTarget / officeCount) : 0;
  const remainderShare = officeCount > 0 ? globalTarget % officeCount : 0;

  officeOrder.forEach((officeName, index) => {
    const officeTarget = baseShare + (index < remainderShare ? 1 : 0);

    // Training Accomplished: still pulled live from the Training
    // Database Tracker, counting completed records for this office
    const officeTrainings = db.filter((t) => t.province === officeName);
    const completedCount = officeTrainings.filter(
      (t) => t.status === 'completed',
    ).length;
    const remainingCount = Math.max(0, officeTarget - completedCount);

    // Total Remaining Budget = region-wide remaining budget "per target
    // unit" x this office's own share of the target
    const officeRemainingBudget = Math.max(
      0,
      perTargetRemainingBudget * officeTarget,
    );

    const tr = document.createElement('tr');
    tr.className = 'hover:bg-slate-50 transition-colors';
    tr.innerHTML = `
                    <td class="py-3 font-semibold text-slate-800 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-dict-bright"></span>
                        ${officeName}
                    </td>
                    <td class="py-2 text-center font-bold text-slate-800">
                        ${officeTarget}
                    </td>
                    <td class="py-3 text-center font-extrabold text-emerald-600">${completedCount}</td>
                    <td class="py-3 text-center font-semibold ${remainingCount > 0 ? 'text-amber-500' : 'text-slate-400'}">${remainingCount}</td>
                    <td class="py-3 text-right font-bold text-slate-900 whitespace-nowrap">
                        <div>${formatCurrency(officeRemainingBudget)}</div>
                        <div class="text-[9px] text-slate-400 font-medium">Target Share: ${officeTarget} of ${globalTarget}</div>
                    </td>
                `;
    provTbody.appendChild(tr);
  });
}

// Inline configuration updater for Target Allocations

function updatePMTAllocation(officeName, field, value) {
  const numVal = parseFloat(value) || 0;

  let formData = new FormData();
  formData.append('office_name', officeName);
  formData.append('field', field);
  formData.append('value', numVal);

  fetch('api/allocation_update.php', {
    method: 'POST',
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === 'success') {
        synchronizeDashboardState();
        Swal.fire({
          title: 'Framework Reallocated',
          text: `${officeName} ${field} parameters adjusted dynamically in SQL database.`,
          icon: 'success',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2000,
        });
      }
    });
}

// Refresh Map pins

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
    status: 'success',
    timestamp: new Date().toISOString(),
    framework_versions: { laravel: '12.0.2', php: '8.3.6' },
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
      course_type: t.course_type || 'Webinar',
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
      document_drive_link: t.drive_link || '',
      status: t.status,
    })),
  };
  document.getElementById('api-output-payload').textContent = JSON.stringify(
    apiOutput,
    null,
    4,
  );
}

// Rebuild Central PMT Downloads Tab Table & Aggregate Cards
