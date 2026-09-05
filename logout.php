<?php
// logout.php - Backward compatibility entrypoint for Logout
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

(new \App\Controllers\AuthController())->logout();