<?php
/**
 * POST -> delete a training record by id.
 */
require_once __DIR__ . '/_bootstrap.php';

            try {
                $stmt = $db->prepare("DELETE FROM `trainings` WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['status' => 'success']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;

