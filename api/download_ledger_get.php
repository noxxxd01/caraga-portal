<?php
/**
 * GET -> returns saved Provincial Ledger +/- assignments.
 * Optional ?download_id=xxx to filter to a single registered download.
 */
require_once __DIR__ . '/_bootstrap.php';

try {
    $downloadId = $_GET['download_id'] ?? null;

    if ($downloadId) {
        $stmt = $db->prepare("SELECT download_id, office_name, duration_bucket, target_count FROM `pmt_download_assignments` WHERE download_id = ?");
        $stmt->execute([$downloadId]);
        $rows = $stmt->fetchAll();
    } else {
        $rows = $db->query("SELECT download_id, office_name, duration_bucket, target_count FROM `pmt_download_assignments`")->fetchAll();
    }

    echo json_encode(['status' => 'success', 'assignments' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}