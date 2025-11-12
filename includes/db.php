<?php
// includes/db.php
$config = require __DIR__ . '/config.php';
$dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
try {
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    die('DB Connection failed: ' . $e->getMessage());
}

// helper
function db() {
    global $pdo;
    return $pdo;
}


 