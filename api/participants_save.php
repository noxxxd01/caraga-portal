<?php
/**
 * POST -> insert/update a single participant record (upsert on id).
 * Province/Municipality typed in the modal always win. Training ID lookup
 * only fills them in automatically when the modal's fields are left blank
 * — it never silently overrides a value you explicitly set or edited.
 */
require_once __DIR__ . '/_bootstrap.php';

$name = trim($_POST['participant_name'] ?? '');
$sex = $_POST['sex'] ?? '';
$trainingDate = $_POST['training_date'] ?? '';

$errors = [];
if ($name === '') $errors[] = 'Participant name is required.';
if (!in_array($sex, ['Male', 'Female'], true)) $errors[] = 'Sex must be Male or Female.';
if ($trainingDate !== '' && !DateTime::createFromFormat('Y-m-d', $trainingDate)) {
    $errors[] = 'Training date is not a valid date.';
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

try {
    $id = $_POST['id'] ?: uniqid('part-');
    $trainingId = trim($_POST['training_id'] ?? '');

    // What the modal actually submitted — this is authoritative
    $province = trim($_POST['province'] ?? '');
    $municipality = trim($_POST['municipality'] ?? '');

    // Only auto-fill from the linked training when the modal's own
    // Province field was left blank
    if ($province === '' && $trainingId) {
        $lookup = $db->prepare("SELECT province, municipality FROM `trainings` WHERE id = ?");
        $lookup->execute([$trainingId]);
        $match = $lookup->fetch();
        if ($match) {
            $province = $match['province'];
            $municipality = $match['municipality'];
        }
    }

    $stmt = $db->prepare("INSERT INTO `participants`
        (id, participant_name, project, program, training_title, training_date, training_id, cert_id, certificate_type, resource_person, sex, province, municipality)
        VALUES (:id, :name, :project, :program, :title, :date, :training_id, :cert_id, :cert_type, :resource_person, :sex, :province, :municipality)
        ON DUPLICATE KEY UPDATE
            participant_name = VALUES(participant_name),
            project = VALUES(project),
            program = VALUES(program),
            training_title = VALUES(training_title),
            training_date = VALUES(training_date),
            training_id = VALUES(training_id),
            cert_id = VALUES(cert_id),
            certificate_type = VALUES(certificate_type),
            resource_person = VALUES(resource_person),
            sex = VALUES(sex),
            province = VALUES(province),
            municipality = VALUES(municipality)");

    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':project' => $_POST['project'] ?? '',
        ':program' => $_POST['program'] ?? '',
        ':title' => $_POST['training_title'] ?? '',
        ':date' => $trainingDate !== '' ? $trainingDate : null,
        ':training_id' => $trainingId,
        ':cert_id' => $_POST['cert_id'] ?? '',
        ':cert_type' => $_POST['certificate_type'] ?? '',
        ':resource_person' => $_POST['resource_person'] ?? '',
        ':sex' => $sex,
        ':province' => $province,
        ':municipality' => $municipality
    ]);

    echo json_encode(['status' => 'success', 'id' => $id]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
}