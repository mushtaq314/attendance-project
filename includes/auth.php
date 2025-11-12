<?php
// includes/auth.php - helpers
require_once __DIR__ . '/db.php';
session_start();

// Check role-based access
function checkAuth($role) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != $role) {
        header("Location: ../{$role}/login.php");
        exit();
    }
}

function current_user() {
    if (!empty($_SESSION['user_id'])) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

function require_admin() {
    $u = current_user();
    if (!$u || $u['role'] !== 'admin') {
        header('Location: /auth/login.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

?>
