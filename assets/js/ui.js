/**
 * Generic UI helpers: tab switching, filters, currency formatting.
 */

function escapeHtml(str) {
  const div = document.createElement("div");
  div.textContent = str ?? "";
  return div.innerHTML;
}

function setActiveTab(tabName) {
  activeTab = tabName;

  document
    .querySelectorAll(".tab-content")
    .forEach((el) => el.classList.add("hidden"));
  const targetTab = document.getElementById("tab-" + tabName);
  if (targetTab) targetTab.classList.remove("hidden");

  document.querySelectorAll('[id^="nav-btn-"]').forEach((btn) => {
    btn.classList.remove("bg-dict-bright", "text-white");
    if (!btn.classList.contains("text-slate-300")) {
      btn.classList.add("text-slate-300");
    }
  });

  const activeBtn = document.getElementById("nav-btn-" + tabName);
  if (activeBtn) {
    activeBtn.classList.remove("text-slate-300");
    activeBtn.classList.add("bg-dict-bright", "text-white");
  }

  // Re-sync map size when the dashboard tab becomes visible again
  if (tabName === "dashboard" && map) {
    setTimeout(() => map.invalidateSize(), 100);
  }
}

//==================================================================
// CURRENCY / FORMATTING HELPERS
//==================================================================

function formatCurrency(value) {
  const num = parseFloat(value) || 0;
  return (
    "₱" +
    num.toLocaleString("en-PH", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}

//==================================================================
// DISTRIBUTION HELPERS (used by the Provincial Ledger widgets)
//==================================================================

// Evenly splits `total` into `count` whole-number buckets. Any
// remainder that doesn't divide evenly is handed out one at a time
// to the first buckets (e.g. distributeEvenly(5, 7) -> [1,1,1,1,1,0,0]).
function distributeEvenly(total, count) {
  if (count <= 0) return [];
  const base = Math.floor(total / count);
  const remainder = total % count;
  return Array.from(
    { length: count },
    (_, i) => base + (i < remainder ? 1 : 0),
  );
}

// Parses a PMT Download's duration_hours string (e.g. "16h, 20h, 40h"
// or "3h, 4h") into an array of duration bucket labels. Falls back to
// the full known set when the value doesn't look like a duration list
// (e.g. "Full Catalog (All Speeds)").
function parseDurationBuckets(durationString) {
  const parts = (durationString || "")
    .split(",")
    .map((s) => s.trim())
    .filter(Boolean);
  const looksLikeDurations =
    parts.length > 0 && parts.every((p) => /\d/.test(p));
  return looksLikeDurations ? parts : ["16h", "20h", "40h", "3h", "4h"];
}

//==================================================================
// FILTER SYNCHRONIZATION (Map <-> Tracker filters stay mirrored)
//==================================================================

function syncDashboardFilters(sourceEl, filterKey) {
  const partnerMap = {
    "map-province": "tracker-province-select",
    "tracker-province": "map-province",
    "map-status": "tracker-status-select",
    "tracker-status": "map-status",
    "map-search": "tracker-search-input",
    "tracker-search": "map-search-input",
  };

  const partnerId = partnerMap[filterKey];
  if (partnerId) {
    const partnerEl = document.getElementById(partnerId);
    if (partnerEl) partnerEl.value = sourceEl.value;
  }

  synchronizeDashboardState();
}

function clearAllFilters() {
  [
    "map-province",
    "tracker-province-select",
    "map-status",
    "tracker-status-select",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  ["map-search-input", "tracker-search-input"].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  synchronizeDashboardState();
}

//==================================================================
// TRAINING FORM MODAL (CREATE / EDIT / CLONE)
//==================================================================
