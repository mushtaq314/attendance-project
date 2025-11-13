<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
// simple export of attendance csv per user between dates
$from = $_GET['from'] ?? null; $to = $_GET['to'] ?? null; $user = $_GET['user'] ?? null;
if (!$from || !$to) { echo 'provide from & to'; exit; }
$stmt = db()->prepare('SELECT a.*, u.name FROM attendance a JOIN users u ON u.id = a.user_id WHERE a.created_at BETWEEN ? AND ?' . ($user ? ' AND a.user_id = ?' : '') . ' ORDER BY a.created_at');
$params = [$from, $to]; if ($user) $params[] = $user;
$stmt->execute($params);
$rows = $stmt->fetchAll();
$zip = new ZipArchive();
$filename = sys_get_temp_dir().'/attendance_export_'.time().'.zip';
if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) { exit('cannot open zip'); }
// create CSV
$csv = "id,user,action,lat,lng,created_at\n";
foreach ($rows as $r) {
    $csv .= implode(',', [ $r['id'], '"'.str_replace('"','""',$r['name']).'"', $r['action'], $r['latitude'], $r['longitude'], $r['created_at'] ]) . "\n";
}
$zip->addFromString('attendance.csv', $csv);
$zip->close();
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="attendance.zip"');
readfile($filename);
unlink($filename);
exit;
