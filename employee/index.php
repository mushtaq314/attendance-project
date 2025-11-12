<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
checkAuth('employee');
$u = current_user();
$posts = db()->query("SELECT p.*, u.name as author_name FROM posts p LEFT JOIN users u ON p.user_id = u.id WHERE p.visible_to IN ('all', 'employees') ORDER BY p.created_at DESC LIMIT 10")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <title>Employee Dashboard</title>
  <link href="../public/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script src="../public/assets/js/app.js"></script>
  <script src="../public/assets/js/offline-sync.js"></script>
</head>
<body class="bg-light">
<div class="container mt-4">
  <h2 class="text-success mb-3">Welcome, <?= htmlspecialchars($u['name']) ?></h2>

  <div class="d-flex gap-2 mb-4">
    <button class="btn btn-primary" onclick="startLivePing(<?= $u['id'] ?>)">Start Shift</button>
    <button class="btn btn-danger" onclick="stopLivePing(<?= $u['id'] ?>)">End Shift</button>
    <button class="btn btn-warning" onclick="sendLocation(<?= $u['id'] ?>,'break_start')">Break Start</button>
    <button class="btn btn-success" onclick="sendLocation(<?= $u['id'] ?>,'break_end')">Break End</button>
  </div>

  <div class="card p-3 mb-3 shadow-sm">
    <h5>Current Location</h5>
    <div id="location-status">Fetching location...</div>
  </div>

  <div class="card p-3 shadow-sm">
    <h5>Shift Log</h5>
    <div id="shift-log"></div>
  </div>

  <?php if (!empty($posts)): ?>
  <div class="card p-3 shadow-sm mt-3">
    <h5>Company Announcements</h5>
    <div class="list-group list-group-flush">
      <?php foreach($posts as $p): ?>
        <div class="list-group-item px-0">
          <h6 class="mb-1"><?= htmlspecialchars($p['title']) ?></h6>
          <p class="mb-1 small text-muted"><?= nl2br(htmlspecialchars(substr($p['body'], 0, 100))) ?><?php if (strlen($p['body']) > 100) echo '...'; ?></p>
          <small class="text-muted">By <?= htmlspecialchars($p['author_name'] ?? 'Admin') ?> on <?= date('M j, Y', strtotime($p['created_at'])) ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <footer class="mt-4 text-center">
    <a href="profile.php" class="btn btn-outline-secondary">Profile</a>
    <a href="logout.php" class="btn btn-danger ms-2">Logout</a>
  </footer>
</div>

<script>
// Update location live
setInterval(() => {
  navigator.geolocation.getCurrentPosition(pos => {
    document.getElementById('location-status').innerText =
      `Lat: ${pos.coords.latitude}, Lng: ${pos.coords.longitude}`;
  });
}, 10000); // every 10 sec
</script>
</body>
</html>
