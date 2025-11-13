<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');
$u = current_user();
$pdo = db();
$posts = $pdo->query("SELECT p.*, u.name as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id WHERE p.visible_to IN ('all', 'admins') ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Attendance System</title>
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
        .dashboard-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #4CAF50;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .nav-link-custom {
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            margin: 0.25rem;
            transition: all 0.3s;
        }
        .nav-link-custom:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="#">
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom text-white px-3" href="users.php">
                            <i class="fas fa-users me-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom text-white px-3" href="approvals.php">
                            <i class="fas fa-check-circle me-1"></i>Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom text-white px-3" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom text-white px-3" href="posts.php">
                            <i class="fas fa-bullhorn me-1"></i>Posts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom text-white px-3" href="/attendance-project/public/index.php">
                            <i class="fas fa-globe me-1"></i>Public
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white px-3" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-user-shield me-2"></i>Welcome back, <?= htmlspecialchars($u['name']) ?>!
                    </h2>
                    <p class="mb-0 opacity-75">Manage your attendance system efficiently</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-cogs fa-4x opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="dashboard-card p-4 text-center">
                    <div class="card-icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title text-muted mb-3">Total Employees</h5>
                    <div class="stats-number"><?= getEmployeeCount($pdo) ?></div>
                    <small class="text-muted">Active users in system</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card p-4 text-center">
                    <div class="card-icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="card-title text-muted mb-3">Pending Approvals</h5>
                    <div class="stats-number text-warning"><?= getPendingApprovals($pdo) ?></div>
                    <small class="text-muted">Awaiting approval</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card p-4 text-center">
                    <div class="card-icon text-success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title text-muted mb-3">Today's Attendance</h5>
                    <div class="stats-number text-success"><?= getTodayAttendanceCount($pdo) ?></div>
                    <small class="text-muted">Check-ins today</small>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="dashboard-card p-4">
                    <h5 class="mb-3">
                        <i class="fas fa-tasks me-2"></i>Quick Actions
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="users.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Manage Users
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="approvals.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-check me-2"></i>Review Approvals
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="reports.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-chart-line me-2"></i>View Reports
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="export_zip.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-download me-2"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="dashboard-card p-4">
                    <h5 class="mb-3">
                        <i class="fas fa-bullhorn me-2"></i>Recent Posts
                    </h5>
                    <?php if (empty($posts)): ?>
                        <p class="text-muted small">No recent posts.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($posts as $p): ?>
                                <div class="list-group-item px-0 py-2">
                                    <h6 class="mb-1 small fw-bold"><?= htmlspecialchars($p['title']) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($p['author_name'] ?? 'Admin') ?>
                                        <i class="fas fa-clock ms-2 me-1"></i><?= date('M j', strtotime($p['created_at'])) ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3">
                            <a href="posts.php" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>Manage Posts
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
