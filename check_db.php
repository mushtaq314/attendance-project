<?php
require 'includes/db.php';
$stmt = db()->query('DESCRIBE users');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Users table columns:\n";
foreach ($columns as $col) {
    echo "- {$col['Field']}: {$col['Type']}\n";
}
?>
