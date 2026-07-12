<?php
/**
 * POST -> deletes all saved Provincial Ledger assignments for one download_id.
 * Called automatically when that download is deleted, so orphaned rows don't
 * pile up in pmt_download_assignments.
 */
require_once __DIR__ . '/_bootstrap.php';

try {
    $stmt = $db->prepare("DELETE FROM `pmt_download_assignments` WHERE download_id = ?");
    $stmt->execute([$_POST['download_id'] ?? '']);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
}