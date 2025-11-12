<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();
require_once __DIR__ . '/../../includes/db.php';
// actions: approve_user, reject_user, assign_task, update_task
$action = $_GET['action'] ?? null;
if ($action === 'approve_user' && !empty($_POST['user_id'])) {
    $stmt = db()->prepare('UPDATE users SET approved = 1 WHERE id = ?');
    $stmt->execute([$_POST['user_id']]);
    echo json_encode(['success' => true, 'message' => 'User approved successfully!']);
    exit;
}
if ($action === 'reject_user' && !empty($_POST['user_id'])) {
    $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$_POST['user_id']]);
    echo json_encode(['success' => true, 'message' => 'User rejected and removed!']);
    exit;
}
if ($action === 'delete_user' && !empty($_POST['user_id'])) {
    $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$_POST['user_id']]);
    echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
    exit;
}
if ($action === 'reset_face' && !empty($_POST['user_id'])) {
    $stmt = db()->prepare('UPDATE users SET face_descriptor = NULL WHERE id = ?');
    $stmt->execute([$_POST['user_id']]);
    echo json_encode(['success' => true, 'message' => 'Face data reset successfully! User will need to recapture face on next login.']);
    exit;
}
// more actions can be added
http_response_code(400); echo json_encode(['error'=>'unknown']);
