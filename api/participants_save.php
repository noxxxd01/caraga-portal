<?php
require_once __DIR__ . '/_bootstrap.php';

try {
    $id = $_POST['id'] ?: uniqid('part-');
    $trainingId = trim($_POST['training_id'] ?? '');

    $province = '';
    $municipality = '';
    if ($trainingId) {
        $lookup = $db->prepare("SELECT province, municipality FROM `trainings` WHERE id = ?");
        $lookup->execute([$trainingId]);
        $match = $lookup->fetch();
        if ($match) {
            $province = $match['province'];
            $municipality = $match['municipality'];
        }
    }
    if (!$province && !empty($_POST['province'])) $province = $_POST['province'];
    if (!$municipality && !empty($_POST['municipality'])) $municipality = $_POST['municipality'];

    $stmt = $db->prepare("INSERT INTO `participants`
        (id, participant_name, project, program, training_title, training_date, training_id, cert_id, certificate_type, resource_person, sex, province, municipality)
        VALUES (:id, :name, :project, :program, :title, :date, :training_id, :cert_id, :cert_type, :resource_person, :sex, :province, :municipality)
        ON DUPLICATE KEY UPDATE
            participant_name = VALUES(participant_name), project = VALUES(project), program = VALUES(program),
            training_title = VALUES(training_title), training_date = VALUES(training_date), training_id = VALUES(training_id),
            cert_id = VALUES(cert_id), certificate_type = VALUES(certificate_type), resource_person = VALUES(resource_person),
            sex = VALUES(sex), province = VALUES(province), municipality = VALUES(municipality)");

    $stmt->execute([
        ':id' => $id,
        ':name' => $_POST['participant_name'] ?? '',
        ':project' => $_POST['project'] ?? '',
        ':program' => $_POST['program'] ?? '',
        ':title' => $_POST['training_title'] ?? '',
        ':date' => !empty($_POST['training_date']) ? $_POST['training_date'] : null,
        ':training_id' => $trainingId,
        ':cert_id' => $_POST['cert_id'] ?? '',
        ':cert_type' => $_POST['certificate_type'] ?? '',
        ':resource_person' => $_POST['resource_person'] ?? '',
        ':sex' => (($_POST['sex'] ?? 'Male') === 'Female') ? 'Female' : 'Male',
        ':province' => $province,
        ':municipality' => $municipality
    ]);

    echo json_encode(['status' => 'success', 'id' => $id]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}