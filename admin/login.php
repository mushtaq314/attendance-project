<?php
session_start();
require_once '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    // Prepare statement to find admin
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $error = "❌ Admin account not found!";
    } elseif (!password_verify($pass, $admin['password'])) {
        $error = "⚠️ Incorrect password!";
    } elseif ($admin['approved'] != 1) {
        $error = "⏳ Account not approved! Please contact the super admin.";
    } else {
        // Check for 2FA
        if (!empty($admin['twofa_secret'])) {
            $_SESSION['tmp_admin_id'] = $admin['id'];
            header('Location: /attendance-project/public/auth/admin_2fa.php');
            exit;
        } else {
            // Success — store session
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['role'] = 'admin';
            header('Location: /attendance-project/admin/index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h4 class="mb-0 fw-bold">Admin Login</h4>
                        <p class="mb-0 opacity-75">Access your admin dashboard</p>
                    </div>
                    <div class="login-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-login w-100 text-white">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="/attendance-project/public/index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
