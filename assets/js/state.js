/**
 * Shared mutable state + static reference data for the portal.
 * Loaded first, before every other script.
 */

// High-fidelity geographical defaults for Caraga including regional office coordinates
const CARAGA_PROVINCE_COORDINATES = {
  "Regional Office": { lat: 8.9515, lng: 125.535 }, // Coordinates offset inside Butuan Capital limits
  "Agusan del Norte": { lat: 9.1204, lng: 125.5342 },
  "Agusan del Sur": { lat: 8.5994, lng: 125.9189 },
  "Surigao del Norte": { lat: 9.7891, lng: 125.4958 },
  "Surigao del Sur": { lat: 9.0768, lng: 126.1956 },
  "Dinagat Islands": { lat: 10.1264, lng: 125.5744 },
};

// Core Portal States
let db = [];
let pmtDownloads = [];
let globalTarget = 40;
let officeAllocations = {};
let map = null;
let heatmapLayer = null;
let isHeatmapActive = false;
let activeTab = "dashboard";
let markerGroup = null;
let participantsData = [];
let participantsChartInstance = null;

// Manual per-download, per-province, per-duration target assignments
// for the Provincial Ledger generator (keyed by download id). Kept in
// memory only, so it resets on page reload unless you wire up persistence.
let downloadLedgerState = {};

// Graphic engines
let doughnutChartInstance = null;
let barChartInstance = null;
let provincialChartInstance = null;

// Status Colors Hex Map
const STATUS_HEX_COLORS = {
  completed: "#10B981", // Emerald
  ongoing: "#3B82F6", // Blue
  upcoming: "#F59E0B", // Amber
  cancelled: "#EF4444", // Red
  rescheduled: "#6B7280", // Slate
};
