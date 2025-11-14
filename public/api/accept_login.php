<?php
// accept_login.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/db.php';
session_start();

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
}

$id = (int)$input['id'];

// Optional: validate user exists
$stmt = db()->prepare("SELECT id, name FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];

echo json_encode(['success' => true, 'name' => $user['name']]);
?>
