<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');
$pdo = db();

$stmt = $pdo->query('SELECT * FROM users WHERE approved = 0 AND role = "employee" ORDER BY created_at DESC');
$pending = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Approvals - Attendance System</title>
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
        .approval-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .approval-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .btn-approve {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .main-content {
                margin: 1rem;
                padding: 1.5rem;
            }
            .approval-card {
                padding: 1rem;
            }
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="index.php">
                <i class="fas fa-check-circle me-2"></i>Pending Approvals
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
                        <a class="nav-link text-white" href="users.php">
                            <i class="fas fa-users me-1"></i>Users
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
                    <i class="fas fa-user-clock me-2 text-warning"></i>Pending Employee Approvals
                </h2>
                <span class="badge bg-warning text-dark fs-6">
                    <i class="fas fa-clock me-1"></i><?= count($pending) ?> pending
                </span>
            </div>

            <?php if (empty($pending)): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle text-success"></i>
                    <h4>No Pending Approvals</h4>
                    <p>All employee registrations have been reviewed.</p>
                    <a href="users.php" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>View All Users
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($pending as $p): ?>
                        <div class="col-lg-6 col-xl-4 mb-3">
                            <div class="approval-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="user-avatar me-3">
                                        <?= strtoupper(substr($p['name'], 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($p['name']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($p['email']) ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>Registered: <?= date('M j, Y', strtotime($p['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <form method="post" action="../public/api/admin_actions.php?action=approve_user" class="approval-form flex-fill">
                                        <input type="hidden" name="user_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action_type" value="approve">
                                        <button type="submit" class="btn btn-approve w-100">
                                            <i class="fas fa-check me-2"></i>Approve
                                        </button>
                                    </form>
                                    <form method="post" action="../public/api/admin_actions.php?action=reject_user" class="approval-form flex-fill">
                                        <input type="hidden" name="user_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action_type" value="reject">
                                        <button type="submit" class="btn btn-reject w-100">
                                            <i class="fas fa-times me-2"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.approval-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const actionType = this.querySelector('input[name="action_type"]').value;
                    const confirmMsg = actionType === 'approve' ? 'Approve this employee?' : 'Reject and remove this employee?';

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
