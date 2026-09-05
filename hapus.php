<?php
// public/hapus.php
require_once 'auth.php';
require_once 'config.php';

// Check if ID parameter exists
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'];

// Get image path before deleting record
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if($product) {
    // Delete the product from database
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    // Try to delete the image file
    if(file_exists($product['image'])) {
        @unlink($product['image']);
    }
}

// Redirect back to admin page
header("Location: admin.php?msg=deleted");
exit;
?>