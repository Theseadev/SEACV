<?php
/**
 * -------------------------------------------------------------------------
 * SEACV - PREMIUM PROFESSIONAL RESUME & CV WRITING SERVICES PLATFORM
 * Powered by Flight PHP Framework (Ultra-Lightweight & Fast MVC)
 * -------------------------------------------------------------------------
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Autoload composer dependencies & project classes
require_once __DIR__ . '/vendor/autoload.php';

// Load shared helpers
require_once __DIR__ . '/helpers.php';

// Configure Flight views
Flight::set('flight.views.path', __DIR__ . '/views');
Flight::set('flight.views.extension', '.php');

// Register Database Service
Flight::register('db', \App\Services\Database::class);

// Load centralized route definitions
require_once __DIR__ . '/routes.php';

// Start Flight micro-framework engine
Flight::start();