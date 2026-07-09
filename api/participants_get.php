<?php
require_once __DIR__ . '/_bootstrap.php';
try {
    $rows = $db->query("SELECT * FROM `participants` ORDER BY `training_date` DESC")->fetchAll();
    echo json_encode(['status' => 'success', 'participants' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}