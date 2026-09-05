<?php
// routes.php - Centralized Route Definitions for SeaCV (Flight PHP)

use App\Controllers\StorefrontController;
use App\Controllers\AdminController;
use App\Controllers\ProductController;
use App\Controllers\AuthController;
use App\Controllers\ApiController;
use App\Middleware\AuthMiddleware;

// -----------------------------------------------------------------------------
// 1. Storefront Routes
// -----------------------------------------------------------------------------
Flight::route('GET /', [StorefrontController::class, 'index']);
Flight::route('GET /index.php', [StorefrontController::class, 'index']);

// -----------------------------------------------------------------------------
// 2. Authentication Routes
// -----------------------------------------------------------------------------
Flight::route('GET /login', [AuthController::class, 'showLogin']);
Flight::route('POST /login', [AuthController::class, 'login']);
Flight::route('GET|POST /login.php', function() {
    $auth = new AuthController();
    if (Flight::request()->method === 'POST') {
        $auth->login();
    } else {
        $auth->showLogin();
    }
});
Flight::route('GET /logout', [AuthController::class, 'logout']);
Flight::route('GET /logout.php', [AuthController::class, 'logout']);

// -----------------------------------------------------------------------------
// 3. Admin Routes (Protected by AuthMiddleware)
// -----------------------------------------------------------------------------
$authMiddleware = new AuthMiddleware();

Flight::group('/admin', function() {
    Flight::route('GET /', [AdminController::class, 'index']);
    Flight::route('GET /upgrade', [AdminController::class, 'upgrade']);
    
    // Product Management
    Flight::route('GET /products/add', [ProductController::class, 'add']);
    Flight::route('POST /products/add', [ProductController::class, 'add']);
    Flight::route('GET /products/edit/@id:[0-9]+', function($id) {
        (new ProductController())->edit((int)$id);
    });
    Flight::route('POST /products/edit/@id:[0-9]+', function($id) {
        (new ProductController())->edit((int)$id);
    });
    Flight::route('GET|POST /products/delete/@id:[0-9]+', function($id) {
        (new ProductController())->delete((int)$id);
    });
}, [$authMiddleware]);

// -----------------------------------------------------------------------------
// 4. Legacy Admin File Aliases (100% Backward Compatibility)
// -----------------------------------------------------------------------------
Flight::route('GET /admin.php', function() use ($authMiddleware) {
    $authMiddleware->before();
    (new AdminController())->index();
});

Flight::route('GET|POST /addproduct.php', function() use ($authMiddleware) {
    $authMiddleware->before();
    (new ProductController())->add();
});

Flight::route('GET|POST /edit.php', function() use ($authMiddleware) {
    $authMiddleware->before();
    $id = (int)(Flight::request()->query->id ?? Flight::request()->data->id ?? 0);
    (new ProductController())->edit($id);
});

Flight::route('GET|POST /hapus.php', function() use ($authMiddleware) {
    $authMiddleware->before();
    $id = (int)(Flight::request()->query->id ?? Flight::request()->data->id ?? 0);
    (new ProductController())->delete($id);
});

Flight::route('GET /upgrade.php', function() use ($authMiddleware) {
    $authMiddleware->before();
    (new AdminController())->upgrade();
});

// -----------------------------------------------------------------------------
// 5. API Endpoints
// -----------------------------------------------------------------------------
Flight::route('GET|POST /api/categories', [ApiController::class, 'categories']);
Flight::route('GET|POST /api_category.php', [ApiController::class, 'categories']);
Flight::route('GET|POST /api/upgrade', [ApiController::class, 'upgrade']);
Flight::route('GET|POST /api_upgrade.php', [ApiController::class, 'upgrade']);

// -----------------------------------------------------------------------------
// 6. 404 Handler
// -----------------------------------------------------------------------------
Flight::map('notFound', function() {
    Flight::response()->status(404);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404 - Halaman Tidak Ditemukan</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#1e293b;}h1{font-size:48px;margin-bottom:10px;}p{font-size:18px;color:#64748b;}a{color:#2563eb;text-decoration:none;font-weight:600;}</style></head><body><h1>404</h1><p>Halaman yang Anda tuju tidak ditemukan.</p><p><a href="' . htmlspecialchars(url('/')) . '">&larr; Kembali ke Beranda</a></p></body></html>';
});
