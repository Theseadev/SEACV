<?php
// api_category.php - Category Management API for Admin
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';
global $pdo;

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
    if ($action === 'list') {
        // Fetch all categories with product count
        $stmt = $pdo->query("
            SELECT c.id, c.name, COUNT(p.id) AS product_count 
            FROM categories c
            LEFT JOIN products p ON LOWER(p.category) = LOWER(c.name)
            GROUP BY c.id, c.name
            ORDER BY c.id ASC
        ");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'categories' => $categories
        ]);
        exit;
    }

    if ($action === 'add') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan. Gunakan POST.']);
            exit;
        }

        $rawName = trim($_POST['name'] ?? '');
        // Clean up multiple spaces
        $name = preg_replace('/\s+/', ' ', $rawName);

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Nama kategori tidak boleh kosong.']);
            exit;
        }

        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            echo json_encode(['success' => false, 'message' => 'Nama kategori harus terdiri dari 2 hingga 100 karakter.']);
            exit;
        }

        // Check if category already exists (case-insensitive)
        $chk = $pdo->prepare("SELECT id, name FROM categories WHERE LOWER(name) = LOWER(?) LIMIT 1");
        $chk->execute([$name]);
        $existing = $chk->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            echo json_encode([
                'success' => false,
                'message' => 'Kategori "' . htmlspecialchars($existing['name']) . '" sudah ada.',
                'category' => $existing
            ]);
            exit;
        }

        // Insert new category
        $ins = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $ins->execute([$name]);
        $newId = (int)$pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Kategori "' . htmlspecialchars($name) . '" berhasil ditambahkan!',
            'category' => [
                'id' => $newId,
                'name' => $name,
                'product_count' => 0
            ]
        ]);
        exit;
    }

    if ($action === 'delete') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan. Gunakan POST.']);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID kategori tidak valid.']);
            exit;
        }

        // Fetch category
        $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cat) {
            echo json_encode(['success' => false, 'message' => 'Kategori tidak ditemukan.']);
            exit;
        }

        // Check if any product is using this category
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(category) = LOWER(?)");
        $countStmt->execute([$cat['name']]);
        $productCount = (int)$countStmt->fetchColumn();

        if ($productCount > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Kategori "' . htmlspecialchars($cat['name']) . '" tidak dapat dihapus karena masih digunakan oleh ' . $productCount . ' template produk.'
            ]);
            exit;
        }

        // Delete from categories table
        $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $del->execute([$id]);

        echo json_encode([
            'success' => true,
            'message' => 'Kategori "' . htmlspecialchars($cat['name']) . '" berhasil dihapus.'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Action tidak dikenali.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
    ]);
}
