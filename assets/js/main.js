/**
 * App entry point: boots the map, charts, then loads live data.
 * Must load last, after every module above has defined its functions.
 */

window.addEventListener('load', function () {
  initializeGISMap();
  initializeCharts();
  initializeParticipantsChart();
  synchronizeDashboardState();
  fetchParticipants();
});
