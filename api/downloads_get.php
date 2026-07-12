<?php
/**
 * GET  -> returns the list of Central Office PMT downloads.
 */
require_once __DIR__ . '/_bootstrap.php';

            try {
                $downloads = $db->query("SELECT * FROM `pmt_downloads` ORDER BY `id` DESC")->fetchAll();
                echo json_encode(['status' => 'success', 'downloads' => $downloads]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
            }
            exit;

