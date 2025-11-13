<?php
// simple admin TOTP entry page
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
if (empty($_SESSION['tmp_admin_id'])) { header('Location: /attendance-project/public/auth/login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['tmp_admin_id']]);
    $u = $stmt->fetch();
    if ($u && verify_totp($u['twofa_secret'], $code)) {
        $_SESSION['user_id'] = $u['id'];
        unset($_SESSION['tmp_admin_id']);
        header('Location: /attendance-project/admin/index.php');
        exit;
    } else {
        $err = 'Invalid code';
    }
}
?>
<!doctype html>
<html><body>
<h3>Enter 2FA code</h3>
<?php if (!empty($err)) echo '<div>'.$err.'</div>'; ?>
<form method="post">
  <input name="code" placeholder="6-digit code" required>
  <button>Verify</button>
</form>
</body></html>
