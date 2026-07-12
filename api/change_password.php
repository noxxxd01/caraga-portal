<?php
/**
 * POST -> changes the current logged-in user's password.
 * Requires current_password to match before allowing the change.
 */
require_once __DIR__ . '/_bootstrap.php';

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$errors = [];
if ($currentPassword === '') $errors[] = 'Current password is required.';
if (strlen($newPassword) < 8) $errors[] = 'New password must be at least 8 characters.';
if ($newPassword !== $confirmPassword) $errors[] = 'New password and confirmation do not match.';

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

try {
    $stmt = $db->prepare("SELECT password_hash FROM `users` WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit;
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $db->prepare("UPDATE `users` SET password_hash = ? WHERE id = ?");
    $update->execute([$newHash, $_SESSION['user_id']]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A database error occurred. Please try again.']);
}