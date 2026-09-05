<?php
namespace App\Controllers;

use Flight;
use App\Services\Database;
use PDO;
use Exception;

class StorefrontController {
    public function index(): void {
        $pdo = Database::getConnection();
        $request = Flight::request();

        $category_filter = trim($request->query->category ?? '');
        $search_query    = trim($request->query->search ?? '');
        $sort_by         = trim($request->query->sort ?? 'latest');

        $products   = [];
        $categories = [];

        try {
            // Fetch categories from categories table if available, else distinct from products
            try {
                $cat_stmt = $pdo->query("SELECT name FROM categories ORDER BY id ASC");
                $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
            } catch (Exception $e) {
                $categories = [];
            }

            if (empty($categories)) {
                $cat_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
                $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            // Default category filter
            if (empty($category_filter)) {
                if (in_array('CV Kreatif', $categories)) {
                    $category_filter = 'CV Kreatif';
                } elseif (!empty($categories)) {
                    $category_filter = $categories[0];
                } else {
                    $category_filter = 'Semua';
                }
            }

            // Query products
            $queryString = "SELECT * FROM products WHERE 1=1";
            $params = [];

            if ($category_filter !== 'Semua' && !empty($category_filter)) {
                $queryString .= " AND category = :category";
                $params[':category'] = $category_filter;
            }

            if (!empty($search_query)) {
                $queryString .= " AND (name LIKE :search OR category LIKE :search_cat)";
                $params[':search'] = "%" . $search_query . "%";
                $params[':search_cat'] = "%" . $search_query . "%";
            }

            if ($sort_by === 'price_low') {
                $queryString .= " ORDER BY price ASC";
            } elseif ($sort_by === 'price_high') {
                $queryString .= " ORDER BY price DESC";
            } elseif ($sort_by === 'alpha_asc') {
                $queryString .= " ORDER BY name ASC";
            } elseif ($category_filter === 'Semua') {
                $queryString .= " ORDER BY RAND()";
            } else {
                $queryString .= " ORDER BY id DESC";
            }

            $stmt = $pdo->prepare($queryString);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, PDO::PARAM_STR);
            }
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch top 3 newest product IDs per category for 'NEW' badge
            $newProductIds = [];
            try {
                $newStmt = $pdo->query("
                    SELECT id FROM (
                        SELECT id,
                               ROW_NUMBER() OVER (PARTITION BY category ORDER BY id DESC) as rn
                        FROM products
                    ) ranked
                    WHERE rn <= 3
                ");
                $newProductIds = $newStmt->fetchAll(PDO::FETCH_COLUMN, 0);
            } catch (Exception $e) {
                $newProductIds = [];
            }
        } catch (Exception $ex) {
            $products = [];
            $newProductIds = [];
        }

        if (empty($categories)) {
            $categories = ['CV Kreatif', 'CV ATS', 'Surat Lamaran Kerja'];
        }

        // Render storefront view with extracted data
        Flight::render('storefront/index', [
            'products' => $products,
            'categories' => $categories,
            'category_filter' => $category_filter,
            'search_query' => $search_query,
            'sort_by' => $sort_by,
            'newProductIds' => $newProductIds,
            'pdo' => $pdo
        ]);
    }
}
