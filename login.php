<?php
// login.php - Backward compatibility entrypoint for Login
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

Flight::set('flight.views.path', __DIR__ . '/views');
Flight::set('flight.views.extension', '.php');
Flight::register('db', \App\Services\Database::class);

$auth = new \App\Controllers\AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->login();
} else {
    $auth->showLogin();
}