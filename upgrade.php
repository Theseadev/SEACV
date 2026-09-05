<?php
// upgrade.php - Backward compatibility entrypoint for System Upgrade
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

Flight::set('flight.views.path', __DIR__ . '/views');
Flight::set('flight.views.extension', '.php');

(new \App\Middleware\AuthMiddleware())->before();
(new \App\Controllers\AdminController())->upgrade();
