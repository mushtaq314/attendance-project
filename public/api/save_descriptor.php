<?php
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id']) || !isset($data['descriptor'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

$user_id = $data['user_id'];
$descriptor = json_encode($data['descriptor']);

try {
    $stmt = db()->prepare('UPDATE users SET face_descriptor = ? WHERE id = ?');
    $stmt->execute([$descriptor, $user_id]);

    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
