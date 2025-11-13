<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');
$u = current_user();
$pdo = db();
$emp = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$stmt = $pdo->prepare("SELECT a.*, u.name FROM attendance a JOIN users u ON u.id=a.user_id WHERE 1=1" .
    ($emp ? " AND a.user_id=?" : "") .
    ($from ? " AND a.created_at >= ?" : "") .
    ($to ? " AND a.created_at <= ?" : "") .
    " ORDER BY a.created_at DESC");
$params = [];
if ($emp) $params[] = $emp;
if ($from) $params[] = $from . ' 00:00:00';
if ($to) $params[] = $to . ' 23:59:59';
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$users = fetch_all("SELECT id,name FROM users WHERE role='employee'");
$totalRecords = count($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports - Attendance System</title>
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
        .filter-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .filter-card .form-control {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 10px;
        }
        .filter-card .btn {
            border-radius: 10px;
            font-weight: 600;
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
            padding: 1rem;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        .action-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .action-login { background: #d4edda; color: #155724; }
        .action-logout { background: #f8d7da; color: #721c24; }
        .action-break_start { background: #fff3cd; color: #856404; }
        .action-break_end { background: #d1ecf1; color: #0c5460; }
        .action-location { background: #e2e3e5; color: #383d41; }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
        }
        @media (max-width: 768px) {
            .main-content {
                margin: 1rem;
                padding: 1.5rem;
            }
            .filter-card {
                padding: 1.5rem;
            }
            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="index.php">
                <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="users.php">
                            <i class="fas fa-users me-1"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="approvals.php">
                            <i class="fas fa-check-circle me-1"></i>Approvals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="posts.php">
                            <i class="fas fa-bullhorn me-1"></i>Posts
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
                    <i class="fas fa-chart-line me-2 text-success"></i>Attendance Reports
                </h2>
                <span class="badge bg-primary fs-6">
                    <i class="fas fa-database me-1"></i><?= $totalRecords ?> records
                </span>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="mt-2">Total Employees</h6>
                        <small class="text-muted"><?= count($users) ?> active</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h6 class="mt-2">Total Records</h6>
                        <small class="text-muted">Filtered results</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-info">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h6 class="mt-2">Date Range</h6>
                        <small class="text-muted">
                            <?php if ($from && $to): ?>
                                <?= date('M j', strtotime($from)) ?> - <?= date('M j, Y', strtotime($to)) ?>
                            <?php else: ?>
                                All time
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number text-warning">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <h6 class="mt-2">Selected Employee</h6>
                        <small class="text-muted">
                            <?php
                            if ($emp) {
                                foreach($users as $u) {
                                    if ($u['id'] == $emp) {
                                        echo htmlspecialchars($u['name']);
                                        break;
                                    }
                                }
                            } else {
                                echo "All employees";
                            }
                            ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="filter-card">
                <h5 class="mb-3">
                    <i class="fas fa-filter me-2"></i>Filter Reports
                </h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label fw-semibold">
                            <i class="fas fa-user me-1"></i>Select Employee
                        </label>
                        <select name="employee_id" id="employee_id" class="form-control">
                            <option value="">All Employees</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= ($emp == $u['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="from" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1"></i>From Date
                        </label>
                        <input name="from" id="from" type="date" class="form-control" value="<?= $from ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="to" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1"></i>To Date
                        </label>
                        <input name="to" id="to" type="date" class="form-control" value="<?= $to ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-light w-100">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="export_zip.php?user=<?= $emp ?>&from=<?= $from ?>&to=<?= $to ?>" class="btn btn-outline-light w-100">
                            <i class="fas fa-download me-2"></i>Export ZIP
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i>ID</th>
                            <th><i class="fas fa-user me-1"></i>Employee</th>
                            <th><i class="fas fa-cogs me-1"></i>Action</th>
                            <th><i class="fas fa-clock me-1"></i>Timestamp</th>
                            <th><i class="fas fa-map-marker-alt me-1"></i>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <br>No attendance records found for the selected filters.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($rows as $r): ?>
                                <tr>
                                    <td class="fw-semibold">#<?= $r['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem;">
                                                <?= strtoupper(substr($r['name'], 0, 1)) ?>
                                            </div>
                                            <?= htmlspecialchars($r['name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="action-badge action-<?= $r['action'] ?>">
                                            <i class="fas fa-<?= $r['action'] === 'login' ? 'sign-in-alt' : ($r['action'] === 'logout' ? 'sign-out-alt' : ($r['action'] === 'break_start' ? 'coffee' : ($r['action'] === 'break_end' ? 'play' : 'map-marker-alt'))) ?> me-1"></i>
                                            <?= ucfirst(str_replace('_', ' ', $r['action'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">
                                                <?= date('M j, Y', strtotime($r['created_at'])) ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= date('g:i A', strtotime($r['created_at'])) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($r['latitude'] && $r['longitude']): ?>
                                            <div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-map-pin me-1"></i>
                                                    Lat: <?= number_format($r['latitude'], 6) ?>
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-map-pin me-1"></i>
                                                    Lng: <?= number_format($r['longitude'], 6) ?>
                                                </small>
                                                <?php if ($r['provider']): ?>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-satellite me-1"></i>
                                                        <?= htmlspecialchars($r['provider']) ?>
                                                        <?php if ($r['accuracy']): ?>
                                                            (±<?= number_format($r['accuracy'], 1) ?>m)
                                                        <?php endif; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-map-marker-alt-slash me-1"></i>No location
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
