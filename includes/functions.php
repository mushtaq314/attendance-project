<?php
// includes/functions.php — core reusable helpers

// ===========================
// PASSWORD & AUTH HELPERS
// ===========================
function hash_password($pw) {
    return password_hash($pw, PASSWORD_DEFAULT);
}

function verify_password($pw, $hash) {
    return password_verify($pw, $hash);
}

// ===========================
// FACE DESCRIPTOR HELPERS
// ===========================
// simple Euclidean distance between two descriptor arrays
function descriptor_distance(array $a, array $b) {
    $sum = 0.0;
    for ($i = 0; $i < count($a); $i++) {
        $d = ($a[$i] - $b[$i]);
        $sum += $d * $d;
    }
    return sqrt($sum);
}

// ===========================
// ADMIN / EMPLOYEE HELPERS
// ===========================
function is_admin($user) {
    return isset($user['role']) && $user['role'] === 'admin';
}

function is_employee($user) {
    return isset($user['role']) && $user['role'] === 'employee';
}

function format_datetime($dt) {
    return date('Y-m-d H:i:s', strtotime($dt));
}

// ===========================
// NOTIFICATION HELPERS
// ===========================
function notify_admin($pdo, $type, $message, $link = null) {
    $stmt = $pdo->prepare("INSERT INTO notifications (type, message, link, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$type, $message, $link]);
}

function notify_employee($pdo, $emp_id, $type, $message) {
    $stmt = $pdo->prepare("INSERT INTO emp_notifications (emp_id, type, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$emp_id, $type, $message]);
}

// ===========================
// LOCATION TRACKING HELPERS
// ===========================
function save_location($pdo, $user_id, $lat, $lng, $status = 'active') {
    $stmt = $pdo->prepare("INSERT INTO locations (user_id, latitude, longitude, status, updated_at)
                           VALUES (?, ?, ?, ?, NOW())
                           ON DUPLICATE KEY UPDATE latitude=?, longitude=?, status=?, updated_at=NOW()");
    $stmt->execute([$user_id, $lat, $lng, $status, $lat, $lng, $status]);
}

function get_live_location($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT latitude, longitude, updated_at FROM locations WHERE user_id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===========================
// TOTP HELPERS (for admin 2FA)
// ===========================
function base32_decode($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $b32 = strtoupper($b32);
    $l = strlen($b32);
    $n = 0;
    $j = 0;
    $binary = '';
    for ($i = 0; $i < $l; $i++) {
        $n = $n << 5;
        $n = $n + strpos($alphabet, $b32[$i]);
        $j += 5;
        if ($j >= 8) {
            $j -= 8;
            $binary .= chr(($n & (0xFF << $j)) >> $j);
        }
    }
    return $binary;
}

function verify_totp($secret, $code, $window = 1) {
    if (!$secret) return false;
    $secretkey = base32_decode($secret);
    $time = floor(time() / 30);
    for ($i = -$window; $i <= $window; $i++) {
        $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $time + $i), $secretkey, true);
        $offset = ord($hash[19]) & 0xf;
        $truncated = substr($hash, $offset, 4);
        $codecalc = unpack('N', $truncated)[1] & 0x7fffffff;
        $codecalc = $codecalc % 1000000;
        if (str_pad($codecalc, 6, '0', STR_PAD_LEFT) == $code) return true;
    }
    return false;
}

// ===========================
// MISSING HELPER FUNCTIONS
// ===========================
function getEmployeeCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'employee'");
    return $stmt->fetch()['count'];
}

function getPendingApprovals($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE approved = 0 AND role = 'employee'");
    return $stmt->fetch()['count'];
}

function getTodayAttendanceCount($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as count FROM attendance WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    return $stmt->fetch()['count'];
}

function fetch_all($query) {
    $pdo = db();
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
