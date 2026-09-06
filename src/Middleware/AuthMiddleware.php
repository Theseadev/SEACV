<?php
namespace App\Middleware;

use Flight;

class AuthMiddleware {
    public function before($params = []): bool {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            Flight::redirect(url('/seaadmin'));
            exit;
        }

        return true;
    }
}
