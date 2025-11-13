<?php
// face_login_handler.php - create session when match returned
session_start();
require_once __DIR__ . '/../../includes/db.php';
if (!empty($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    $stmt = db()->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $user['role'];
        unset($_SESSION['tmp_user_id']); // Clear temporary user ID
        header('Location: /attendance-project/' . ($user['role'] == 'admin' ? 'admin' : 'employee') . '/index.php');
        exit;
    }
} elseif (isset($_SESSION['tmp_user_id'])) {
    // If redirected from employee login, use tmp_user_id for face verification
    $user_id = (int)$_SESSION['tmp_user_id'];
    $stmt = db()->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user && $user['role'] === 'employee') {
        // This is where the actual face recognition logic would go.
        // For now, we'll simulate a successful face recognition and log them in.
        // In a real application, this would involve client-side face detection
        // and sending descriptor data to a server-side API (e.g., public/api/match_descriptor.php)
        // to verify the user.
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $user['role'];
        unset($_SESSION['tmp_user_id']); // Clear temporary user ID
        header('Location: /attendance-project/employee/index.php');
        exit;
    }
}
header('Location: /attendance-project/public/auth/login.php');
exit;
