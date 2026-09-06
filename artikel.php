<?php
/**
 * artikel.php - Standalone entry point & Flight route bridge for SeaCV Articles
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

// Configure Flight views if not already set
Flight::set('flight.views.path', __DIR__ . '/views');
Flight::set('flight.views.extension', '.php');

// Register db
Flight::register('db', \App\Services\Database::class);

// Direct request handling
$slug = trim($_GET['slug'] ?? '');

$controller = new \App\Controllers\ArticleController();

if (!empty($slug)) {
    $controller->show($slug);
} else {
    $controller->index();
}
