<?php
// hapus.php - Backward compatibility entrypoint for Delete Product
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

(new \App\Middleware\AuthMiddleware())->before();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
(new \App\Controllers\ProductController())->delete($id);