<?php
// routes.php - Centralized Route Definitions for SeaCV (Flight PHP)

use App\Controllers\StorefrontController;
use App\Controllers\AdminController;
use App\Controllers\ProductController;
use App\Controllers\AuthController;
use App\Controllers\ApiController;
use App\Controllers\ArticleController;
use App\Middleware\AuthMiddleware;

// -----------------------------------------------------------------------------
// 1. Storefront Routes
// -----------------------------------------------------------------------------
Flight::route('GET /', [StorefrontController::class, 'index']);
Flight::route('GET /index.php', [StorefrontController::class, 'index']);

// Article & Career Education Routes
Flight::route('GET /artikel', [ArticleController::class, 'index']);
Flight::route('GET /artikel.php', [ArticleController::class, 'index']);
Flight::route('GET /artikel/@slug', function($slug) {
    (new ArticleController())->show($slug);
});
Flight::route('GET /berita', [ArticleController::class, 'index']);
Flight::route('GET /berita.php', [ArticleController::class, 'index']);
Flight::route('GET /berita/@slug', function($slug) {
    (new ArticleController())->show($slug);
});

// -----------------------------------------------------------------------------
// 2. Authentication Routes (Secret Hidden Admin Portal: /seaadmin)
// -----------------------------------------------------------------------------
Flight::route('GET /seaadmin', [AuthController::class, 'showLogin']);
Flight::route('POST /seaadmin', [AuthController::class, 'login']);

// 404 with laughing emoji when accessing /login or /login.php
Flight::route('GET|POST /login', function() {
    Flight::response()->status(404);
    $imgUrl = function_exists('asset') ? asset('laugh_transparent.png') : 'assets/images/laugh_transparent.png';
    $homeUrl = function_exists('url') ? url('/') : '/';
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>404 - Hayolo Ngapain Haha</title><style>*{box-sizing:border-box;margin:0;padding:0;}body{font-family:sans-serif;text-align:center;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#0f172a;color:#f8fafc;}.card{background:#1e293b;border:1px solid #334155;border-radius:24px;padding:44px 28px;max-width:420px;width:100%;box-shadow:0 25px 50px -12px rgba(0,0,0,0.6);}.emoji-img{width:140px;height:140px;object-fit:contain;margin-bottom:16px;filter:drop-shadow(0 12px 18px rgba(0,0,0,0.45));animation:bounce 1.5s infinite;}@keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}.emoji{font-size:80px;margin-bottom:14px;animation:bounce 1.5s infinite;}.tag{display:inline-block;font-size:12px;font-weight:800;letter-spacing:0.08em;color:#ef4444;background:rgba(239,68,68,0.15);padding:5px 14px;border-radius:99px;margin-bottom:14px;}h1{font-size:26px;font-weight:800;margin-bottom:8px;}p{font-size:15px;color:#94a3b8;margin-bottom:28px;}a{display:inline-block;padding:13px 28px;background:#2563eb;color:#fff;text-decoration:none;font-weight:600;border-radius:12px;box-shadow:0 8px 16px rgba(37,99,235,0.4);transition:transform 0.2s;}a:hover{transform:translateY(-2px);}</style></head><body><div class="card"><img src="' . htmlspecialchars($imgUrl) . '" alt="Emoji Ketawa" class="emoji-img" onerror="this.outerHTML=\'<div class=\\\'emoji\\\'>🤣</div>\'"><div><span class="tag">404 NOT FOUND</span></div><h1>hayolo ngapain haha</h1><p>Gak ada apa-apa di sini wkwk 😜</p><a href="' . htmlspecialchars($homeUrl) . '">&larr; Balik ke Beranda</a></div></body></html>';
});
Flight::route('GET|POST /login.php', function() {
    Flight::response()->status(404);
    $imgUrl = function_exists('asset') ? asset('laugh_transparent.png') : 'assets/images/laugh_transparent.png';
    $homeUrl = function_exists('url') ? url('/') : '/';
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>404 - Hayolo Ngapain Haha</title><style>*{box-sizing:border-box;margin:0;padding:0;}body{font-family:sans-serif;text-align:center;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#0f172a;color:#f8fafc;}.card{background:#1e293b;border:1px solid #334155;border-radius:24px;padding:44px 28px;max-width:420px;width:100%;box-shadow:0 25px 50px -12px rgba(0,0,0,0.6);}.emoji-img{width:140px;height:140px;object-fit:contain;margin-bottom:16px;filter:drop-shadow(0 12px 18px rgba(0,0,0,0.45));animation:bounce 1.5s infinite;}@keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}.emoji{font-size:80px;margin-bottom:14px;animation:bounce 1.5s infinite;}.tag{display:inline-block;font-size:12px;font-weight:800;letter-spacing:0.08em;color:#ef4444;background:rgba(239,68,68,0.15);padding:5px 14px;border-radius:99px;margin-bottom:14px;}h1{font-size:26px;font-weight:800;margin-bottom:8px;}p{font-size:15px;color:#94a3b8;margin-bottom:28px;}a{display:inline-block;padding:13px 28px;background:#2563eb;color:#fff;text-decoration:none;font-weight:600;border-radius:12px;box-shadow:0 8px 16px rgba(37,99,235,0.4);transition:transform 0.2s;}a:hover{transform:translateY(-2px);}</style></head><body><div class="card"><img src="' . htmlspecialchars($imgUrl) . '" alt="Emoji Ketawa" class="emoji-img" onerror="this.outerHTML=\'<div class=\\\'emoji\\\'>🤣</div>\'"><div><span class="tag">404 NOT FOUND</span></div><h1>hayolo ngapain haha</h1><p>Gak ada apa-apa di sini wkwk 😜</p><a href="' . htmlspecialchars($homeUrl) . '">&larr; Balik ke Beranda</a></div></body></html>';
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
