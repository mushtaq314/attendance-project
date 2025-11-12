<?php
// create_admin.php - Script to create initial admin user
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$adminEmail = 'admin@123.com';
$adminPassword = 'admin123'; // Change this to a secure password
$adminName = 'System Administrator';

try {
    $pdo = db();

    // Check if admin already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND role = ?');
    $stmt->execute([$adminEmail, 'admin']);
    $existing = $stmt->fetch();

    if ($existing) {
        echo "Admin user already exists with email: $adminEmail\n";
        echo "If you forgot the password, you can update it by running:\n";
        echo "UPDATE users SET password = '" . password_hash('newpassword', PASSWORD_DEFAULT) . "' WHERE email = '$adminEmail';\n";
        exit;
    }

    // Create admin user
    $hash = hash_password($adminPassword);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, approved, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$adminName, $adminEmail, $hash, 'admin', 1, 'active']);

    echo "Admin user created successfully!\n";
    echo "Email: $adminEmail\n";
    echo "Password: $adminPassword\n";
    echo "Please change the password after first login.\n";

} catch (Exception $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
