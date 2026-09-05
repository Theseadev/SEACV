<?php
// public/config.php
// Smart Environment Auto-Detect:
// Otomatis mengenali apakah berjalan di localhost (Laragon) atau di server hosting (InfinityFree)
$serverHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
$hostName = strtolower(explode(':', $serverHost)[0]);
$isLocal = in_array($hostName, ['localhost', '127.0.0.1', 'seacv.test', '::1'])
    || (php_sapi_name() === 'cli' && empty(getenv('DB_HOST')));

if ($isLocal && !getenv('DB_HOST')) {
    // 1. Database Lokal (Laragon)
    $host = 'localhost';
    $dbname = 'seacv';
    $username = 'root';
    $password = '';
} else {
    // 2. Database Production (InfinityFree)
    $host = getenv('DB_HOST') ?: 'sql107.infinityfree.com';
    $dbname = getenv('DB_NAME') ?: 'if0_39237979_seacv';
    $username = getenv('DB_USER') ?: 'if0_39237979';
    $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Fahrul200505';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// Create uploads directory if it doesn't exist
$uploadsDir = __DIR__ . '/uploads';
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}
?>