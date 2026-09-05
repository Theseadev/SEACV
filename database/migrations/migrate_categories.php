<?php
// migrate_categories.php - Migration script to support dynamic categories
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    // 1. Alter products table column category to VARCHAR(100)
    $pdo->exec("ALTER TABLE products MODIFY category VARCHAR(100) NOT NULL");
    echo "OK: Kolom products.category berhasil diubah menjadi VARCHAR(100).\n";
} catch (Exception $e) {
    echo "INFO/NOTICE products.category: " . $e->getMessage() . "\n";
}

try {
    // 2. Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "OK: Tabel categories berhasil dibuat atau sudah ada.\n";
} catch (Exception $e) {
    echo "ERROR categories table: " . $e->getMessage() . "\n";
}

try {
    // 3. Seed initial categories
    $defaultCategories = ['CV Kreatif', 'CV ATS', 'Surat Lamaran Kerja'];
    
    // Also include any categories already present in products
    $stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
    $existingFromProducts = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $allToSeed = array_unique(array_merge($defaultCategories, $existingFromProducts));
    
    $ins = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
    foreach ($allToSeed as $catName) {
        $clean = trim($catName);
        if (!empty($clean)) {
            $ins->execute([$clean]);
        }
    }
    echo "OK: Kategori awal berhasil di-seed.\n";

    $currentCats = $pdo->query("SELECT id, name FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo "Daftar kategori di database saat ini:\n";
    foreach ($currentCats as $c) {
        echo "- [ID {$c['id']}] {$c['name']}\n";
    }

} catch (Exception $e) {
    echo "ERROR seeding categories: " . $e->getMessage() . "\n";
}
