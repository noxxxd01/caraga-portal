<?php
/**
 * POST -> truncates all tables and redirects so install.php re-seeds them.
 */
require_once __DIR__ . '/_bootstrap.php';

            try {
                $db->exec("TRUNCATE TABLE `trainings`");
                $db->exec("TRUNCATE TABLE `pmt_downloads`");
                $db->exec("TRUNCATE TABLE `office_allocations`");
                
                // Re-trigger seeding script via simple self-redirection
                header("Location: " . $_SERVER['PHP_SELF']);
            } catch (PDOException $e) {
                error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
            }
            exit;
