<?php
/**
 * POST -> insert/update a training record (upsert on id).
 */
require_once __DIR__ . '/_bootstrap.php';

            $targetParticipants = (int)($_POST['target_participants'] ?? 0);
            $malePart = (int)($_POST['male_participants'] ?? 0);
            $femalePart = (int)($_POST['female_participants'] ?? 0);
            $budgetAllocated = (float)($_POST['budget_allocated'] ?? 0);
            $budgetUtilized = (float)($_POST['budget_utilized'] ?? 0);
            $title = trim($_POST['training_title'] ?? '');
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $lat = (float)($_POST['latitude'] ?? 0);
            $lng = (float)($_POST['longitude'] ?? 0);

            $errors = [];
            if ($title === '') $errors[] = 'Training title is required.';
            if ($targetParticipants < 0) $errors[] = 'Target participants cannot be negative.';
            if ($malePart < 0 || $femalePart < 0) $errors[] = 'Participant counts cannot be negative.';
            if ($budgetAllocated < 0) $errors[] = 'Budget allocated cannot be negative.';
            if ($budgetUtilized < 0) $errors[] = 'Budget utilized cannot be negative.';
            if ($budgetUtilized > $budgetAllocated) $errors[] = 'Budget utilized cannot exceed budget allocated.';
            if ($startDate === '' || $endDate === '') $errors[] = 'Start and end dates are required.';
            if ($startDate !== '' && $endDate !== '' && strtotime($endDate) < strtotime($startDate)) {
                $errors[] = 'End date cannot be before start date.';
            }
            if ($lat < 7.5 || $lat > 11.5 || $lng < 124.5 || $lng > 127.0) {
                $errors[] = 'Coordinates must reside within standard Caraga regional limits.';
            }

            if (!empty($errors)) {
                echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
                exit;
            }

            try {
                $id = !empty($_POST['id']) ? $_POST['id'] : 'tr-' . time();
                
                // Server side sex disaggregation auto-totaling check
                $male = intval($_POST['male_participants'] ?? 0);
                $female = intval($_POST['female_participants'] ?? 0);
                $actual = $male + $female;

                $sql = "INSERT INTO `trainings` (
                            id, training_title, course_code, course_name, province, municipality, barangay, venue,
                            latitude, longitude, start_date, end_date, course_officer, resource_person,
                            course_type, duration_hours, implementation_mode,
                            target_participants, male_participants, female_participants, actual_participants,
                            budget_allocated, budget_utilized, status, drive_link, photos, documents
                        ) VALUES (
                            :id, :title, :code, :name, :province, :municipality, :barangay, :venue,
                            :latitude, :longitude, :start_date, :end_date, :course_officer, :resource_person,
                            :course_type, :duration_hours, :implementation_mode,
                            :target, :male, :female, :actual, :allocated, :utilized, :status, :drive_link, :photos, :documents
                        ) ON DUPLICATE KEY UPDATE
                            training_title = VALUES(training_title),
                            course_code = VALUES(course_code),
                            course_name = VALUES(course_name),
                            province = VALUES(province),
                            municipality = VALUES(municipality),
                            barangay = VALUES(barangay),
                            venue = VALUES(venue),
                            latitude = VALUES(latitude),
                            longitude = VALUES(longitude),
                            start_date = VALUES(start_date),
                            end_date = VALUES(end_date),
                            course_officer = VALUES(course_officer),
                            resource_person = VALUES(resource_person),
                            course_type = VALUES(course_type),
                            duration_hours = VALUES(duration_hours),
                            implementation_mode = VALUES(implementation_mode),
                            target_participants = VALUES(target_participants),
                            male_participants = VALUES(male_participants),
                            female_participants = VALUES(female_participants),
                            actual_participants = VALUES(actual_participants),
                            budget_allocated = VALUES(budget_allocated),
                            budget_utilized = VALUES(budget_utilized),
                            status = VALUES(status),
                            drive_link = VALUES(drive_link),
                            photos = VALUES(photos),
                            documents = VALUES(documents)";

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':id' => $id,
                    ':title' => $_POST['training_title'],
                    ':code' => $_POST['course_code'],
                    ':name' => $_POST['course_name'] ?? '',
                    ':province' => $_POST['province'],
                    ':municipality' => $_POST['municipality'],
                    ':barangay' => $_POST['barangay'],
                    ':venue' => $_POST['venue'],
                    ':latitude' => $_POST['latitude'],
                    ':longitude' => $_POST['longitude'],
                    ':start_date' => $_POST['start_date'],
                    ':end_date' => $_POST['end_date'],
                    ':course_officer' => $_POST['course_officer'],
                    ':resource_person' => $_POST['resource_person'],
                    ':course_type' => $_POST['course_type'] ?? 'Webinar',
                    ':duration_hours' => $_POST['duration_hours'] ?? '3',
                    ':implementation_mode' => $_POST['implementation_mode'] ?? 'Face-to-Face',
                    ':target' => intval($_POST['target_participants']),
                    ':male' => $male,
                    ':female' => $female,
                    ':actual' => $actual,
                    ':allocated' => floatval($_POST['budget_allocated']),
                    ':utilized' => floatval($_POST['budget_utilize'] ?? $_POST['budget_utilized']),
                    ':status' => $_POST['status'],
                    ':drive_link' => $_POST['drive_link'] ?? '',
                    ':photos' => isset($_POST['photos']) ? 1 : 0,
                    ':documents' => isset($_POST['documents']) ? 1 : 0
                ]);

                echo json_encode(['status' => 'success']);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
            }
            exit;