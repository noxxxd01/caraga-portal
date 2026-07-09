<?php
/**
 * DICT Caraga - PMT Training Monitoring & Analytics Web Portal
 * Entry point: wires the DB, one-time installer, and all HTML partials
 * together. All AJAX/CRUD logic now lives under /api, and all page
 * script lives under /assets/js.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/install.php';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<body class="text-slate-800 antialiased min-h-screen flex flex-col">

<?php include __DIR__ . '/partials/header.php'; ?>

    <!-- CONTENT PORTAL WRAPPER -->
    <main class="max-w-[1700px] mx-auto p-4 w-full flex-grow flex flex-col gap-6">

<?php include __DIR__ . '/partials/tab_dashboard.php'; ?>
<?php include __DIR__ . '/partials/tab_tracker.php'; ?>
<?php include __DIR__ . '/partials/tab_participants.php'; ?>
<?php include __DIR__ . '/partials/tab_financial.php'; ?>
<?php include __DIR__ . '/partials/tab_api_explorer.php'; ?>
<?php include __DIR__ . '/partials/tab_downloads.php'; ?>

    </main>

<?php include __DIR__ . '/partials/modal_download.php'; ?>
<?php include __DIR__ . '/partials/modal_training.php'; ?>
<?php include __DIR__ . '/partials/modal_participant.php'; ?>

    <!-- CENTRAL PORTAL ENGINE & STATE CONTROLLER -->
    <script src="assets/js/state.js"></script>
    <script src="assets/js/map.js"></script>
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/tracker.js"></script>
    <script src="assets/js/downloads.js"></script>
    <script src="assets/js/participants.js"></script>
    <script src="assets/js/ui.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js?v=<?php echo filemtime(__DIR__ . '/assets/js/dashboard.js'); ?>"></script>
    <script src="assets/js/tracker.js?v=<?php echo filemtime(__DIR__ . '/assets/js/tracker.js'); ?>"></script>
</body>
</html>
