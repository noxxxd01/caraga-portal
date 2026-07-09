<?php
/**
 * POST -> update a single field (target|budget) on office_allocations.
 */
require_once __DIR__ . '/_bootstrap.php';

            try {
                $field = $_POST['field']; // target or budget
                if (!in_array($field, ['target', 'budget'])) {
                    throw new Exception("Invalid column allocation modifier requested.");
                }
                
                $stmt = $db->prepare("UPDATE `office_allocations` SET `$field` = ? WHERE office_name = ?");
                $stmt->execute([$_POST['value'], $_POST['office_name']]);
                echo json_encode(['status' => 'success']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;

