    <?php
require_once __DIR__ . '/_bootstrap.php';

try {
    $raw = file_get_contents('php://input');
    $rows = json_decode($raw, true);

    if (!is_array($rows)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or empty CSV payload.']);
        exit;
    }

    $insertStmt = $db->prepare("INSERT INTO `participants`
        (id, participant_name, project, program, training_title, training_date, training_id, cert_id, certificate_type, resource_person, sex, province, municipality)
        VALUES (:id, :name, :project, :program, :title, :date, :training_id, :cert_id, :cert_type, :resource_person, :sex, :province, :municipality)
        ON DUPLICATE KEY UPDATE
            participant_name = VALUES(participant_name), project = VALUES(project), program = VALUES(program),
            training_title = VALUES(training_title), training_date = VALUES(training_date), training_id = VALUES(training_id),
            cert_id = VALUES(cert_id), certificate_type = VALUES(certificate_type), resource_person = VALUES(resource_person),
            sex = VALUES(sex), province = VALUES(province), municipality = VALUES(municipality)");

    $lookupStmt = $db->prepare("SELECT province, municipality FROM `trainings` WHERE id = ?");

    $inserted = 0;
    $skipped = 0;

    foreach ($rows as $row) {
        $name = trim($row['Participant Name'] ?? '');
        if ($name === '') { $skipped++; continue; }

        $trainingId = trim($row['Training ID'] ?? '');
        $province = '';
        $municipality = '';
        if ($trainingId) {
            $lookupStmt->execute([$trainingId]);
            $match = $lookupStmt->fetch();
            if ($match) {
                $province = $match['province'];
                $municipality = $match['municipality'];
            }
        }

        $trainingDate = trim($row['Training Date'] ?? '');

        $insertStmt->execute([
            ':id' => 'part-' . bin2hex(random_bytes(6)),
            ':name' => $name,
            ':project' => $row['Project'] ?? '',
            ':program' => $row['Program'] ?? '',
            ':title' => $row['Training Title'] ?? '',
            ':date' => $trainingDate !== '' ? $trainingDate : null,
            ':training_id' => $trainingId,
            ':cert_id' => $row['CertID'] ?? '',
            ':cert_type' => $row['Certificate Type'] ?? '',
            ':resource_person' => $row['Resource Person'] ?? '',
            ':sex' => (($row['Sex'] ?? 'Male') === 'Female') ? 'Female' : 'Male',
            ':province' => $province,
            ':municipality' => $municipality
        ]);
        $inserted++;
    }

    echo json_encode(['status' => 'success', 'inserted' => $inserted, 'skipped' => $skipped]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}