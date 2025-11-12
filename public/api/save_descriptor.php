<?php
// saves face descriptor for a user (register face after admin approval)
require_once __DIR__ . '/../../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['user_id']) || empty($data['descriptor'])) {
    http_response_code(400); echo json_encode(['error'=>'invalid']); exit;
}
$user_id = (int)$data['user_id'];
$descriptor = $data['descriptor']; // expected array of floats
$stmt = db()->prepare('UPDATE users SET face_descriptor = ? WHERE id = ?');
$stmt->execute([json_encode($descriptor), $user_id]);

file_put_contents(__DIR__ . '/../../storage/descriptors/'.$user_id.'.json', json_encode($descriptor));

echo json_encode(['ok'=>1]);
