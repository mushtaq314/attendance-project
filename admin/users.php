<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');
$pdo = db();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide valid name and email.";
    } elseif ($role === 'admin' && empty($password)) {
        $error = "Password is required for admin accounts.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email address already exists.";
        } else {
            $hashedPassword = !empty($password) ? hash_password($password) : NULL;
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role,approved,status) VALUES (?,?,?,?,1,'approved')");
            $stmt->execute([$name, $email, $hashedPassword, $role]);
            header('Location: users.php?success=1');
            exit;
        }
    }
}

$users = fetch_all("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users Management - Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .main-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem 0;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .table thead th {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-approved { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-active { background: #d1ecf1; color: #0c5460; }
        .role-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .role-admin { background: #e3f2fd; color: #1565c0; }
        .role-employee { background: #f3e5f5; color: #7b1fa2; }
        .add-user-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .btn-add {
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        .btn-action {
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            margin: 0.125rem;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="index.php">
                <i class="fas fa-users me-2"></i>Users Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="approvals.php">
                            <i class="fas fa-check-circle me-1"></i>Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-users me-2 text-success"></i>User Management
                </h2>
                <span class="badge bg-primary fs-6">Total: <?= count($users) ?> users</span>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    User added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['approved'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    User approved successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['rejected'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    User rejected and removed!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i>ID</th>
                            <th><i class="fas fa-user me-1"></i>Name</th>
                            <th><i class="fas fa-envelope me-1"></i>Email</th>
                            <th><i class="fas fa-user-tag me-1"></i>Role</th>
                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                            <th><i class="fas fa-cogs me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td class="fw-semibold">#<?= $u['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <?= htmlspecialchars($u['name']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= $u['role'] ?>">
                                        <i class="fas fa-<?= $u['role'] === 'admin' ? 'user-shield' : 'user' ?> me-1"></i>
                                        <?= ucfirst($u['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $u['approved'] ? 'approved' : 'pending' ?>">
                                        <i class="fas fa-<?= $u['approved'] ? 'check-circle' : 'clock' ?> me-1"></i>
                                        <?= $u['approved'] ? 'Approved' : 'Pending' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!$u['approved']): ?>
                                        <form method="post" action="../public/api/admin_actions.php?action=approve_user" class="approval-form" style="display:inline">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action_type" value="approve">
                                            <button type="submit" class="btn btn-success btn-action">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                        </form>
                                        <form method="post" action="../public/api/admin_actions.php?action=reject_user" class="approval-form" style="display:inline">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="action_type" value="reject">
                                            <button type="submit" class="btn btn-danger btn-action">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    <?php endif; ?>
                                    <form method="post" action="../public/api/admin_actions.php?action=reset_face" class="approval-form d-inline" onsubmit="return confirm('Reset face data? User will need to recapture face on next login.')">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="action_type" value="reset_face">
                                        <button type="submit" class="btn btn-outline-warning btn-action btn-sm">
                                            <i class="fas fa-camera me-1"></i>Reset Face
                                        </button>
                                    </form>
                                    <form method="post" action="../public/api/admin_actions.php?action=delete_user" class="approval-form d-inline" onsubmit="return confirm('Delete this user permanently?')">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="action_type" value="delete">
                                        <button type="submit" class="btn btn-outline-danger btn-action btn-sm">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="add-user-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </h4>
                    <p class="mb-0 opacity-75">Create a new user account for the system</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>

            <form method="post" class="mt-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label fw-semibold text-white">
                            <i class="fas fa-user me-1"></i>Full Name
                        </label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter full name" required
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label fw-semibold text-white">
                            <i class="fas fa-envelope me-1"></i>Email Address
                        </label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label fw-semibold text-white">
                            <i class="fas fa-user-tag me-1"></i>Role
                        </label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="employee" <?= ($_POST['role'] ?? '') === 'employee' ? 'selected' : '' ?>>Employee</option>
                            <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="password" class="form-label fw-semibold text-white">
                            <i class="fas fa-lock me-1"></i>Password
                            <small class="text-warning">*</small>
                        </label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password"
                               value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
                        <small class="text-light opacity-75">Required for admins, optional for employees</small>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-add text-white w-100">
                            <i class="fas fa-plus me-2"></i>Add User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle role change to toggle password requirement
            const roleSelect = document.getElementById('role');
            const passwordInput = document.getElementById('password');
            const passwordLabel = document.querySelector('label[for="password"]');

            function updatePasswordRequirement() {
                if (roleSelect.value === 'admin') {
                    passwordInput.required = true;
                    passwordLabel.innerHTML = '<i class="fas fa-lock me-1"></i>Password <small class="text-warning">*</small>';
                } else {
                    passwordInput.required = false;
                    passwordLabel.innerHTML = '<i class="fas fa-lock me-1"></i>Password <small class="text-light opacity-75">(optional)</small>';
                }
            }

            roleSelect.addEventListener('change', updatePasswordRequirement);
            updatePasswordRequirement(); // Initial check

            // Handle approval forms
            document.querySelectorAll('.approval-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const actionType = this.querySelector('input[name="action_type"]').value;
                    const confirmMsg = actionType === 'approve' ? 'Approve this user?' :
                                     actionType === 'reject' ? 'Reject and remove this user?' :
                                     actionType === 'delete' ? 'Delete this user permanently?' :
                                     'Reset face data? User will need to recapture face on next login.';

                    if (confirm(confirmMsg)) {
                        const formData = new FormData(this);
                        fetch(this.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            alert('Error: ' + error.message);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
