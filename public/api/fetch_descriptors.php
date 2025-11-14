<?php
// fetch_descriptors.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/db.php';

$stmt = db()->query("SELECT id, name, face_descriptor FROM users WHERE face_descriptor IS NOT NULL");
$users = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $users[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'descriptor' => json_decode($row['face_descriptor'], true)
    ];
}
echo json_encode($users);
?>
