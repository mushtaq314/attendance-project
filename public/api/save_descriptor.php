<?php
// saves face descriptor for a user (register face after admin approval)
require_once __DIR__ . '/../../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['user_id']) || empty($data['descriptor'])) {
    http_response_code(400); echo json_encode(['error'=>'invalid']); exit;
}
$user_id = (int)$data['user_id'];
$descriptor = $data['descriptor']; // expected array of floats

// Check if user exists and is approved
$stmt = db()->prepare('SELECT id, approved FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    http_response_code(404); echo json_encode(['error'=>'user not found']); exit;
}
if (!$user['approved']) {
    http_response_code(403); echo json_encode(['error'=>'user not approved']); exit;
}

$stmt = db()->prepare('UPDATE users SET face_descriptor = ? WHERE id = ?');
$stmt->execute([json_encode($descriptor), $user_id]);

// Ensure storage directory exists
$storage_dir = __DIR__ . '/../../storage/descriptors/';
if (!is_dir($storage_dir)) {
    mkdir($storage_dir, 0755, true);
}

file_put_contents($storage_dir . $user_id . '.json', json_encode($descriptor));

echo json_encode(['ok'=>1]);
