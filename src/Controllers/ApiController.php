<?php
namespace App\Controllers;

use Flight;
use App\Services\Database;
use PDO;
use Exception;

class ApiController {
    public function categories(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Must be logged in as admin to access category API
        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            Flight::json([
                'success' => false,
                'message' => 'Akses ditolak. Silakan login terlebih dahulu.'
            ], 401);
            return;
        }

        $pdo = Database::getConnection();
        $request = Flight::request();
        $action = $request->query->action ?? $request->data->action ?? 'list';

        try {
            if ($action === 'list') {
                $stmt = $pdo->query("
                    SELECT c.id, c.name, COUNT(p.id) AS product_count 
                    FROM categories c
                    LEFT JOIN products p ON LOWER(p.category) = LOWER(c.name)
                    GROUP BY c.id, c.name
                    ORDER BY c.id ASC
                ");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                Flight::json([
                    'success' => true,
                    'categories' => $categories
                ]);
                return;
            }

            if ($action === 'add') {
                if ($request->method !== 'POST') {
                    Flight::json([
                        'success' => false,
                        'message' => 'Metode request tidak diizinkan. Gunakan POST.'
                    ], 405);
                    return;
                }

                $rawName = trim($request->data->name ?? '');
                $name = preg_replace('/\s+/', ' ', $rawName);

                if (empty($name)) {
                    Flight::json(['success' => false, 'message' => 'Nama kategori tidak boleh kosong.']);
                    return;
                }

                if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
                    Flight::json(['success' => false, 'message' => 'Nama kategori harus terdiri dari 2 hingga 100 karakter.']);
                    return;
                }

                $chk = $pdo->prepare("SELECT id, name FROM categories WHERE LOWER(name) = LOWER(?) LIMIT 1");
                $chk->execute([$name]);
                $existing = $chk->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    Flight::json([
                        'success' => false,
                        'message' => 'Kategori "' . htmlspecialchars($existing['name']) . '" sudah ada.',
                        'category' => $existing
                    ]);
                    return;
                }

                $ins = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                $ins->execute([$name]);
                $newId = (int)$pdo->lastInsertId();

                Flight::json([
                    'success' => true,
                    'message' => 'Kategori "' . htmlspecialchars($name) . '" berhasil ditambahkan!',
                    'category' => [
                        'id' => $newId,
                        'name' => $name,
                        'product_count' => 0
                    ]
                ]);
                return;
            }

            if ($action === 'delete') {
                if ($request->method !== 'POST') {
                    Flight::json([
                        'success' => false,
                        'message' => 'Metode request tidak diizinkan. Gunakan POST.'
                    ], 405);
                    return;
                }

                $id = (int)($request->data->id ?? 0);
                if ($id <= 0) {
                    Flight::json(['success' => false, 'message' => 'ID kategori tidak valid.']);
                    return;
                }

                $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                $cat = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$cat) {
                    Flight::json(['success' => false, 'message' => 'Kategori tidak ditemukan.']);
                    return;
                }

                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(category) = LOWER(?)");
                $countStmt->execute([$cat['name']]);
                $productCount = (int)$countStmt->fetchColumn();

                if ($productCount > 0) {
                    Flight::json([
                        'success' => false,
                        'message' => 'Kategori "' . htmlspecialchars($cat['name']) . '" tidak dapat dihapus karena masih digunakan oleh ' . $productCount . ' template produk.'
                    ]);
                    return;
                }

                $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $del->execute([$id]);

                Flight::json([
                    'success' => true,
                    'message' => 'Kategori "' . htmlspecialchars($cat['name']) . '" berhasil dihapus.'
                ]);
                return;
            }

            Flight::json(['success' => false, 'message' => 'Action tidak dikenali.']);
        } catch (Exception $e) {
            Flight::json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function upgrade(): void {
        require_once dirname(__DIR__, 2) . '/api_upgrade.php';
    }
}
