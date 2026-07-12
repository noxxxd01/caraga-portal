<?php
/**
 * POST -> delete a PMT download record by id.
 */
require_once __DIR__ . '/_bootstrap.php';

            try {
                $stmt = $db->prepare("DELETE FROM `pmt_downloads` WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                echo json_encode(['status' => 'success']);
            } catch (PDOException $e) {
                error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
            }
            exit;

