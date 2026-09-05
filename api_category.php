<?php
// api_category.php - Backward compatibility entrypoint for Category API
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

(new \App\Controllers\ApiController())->categories();
Flight::response()->send();
