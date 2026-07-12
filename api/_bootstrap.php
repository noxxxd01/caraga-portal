<?php
/**
 * Shared bootstrap for every api/*.php endpoint.
 * Sets JSON + no-cache headers and loads the DB connection.
 * (config/install.php doesn't need to run here — it only needs to run
 * once, from index.php, before any API calls happen.)
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Session expired or not logged in. Please sign in again.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (empty($submittedToken) || !hash_equals($_SESSION['csrf_token'] ?? '', $submittedToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing security token. Please refresh the page and try again.']);
        exit;
    }
}

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
