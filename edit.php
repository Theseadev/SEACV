<?php
// edit.php - Backward compatibility entrypoint for Edit Product
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

Flight::set('flight.views.path', __DIR__ . '/views');
Flight::set('flight.views.extension', '.php');

(new \App\Middleware\AuthMiddleware())->before();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
(new \App\Controllers\ProductController())->edit($id);