<?php
namespace App\Controllers;

use Flight;
use App\Services\Database;
use PDO;
use Exception;

class AdminController {
    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::getConnection();

        // Fetch all products
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch dynamic categories with product counts
        $categoriesList = getCategoriesWithCounts($pdo);
        if (empty($categoriesList)) {
            $categoriesList = [
                ['id' => 1, 'name' => 'CV Kreatif', 'count' => 0],
                ['id' => 2, 'name' => 'CV ATS', 'count' => 0],
                ['id' => 3, 'name' => 'Surat Lamaran Kerja', 'count' => 0]
            ];
        }

        // Calculate category counts map
        $stats = [
            'total' => count($products),
            'kreatif' => 0,
            'ats' => 0,
            'lamaran' => 0
        ];

        foreach ($categoriesList as $cItem) {
            $cNameLower = strtolower($cItem['name']);
            if (strpos($cNameLower, 'kreatif') !== false) {
                $stats['kreatif'] = (int)$cItem['count'];
            } elseif (strpos($cNameLower, 'ats') !== false) {
                $stats['ats'] = (int)$cItem['count'];
            } elseif (strpos($cNameLower, 'lamaran') !== false) {
                $stats['lamaran'] = (int)$cItem['count'];
            }
        }

        $request = Flight::request();
        $flashMsg = trim($request->query->msg ?? '');
        $adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
        $adminInitial = strtoupper(substr($adminUsername, 0, 1));

        // Load version.json
        $versionFile = dirname(__DIR__, 2) . '/version.json';
        $versionData = file_exists($versionFile) ? json_decode(file_get_contents($versionFile), true) : [];
        $sidebarRepo = $versionData['github_repo'] ?? 'Theseadev/SEACV';
        $sidebarBranch = $versionData['github_branch'] ?? 'main';
        $sidebarCommit = substr($versionData['current_commit'] ?? '5540ac8', 0, 7);

        Flight::render('admin/dashboard', [
            'products' => $products,
            'categoriesList' => $categoriesList,
            'stats' => $stats,
            'flashMsg' => $flashMsg,
            'adminUsername' => $adminUsername,
            'adminInitial' => $adminInitial,
            'sidebarRepo' => $sidebarRepo,
            'sidebarBranch' => $sidebarBranch,
            'sidebarCommit' => $sidebarCommit,
            'pdo' => $pdo
        ]);
    }

    public function upgrade(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::getConnection();
        $adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
        $adminInitial = strtoupper(substr($adminUsername, 0, 1));

        $versionFile = dirname(__DIR__, 2) . '/version.json';
        $versionData = file_exists($versionFile) ? json_decode(file_get_contents($versionFile), true) : [];
        $sidebarRepo = $versionData['github_repo'] ?? 'Theseadev/SEACV';
        $sidebarBranch = $versionData['github_branch'] ?? 'main';
        $sidebarCommit = substr($versionData['current_commit'] ?? '5540ac8', 0, 7);

        Flight::render('admin/upgrade', [
            'adminUsername' => $adminUsername,
            'adminInitial' => $adminInitial,
            'sidebarRepo' => $sidebarRepo,
            'sidebarBranch' => $sidebarBranch,
            'sidebarCommit' => $sidebarCommit,
            'versionData' => $versionData,
            'pdo' => $pdo
        ]);
    }
}
