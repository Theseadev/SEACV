<?php
// public/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    require_once __DIR__ . '/helpers.php';
    header("Location: " . url('/seaadmin'));
    exit;
}
?>