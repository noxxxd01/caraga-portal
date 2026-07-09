/**
 * PMT Downloads Registry tab: list, save, delete Central Office downloads,
 * and provincial baseline reset.
 */

function rebuildDownloadsRegistry() {
  fetch('api/downloads_get.php', { cache: 'no-store' })
    .then((res) => res.json())
    .then((res) => {
      console.log('downloads_get.php response:', res); // TEMP debug line — remove once fixed

      if (res.status === 'success') {
        pmtDownloads = res.downloads;

        const ledgerSelect = document.getElementById('download-ledger-select');
        if (ledgerSelect) {
          const previousSelection = ledgerSelect.value;
          ledgerSelect.innerHTML =
            '<option value="">Select a registered download...</option>' +
            pmtDownloads
              .map((dl) => `<option value="${dl.id}">${dl.title}</option>`)
              .join('');
          if (pmtDownloads.some((dl) => dl.id === previousSelection)) {
            ledgerSelect.value = previousSelection;
          }
        }

        const tableBody = document.getElementById('downloads-table-body');
        const emptyState = document.getElementById('downloads-empty-state');
        const summaryDeck = document.getElementById('downloads-summary-deck');
        tableBody.innerHTML = '';

        if (pmtDownloads.length === 0) {
          emptyState.classList.remove('hidden');
          summaryDeck.innerHTML = '';
          const ledgerContainer = document.getElementById(
            'download-ledger-container',
          );
          if (ledgerContainer) ledgerContainer.classList.add('hidden');
          return;
        } else {
          emptyState.classList.add('hidden');
        }

        let dlTargetSum = 0;
        let dlBudgetSum = 0;

        pmtDownloads.forEach((dl) => {
          const rowTotalBudget = dl.target_trainings * dl.unit_budget;
          dlTargetSum += parseInt(dl.target_trainings || 0);
          dlBudgetSum += parseFloat(rowTotalBudget || 0);

          const tr = document.createElement('tr');
          tr.className = 'hover:bg-slate-50 transition-colors';
          tr.innerHTML = `
                                <td class="p-3 align-top font-bold text-slate-900">${dl.title}</td>
                                <td class="p-3 align-top">
                                    <span class="bg-blue-50 text-blue-800 font-extrabold px-2 py-0.5 rounded text-[10px]">${dl.course_type || 'Mixed Allocations'}</span>
                                </td>
                                <td class="p-3 align-top font-medium text-slate-600">${dl.duration_hours || 'Full Catalog'}</td>
                                <td class="p-3 align-top text-center font-extrabold text-blue-600">${dl.target_trainings}</td>
                                <td class="p-3 align-top text-right font-semibold">${formatCurrency(dl.unit_budget)}</td>
                                <td class="p-3 align-top text-right font-black text-slate-800">${formatCurrency(rowTotalBudget)}</td>
                                <td class="p-3 align-top font-mono font-bold text-purple-600">${dl.subaro_code}</td>
                                <td class="p-3 align-top font-mono font-bold text-slate-500">${dl.uacs_code || '5020201000'}</td>
                                <td class="p-3 align-top text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick="openDownloadModal('${dl.id}')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded" title="Edit Properties">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <button onclick="deleteDownload('${dl.id}')" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded" title="Wipe Registry">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            `;
          tableBody.appendChild(tr);
        });

        summaryDeck.innerHTML = `
                            <div class="bg-amber-50/50 border border-amber-100 p-4 rounded-xl">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Central Allocation Schemes</span>
                                <div class="text-2xl font-black text-slate-800 mt-1">${pmtDownloads.length} Registered</div>
                            </div>
                            <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-xl">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Central Trainings Base Target</span>
                                <div class="text-2xl font-black text-dict-bright mt-1">${dlTargetSum} Trainings</div>
                            </div>
                            <div class="bg-emerald-50/50 border border-emerald-100 p-4 rounded-xl">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Consolidated Fund allocation</span>
                                <div class="text-2xl font-black text-emerald-600 mt-1">${formatCurrency(dlBudgetSum)}</div>
                            </div>
                            <div class="bg-purple-50/50 border border-purple-100 p-4 rounded-xl">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Central Avg Allotted per Unit</span>
                                <div class="text-2xl font-black text-purple-700 mt-1">${formatCurrency(pmtDownloads.length > 0 ? dlBudgetSum / dlTargetSum : 0)}</div>
                            </div>
                        `;
      } else {
        // This branch was completely missing before — an API-level error
        // (bad SQL, missing table, etc.) was failing silently with zero feedback.
        console.error('downloads_get.php returned an error:', res.message);
        Swal.fire(
          'Load Failed',
          res.message || 'Could not load the PMT Downloads Registry.',
          'error',
        );
      }
    })
    .catch((err) => {
      // This catch was also completely missing — a network failure, a 500
      // error, or malformed (non-JSON) PHP output would all die silently here.
      console.error('Failed to fetch downloads_get.php:', err);
      Swal.fire(
        'Load Failed',
        'Could not reach the server to load the Downloads Registry.',
        'error',
      );
    });
}

function rebuildPMTWidgets() {
  // ICT / Webinar per-duration target counts now come from the PMT
  // Downloads Registry (target_trainings summed per duration bucket),
  // not from the Training Tracker.
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
      parseInt((dl.duration_hours || '').replace(/[^0-9]/g, '')) || 0;

    if (dl.course_type === 'ICT Training') {
      ictTotal += dlTarget;
      if (durationNum === 16) ict16 += dlTarget;
      else if (durationNum === 20) ict20 += dlTarget;
      else if (durationNum === 40) ict40 += dlTarget;
    } else if (dl.course_type === 'Webinar') {
      webTotal += dlTarget;
      if (durationNum === 2) web2 += dlTarget;
      else if (durationNum === 3) web3 += dlTarget;
      else if (durationNum === 4) web4 += dlTarget;
    }
  });

  document.getElementById('ict-total-badge').innerText = `${ictTotal} Total`;
  document.getElementById('ict-16h-val').innerText = ict16;
  document.getElementById('ict-20h-val').innerText = ict20;
  document.getElementById('ict-40h-val').innerText = ict40;

  document.getElementById('webinar-total-badge').innerText =
    `${webTotal} Total`;
  document.getElementById('web-2h-val').innerText = web2;
  document.getElementById('web-3h-val').innerText = web3;
  document.getElementById('web-4h-val').innerText = web4;

  // Region-wide budget figures (same source as everywhere else)
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
  const perTargetRemainingBudget =
    globalTarget > 0 ? remainingBudgetAll / globalTarget : 0;

  // Financial Expenditure Summary column (now the only place these render)
  document.getElementById('card-budget-allocated').innerText =
    formatCurrency(totalAllocated);
  document.getElementById('card-budget-utilized').innerText =
    formatCurrency(totalUtilizedAll);
  document.getElementById('card-budget-remaining').innerText =
    formatCurrency(remainingBudgetAll);

  // Provinces Allocation & Remaining Targets Ledger (condensed table, no budget column)
  const officeOrder = Object.keys(CARAGA_PROVINCE_COORDINATES).filter(
    (name) => officeAllocations[name],
  );
  const officeCount = officeOrder.length;
  const baseShare =
    officeCount > 0 ? Math.floor(globalTarget / officeCount) : 0;
  const remainderShare = officeCount > 0 ? globalTarget % officeCount : 0;

  const summaryTbody = document.getElementById('provincial-summary-tbody');
  summaryTbody.innerHTML = '';

  const chartLabels = [],
    chartTargets = [],
    chartAccomplished = [],
    chartRemaining = [];

  officeOrder.forEach((officeName, index) => {
    const officeTarget = baseShare + (index < remainderShare ? 1 : 0);
    const officeTrainings = db.filter((t) => t.province === officeName);
    const completedCount = officeTrainings.filter(
      (t) => t.status === 'completed',
    ).length;
    const remainingCount = Math.max(0, officeTarget - completedCount);

    const tr = document.createElement('tr');
    tr.innerHTML = `
            <td class="py-2 font-semibold text-slate-800">${officeName}</td>
            <td class="py-2 text-center font-bold text-slate-800">${officeTarget}</td>
            <td class="py-2 text-center font-extrabold text-emerald-600">${completedCount}</td>
            <td class="py-2 text-center font-semibold ${remainingCount > 0 ? 'text-amber-500' : 'text-slate-400'}">${remainingCount}</td>
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

// Reallocates and registers Central download row

function handleDownloadSubmit(event) {
  event.preventDefault();
  const id = document.getElementById('download-index').value;

  let formData = new FormData();
  formData.append('id', id);
  formData.append('title', document.getElementById('dl-title').value);
  formData.append(
    'target_trainings',
    document.getElementById('dl-target').value,
  );
  formData.append(
    'unit_budget',
    document.getElementById('dl-budget-per').value,
  );
  formData.append('subaro_code', document.getElementById('dl-subaro').value);
  formData.append('uacs_code', document.getElementById('dl-uacs').value);
  formData.append('course_type', document.getElementById('dl-type').value);
  formData.append(
    'duration_hours',
    document.getElementById('dl-duration').value,
  );
  // The reference-document link field was dropped from this modal;
  // send an empty string so the (still NOT NULL) drive_link column
  // in pmt_downloads doesn't reject the insert.
  formData.append('drive_link', '');

  fetch('api/downloads_save.php', {
    method: 'POST',
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === 'success') {
        closeDownloadModal();
        synchronizeDashboardState();
        Swal.fire(
          'Download Registered',
          'Central allocation saved to SQL server.',
          'success',
        );
      }
    });
}

// Delete download registry record

function deleteDownload(id) {
  Swal.fire({
    title: 'Wipe Download?',
    text: 'Wipe this Central Download from persistence database?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#EF4444',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Yes, wipe allocation',
  }).then((result) => {
    if (result.isConfirmed) {
      let formData = new FormData();
      formData.append('id', id);
      fetch('api/downloads_delete.php', {
        method: 'POST',
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === 'success') {
            synchronizeDashboardState();
            Swal.fire(
              'Removed!',
              'Download allocation pruned from registry.',
              'success',
            );
          }
        });
    }
  });
}

// CRUD Form submission to persistent SQL via PDO

function openDownloadModal(id) {
  const form = document.getElementById('download-form');
  form.reset();
  document.getElementById('download-index').value = '';

  if (id) {
    const record = pmtDownloads.find((d) => d.id === id);
    if (!record) return;

    document.getElementById('download-modal-title').innerText =
      'Edit Central Office Download Document';
    document.getElementById('download-index').value = record.id;
    document.getElementById('dl-title').value = record.title;
    document.getElementById('dl-type').value = record.course_type;
    document.getElementById('dl-duration').value = record.duration_hours;
    document.getElementById('dl-target').value = record.target_trainings;
    document.getElementById('dl-budget-per').value = record.unit_budget;
    document.getElementById('dl-subaro').value = record.subaro_code;
    document.getElementById('dl-uacs').value = record.uacs_code;
  } else {
    document.getElementById('download-modal-title').innerText =
      'Register Central Office Download Document';
  }

  document.getElementById('download-modal').classList.remove('hidden');
}

function closeDownloadModal() {
  document.getElementById('download-modal').classList.add('hidden');
}

//==================================================================
// CSV EXPORT (Training Tracker)
//==================================================================

const DEFAULT_OFFICE_ALLOCATIONS = {
  'Regional Office': { target: 5, budget: 500000.0 },
  'Butuan City': { target: 6, budget: 200000.0 },
  'Agusan del Norte': { target: 5, budget: 150000.0 },
  'Agusan del Sur': { target: 7, budget: 250000.0 },
  'Surigao del Norte': { target: 6, budget: 200000.0 },
  'Surigao del Sur': { target: 6, budget: 200000.0 },
  'Dinagat Islands': { target: 5, budget: 150000.0 },
};

function resetAllocationsToDefault() {
  Swal.fire({
    title: 'Reset PMT Baselines?',
    text: 'This restores the default training target and budget for every provincial office.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#1E40AF',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Yes, reset baselines',
  }).then((result) => {
    if (!result.isConfirmed) return;

    const requests = [];
    Object.keys(DEFAULT_OFFICE_ALLOCATIONS).forEach((office) => {
      const defaults = DEFAULT_OFFICE_ALLOCATIONS[office];
      ['target', 'budget'].forEach((field) => {
        const formData = new FormData();
        formData.append('office_name', office);
        formData.append('field', field);
        formData.append('value', defaults[field]);
        requests.push(
          fetch('api/allocation_update.php', {
            method: 'POST',
            body: formData,
          }),
        );
      });
    });

    Promise.all(requests).then(() => {
      synchronizeDashboardState();
      Swal.fire(
        'Reset Complete',
        'Provincial baselines restored to default PMT values.',
        'success',
      );
    });
  });
}

//==================================================================
// PER-DOWNLOAD PROVINCIAL LEDGER GENERATOR (manually adjustable)
//==================================================================

function generateDownloadLedgerMatrix() {
  const selectEl = document.getElementById('download-ledger-select');
  const downloadId = selectEl.value;

  if (!downloadId) {
    Swal.fire(
      'Select a Download',
      'Please choose a Reference Document Name first.',
      'info',
    );
    return;
  }

  const download = pmtDownloads.find((dl) => dl.id === downloadId);
  if (!download) {
    Swal.fire(
      'Not Found',
      'That registered download could not be found. Try refreshing the page.',
      'error',
    );
    return;
  }

  // Always check the database first for previously saved +/-
  // assignments, so numbers survive a page refresh. Only fall back
  // to an even split if nothing has been saved for this download yet.
  fetch(
    `api/download_ledger_get.php?download_id=${encodeURIComponent(downloadId)}`,
    { cache: 'no-store' },
  )
    .then((res) => res.json())
    .then((res) => {
      const durationBuckets = parseDurationBuckets(download.duration_hours);
      const officeOrder = Object.keys(CARAGA_PROVINCE_COORDINATES).filter(
        (name) => officeAllocations[name],
      );
      const savedRows = res.status === 'success' ? res.assignments : [];

      if (savedRows.length > 0) {
        const buckets = {};
        officeOrder.forEach((officeName) => {
          buckets[officeName] = {};
          durationBuckets.forEach((bucket) => {
            buckets[officeName][bucket] = 0;
          });
        });
        savedRows.forEach((row) => {
          if (
            buckets[row.office_name] &&
            durationBuckets.includes(row.duration_bucket)
          ) {
            buckets[row.office_name][row.duration_bucket] =
              parseInt(row.target_count) || 0;
          }
        });
        downloadLedgerState[downloadId] = {
          durationBuckets,
          officeOrder,
          buckets,
        };
      } else {
        // Nothing saved yet for this download -> seed an even
        // split locally (not persisted until the first edit)
        seedDownloadLedgerState(downloadId, download);
      }

      renderDownloadLedgerTable(downloadId);
    })
    .catch((err) => {
      console.error('Failed to load saved ledger assignments:', err);
      seedDownloadLedgerState(downloadId, download);
      renderDownloadLedgerTable(downloadId);
    });
}

function seedDownloadLedgerState(downloadId, download) {
  const durationBuckets = parseDurationBuckets(download.duration_hours);
  const officeOrder = Object.keys(CARAGA_PROVINCE_COORDINATES).filter(
    (name) => officeAllocations[name],
  );
  const officeTargets = distributeEvenly(
    parseInt(download.target_trainings) || 0,
    officeOrder.length,
  );

  const buckets = {};
  officeOrder.forEach((officeName, index) => {
    const bucketTargets = distributeEvenly(
      officeTargets[index],
      durationBuckets.length,
    );
    const bucketState = {};
    durationBuckets.forEach((bucket, bIndex) => {
      bucketState[bucket] = bucketTargets[bIndex];
    });
    buckets[officeName] = bucketState;
  });

  downloadLedgerState[downloadId] = { durationBuckets, officeOrder, buckets };
}

// Called by each +/- button; delta is +1 or -1
function adjustDownloadLedgerCell(downloadId, officeName, bucket, delta) {
  const state = downloadLedgerState[downloadId];
  if (!state) return;
  const current = state.buckets[officeName][bucket] || 0;
  state.buckets[officeName][bucket] = Math.max(0, current + delta);
  renderDownloadLedgerTable(downloadId);
}

function resetDownloadLedgerAssignments(downloadId) {
  const download = pmtDownloads.find((dl) => dl.id === downloadId);
  if (!download) return;
  seedDownloadLedgerState(downloadId, download);
  renderDownloadLedgerTable(downloadId);
}

function renderDownloadLedgerTable(downloadId) {
  const container = document.getElementById('download-ledger-container');
  const state = downloadLedgerState[downloadId];
  const download = pmtDownloads.find((dl) => dl.id === downloadId);
  if (!state || !download) return;

  const unitBudget = parseFloat(download.unit_budget) || 0;
  const downloadTarget = parseInt(download.target_trainings) || 0;
  const { durationBuckets, officeOrder, buckets } = state;

  let headerCells = `<th class="pb-2.5">Operating Provincial Unit</th>`;
  durationBuckets.forEach((bucket) => {
    headerCells += `<th class="pb-2.5 text-center w-32">${bucket}</th>`;
  });
  headerCells += `<th class="pb-2.5 text-right">Total Budget Allocation</th>`;

  let bodyRows = '';
  let grandTotal = 0;

  officeOrder.forEach((officeName) => {
    const officeBuckets = buckets[officeName];
    const officeTotal = durationBuckets.reduce(
      (sum, bucket) => sum + (officeBuckets[bucket] || 0),
      0,
    );
    grandTotal += officeTotal;
    const totalBudgetAllocation = officeTotal * unitBudget;

    const bucketCells = durationBuckets
      .map((bucket) => {
        const count = officeBuckets[bucket] || 0;
        return `
                        <td class="py-3 text-center">
                            <div class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-lg px-1.5 py-1">
                                <button type="button" onclick="adjustDownloadLedgerCell('${downloadId}', '${officeName}', '${bucket}', -1)" class="w-5 h-5 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all text-[10px] font-bold">&minus;</button>
                                <span class="w-6 text-center font-bold text-slate-800 text-xs">${count}</span>
                                <button type="button" onclick="adjustDownloadLedgerCell('${downloadId}', '${officeName}', '${bucket}', 1)" class="w-5 h-5 flex items-center justify-center rounded bg-white border border-slate-200 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-all text-[10px] font-bold">+</button>
                            </div>
                        </td>
                    `;
      })
      .join('');

    bodyRows += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 font-semibold text-slate-800 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            ${officeName}
                        </td>
                        ${bucketCells}
                        <td class="py-3 text-right font-bold text-slate-900 whitespace-nowrap">${formatCurrency(totalBudgetAllocation)}</td>
                    </tr>
                `;
  });

  // Grand total footer row, so you can see your manual assignment
  // against the download's actual Training Target Volume
  const isBalanced = grandTotal === downloadTarget;
  bodyRows += `
                <tr class="bg-slate-50 font-extrabold text-slate-800">
                    <td class="py-3">Grand Total</td>
                    <td colspan="${durationBuckets.length}" class="py-3 text-center ${isBalanced ? 'text-emerald-600' : 'text-amber-600'}">
                        ${grandTotal} of ${downloadTarget} Target Trainings Assigned
                    </td>
                    <td class="py-3 text-right whitespace-nowrap">${formatCurrency(grandTotal * unitBudget)}</td>
                </tr>
            `;

  container.innerHTML = `
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm mt-2">
                    <div class="border-b border-slate-100 pb-3 mb-4 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-table-list text-amber-500"></i> Provincial Performance Ledger Matrix — ${download.title}
                            </h3>
                            <p class="text-xs text-slate-500">Use the + / − controls to manually assign how many trainings each province runs at each duration.</p>
                        </div>
                        <button type="button" onclick="resetDownloadLedgerAssignments('${downloadId}')" class="text-[10px] font-bold text-dict-bright hover:text-blue-800 border border-slate-100 bg-slate-50 px-2.5 py-1 rounded hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Reset to Even Split
                        </button>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-[9px] font-extrabold uppercase tracking-wider">
                                    ${headerCells}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                ${bodyRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
  container.classList.remove('hidden');
}
