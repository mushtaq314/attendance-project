<?php
// save_user.php
header('Content-Type: application/json');
require 'db.php';

$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input['name']) || !isset($input['image']) || !isset($input['descriptor'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$name = trim($input['name']);
$imageData = $input['image']; // data:image/png;base64,...
$descriptor = $input['descriptor']; // array of floats

// Save image
if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
    $imageData = substr($imageData, strpos($imageData, ',') + 1);
    $imageData = base64_decode($imageData);
    $ext = $type[1]; // png or jpeg
    $filename = 'uploads/' . uniqid('face_') . '.' . $ext;
    if (!is_dir('uploads')) mkdir('uploads', 0755, true);
    file_put_contents($filename, $imageData);
} else {
    $filename = null;
}

// Store in DB
$stmt = $pdo->prepare("INSERT INTO users (name, image_path, descriptor) VALUES (:name, :image_path, :descriptor)");
$stmt->execute([
    ':name' => $name,
    ':image_path' => $filename,
    ':descriptor' => json_encode($descriptor)
]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

?>
