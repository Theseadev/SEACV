<?php
namespace App\Services;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $configFile = dirname(__DIR__, 2) . '/config.php';
            if (file_exists($configFile)) {
                require_once $configFile;
                global $pdo;
                if (isset($pdo) && $pdo instanceof PDO) {
                    self::$instance = $pdo;
                    return self::$instance;
                }
            }

            // Fallback smart config
            $serverHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
            $hostName = strtolower(explode(':', $serverHost)[0]);
            $isLocal = in_array($hostName, ['localhost', '127.0.0.1', 'seacv.test', '::1'])
                || (php_sapi_name() === 'cli' && empty(getenv('DB_HOST')));

            if ($isLocal && !getenv('DB_HOST')) {
                $host = 'localhost';
                $dbname = 'seacv';
                $username = 'root';
                $password = '';
            } else {
                $host = getenv('DB_HOST') ?: 'sql107.infinityfree.com';
                $dbname = getenv('DB_NAME') ?: 'if0_39237979_seacv';
                $username = getenv('DB_USER') ?: 'if0_39237979';
                $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Fahrul200505';
            }

            try {
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
