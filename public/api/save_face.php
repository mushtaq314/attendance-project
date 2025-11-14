<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/db.php';

// Receive JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!empty($input['image'])) {
    $img = $input['image'];
    // remove "data:image/png;base64,"
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $fileName = __DIR__ . '/../../storage/faces/' . uniqid() . '.png';
    // Ensure directory exists
    $dir = dirname($fileName);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($fileName, $data);
    echo json_encode(['success' => true, 'file' => $fileName]);
} else {
    echo json_encode(['success' => false, 'error' => 'No image data']);
}
?>
