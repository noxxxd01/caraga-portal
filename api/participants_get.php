<?php
require_once __DIR__ . '/_bootstrap.php';
try {
    $rows = $db->query("SELECT * FROM `participants` ORDER BY `training_date` DESC")->fetchAll();
    echo json_encode(['status' => 'success', 'participants' => $rows]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
}