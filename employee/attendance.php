<?php
require_once __DIR__ . '/../includes/auth.php'; require_login();
$user = current_user();
$rows = fetch_all("SELECT * FROM attendance WHERE employee_id={$user['id']} ORDER BY timestamp DESC LIMIT 200");
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link href="assets/css/styles.css" rel="stylesheet">
<body class="p-4">
<div class="container">
  <h4>My Attendance</h4>
  <table class="table table-sm"><thead><tr><th>Type</th><th>Time</th><th>Lat</th><th>Lng</th></tr></thead><tbody>
  <?php foreach($rows as $r): ?><tr><td><?=$r['type']?></td><td><?=$r['timestamp']?></td><td><?=$r['lat']?></td><td><?=$r['lng']?></td></tr><?php endforeach; ?>
  </tbody></table>
</div>
</body></html>
