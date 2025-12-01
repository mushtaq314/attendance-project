<?php
// fetch_descriptors.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/db.php';

$stmt = db()->query("SELECT id, name, face_image FROM users WHERE face_image IS NOT NULL");
$users = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $users[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'image' => $row['face_image']
    ];
}
echo json_encode($users);
?>
