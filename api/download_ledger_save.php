<?php
/**
 * POST -> upsert a single (download_id, office_name, duration_bucket) cell's
 * manually-assigned target_count. Called once per +/- click.
 */
require_once __DIR__ . '/_bootstrap.php';

try {
    $stmt = $db->prepare("INSERT INTO `pmt_download_assignments` (download_id, office_name, duration_bucket, target_count)
                          VALUES (:download_id, :office_name, :duration_bucket, :target_count)
                          ON DUPLICATE KEY UPDATE target_count = VALUES(target_count)");
    $stmt->execute([
        ':download_id' => $_POST['download_id'] ?? '',
        ':office_name' => $_POST['office_name'] ?? '',
        ':duration_bucket' => $_POST['duration_bucket'] ?? '',
        ':target_count' => (int)($_POST['target_count'] ?? 0)
    ]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
}