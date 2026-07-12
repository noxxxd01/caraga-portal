<?php
/**
 * GET  -> returns office_allocations + trainings for the executive dashboard.
 */
require_once __DIR__ . '/_bootstrap.php';

            try {
                // Fetch Office Allocations
                $allocations = $db->query("SELECT * FROM `office_allocations`")->fetchAll();
                
                // Fetch Trainings
                $trainings = $db->query("SELECT * FROM `trainings` ORDER BY `start_date` DESC")->fetchAll();

                echo json_encode([
                    'status' => 'success',
                    'office_allocations' => $allocations,
                    'trainings' => $trainings
                ]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
            }
            exit;

