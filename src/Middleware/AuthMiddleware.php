<?php
namespace App\Middleware;

use Flight;

class AuthMiddleware {
    public function before(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            Flight::redirect(url('/login'));
            return false;
        }

        return true;
    }
}
