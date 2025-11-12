<?php
require_once __DIR__ . '/../../includes/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['user_id'])) { http_response_code(400); exit; }
$user_id = (int)$data['user_id'];
$lat = isset($data['lat']) ? $data['lat'] : null;
$lng = isset($data['lng']) ? $data['lng'] : null;
$provider = $data['provider'] ?? null;
$accuracy = $data['accuracy'] ?? null;

$stmt = db()->prepare('INSERT INTO locations (user_id, lat, lng, provider) VALUES (?,?,?,?)');
$stmt->execute([$user_id,$lat,$lng,$provider]);

// Optionally: also create an attendance 'login' event when action provided
if (!empty($data['action'])) {
    $action = $data['action'];
    $att = db()->prepare('INSERT INTO attendance (user_id, action, latitude, longitude, provider, accuracy) VALUES (?,?,?,?,?,?)');
    $att->execute([$user_id,$action,$lat,$lng,$provider,$accuracy]);
}

echo json_encode(['ok'=>1]);
