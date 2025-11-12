<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['descriptor'])) { http_response_code(400); exit; }
$probe = $input['descriptor'];
$threshold = 0.6; // adjust: smaller = stricter
$stmt = db()->query('SELECT id, name, face_descriptor FROM users WHERE face_descriptor IS NOT NULL');
$best = null; $bestScore = PHP_FLOAT_MAX;
while ($row = $stmt->fetch()) {
    $dbdesc = json_decode($row['face_descriptor'], true);
    if (!is_array($dbdesc)) continue;
    $dist = descriptor_distance($probe, $dbdesc);
    if ($dist < $bestScore) {
        $bestScore = $dist;
        $best = $row;
    }
}
if ($best && $bestScore <= $threshold) {
    echo json_encode(['match'=>true,'user_id'=>$best['id'],'name'=>$best['name'],'score'=>$bestScore]);
} else {
    echo json_encode(['match'=>false,'score'=>$bestScore]);
}
