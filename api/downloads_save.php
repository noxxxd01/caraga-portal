<?php
/**
 * POST -> insert/update a PMT download record (upsert on id).
 */
require_once __DIR__ . '/_bootstrap.php';

            $target = (int)($_POST['target_trainings'] ?? 0);
            $budget = (float)($_POST['unit_budget'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $subaro = trim($_POST['subaro_code'] ?? '');
            $uacs = trim($_POST['uacs_code'] ?? '');

            $errors = [];
            if ($title === '') $errors[] = 'Title is required.';
            if ($target < 1) $errors[] = 'Target Trainings must be at least 1.';
            if ($budget < 0) $errors[] = 'Budget per Training cannot be negative.';
            if ($subaro === '') $errors[] = 'Sub-ARO Code is required.';
            if ($uacs === '') $errors[] = 'UACS Code is required.';

            if (!empty($errors)) {
                echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
                exit;
            }

            try {
                $id = !empty($_POST['id']) ? $_POST['id'] : 'dl-' . time();
                $sql = "INSERT INTO `pmt_downloads` (id, title, target_trainings, unit_budget, subaro_code, uacs_code, course_type, duration_hours, drive_link) 
                        VALUES (:id, :title, :target_trainings, :unit_budget, :subaro_code, :uacs_code, :course_type, :duration_hours, :drive_link)
                        ON DUPLICATE KEY UPDATE 
                            title = VALUES(title),
                            target_trainings = VALUES(target_trainings),
                            unit_budget = VALUES(unit_budget),
                            subaro_code = VALUES(subaro_code),
                            uacs_code = VALUES(uacs_code),
                            course_type = VALUES(course_type),
                            duration_hours = VALUES(duration_hours),
                            drive_link = VALUES(drive_link)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':id' => $id,
                    ':title' => $_POST['title'],
                    ':target_trainings' => intval($_POST['target_trainings']),
                    ':unit_budget' => floatval($_POST['unit_budget']),
                    ':subaro_code' => $_POST['subaro_code'],
                    ':uacs_code' => $_POST['uacs_code'],
                    ':course_type' => $_POST['course_type'],
                    ':duration_hours' => $_POST['duration_hours'],
                    ':drive_link' => $_POST['drive_link']
                ]);
                echo json_encode(['status' => 'success']);
            } catch (PDOException $e) {
                error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
            }
            exit;

