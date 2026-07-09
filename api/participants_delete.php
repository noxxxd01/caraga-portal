<?php
require_once __DIR__ . '/_bootstrap.php';
try {
    $stmt = $db->prepare("DELETE FROM `participants` WHERE id = ?");
    $stmt->execute([$_POST['id'] ?? '']);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}