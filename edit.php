<?php
// public/edit.php
require_once 'auth.php';
require_once 'config.php';
require_once 'helpers.php';

// Check if ID parameter exists
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = (int)$_GET['id'];

// Get product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: admin.php");
    exit;
}

$error = '';
$adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
$adminInitial = strtoupper(substr($adminUsername, 0, 1));
$displayName = resolveProductDisplayName($product);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = $_POST['category'] ?? '';
    
    if (empty($name) || empty($price) || empty($category)) {
        $error = "Semua field wajib diisi.";
    } elseif (!is_numeric($price)) {
        $error = "Harga harus berupa angka nominal valid tanpa titik/koma.";
    } else {
        // Check if a new image was uploaded
        if (!empty($_FILES['image']['name'])) {
            $image_name = $_FILES['image']['name'];
            $image_tmp  = $_FILES['image']['tmp_name'];
            $image_ext  = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($image_ext, $allowed_extensions)) {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                $clean_filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $image_name);
                $image_path = 'uploads/' . uniqid() . '_' . $clean_filename;
                
                if (move_uploaded_file($image_tmp, $image_path)) {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, image = ? WHERE id = ?");
                    $stmt->execute([$name, $price, $category, $image_path, $id]);
                    
                    // Remove old image if file exists
                    if (!empty($product['image']) && file_exists($product['image']) && $product['image'] !== $image_path) {
                        @unlink($product['image']);
                    }
                    
                    header("Location: admin.php?msg=updated");
                    exit;
                } else {
                    $error = "Gagal memproses upload file gambar baru.";
                }
            } else {
                $error = "Format gambar tidak didukung. Harap gunakan format JPG, PNG, atau WEBP.";
            }
        } else {
            // Update product without changing the image
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ? WHERE id = ?");
            $stmt->execute([$name, $price, $category, $id]);
            
            header("Location: admin.php?msg=updated");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= htmlspecialchars($displayName) ?> - SeaCV Admin</title>
    <link rel="icon" type="image/png" href="logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --adm-bg: #f8fafc;
            --adm-surface: #ffffff;
            --adm-subtle: #f1f5f9;
            --adm-border: #e2e8f0;
            --adm-text-main: #0f172a;
            --adm-text-sub: #475569;
            --adm-text-muted: #94a3b8;
            --adm-primary: #2563eb;
            --adm-primary-hover: #1d4ed8;
            --adm-primary-soft: #eff6ff;
            --adm-primary-border: #bfdbfe;
            --adm-danger: #dc2626;
            --adm-danger-soft: #fef2f2;
            --adm-danger-border: #fecaca;
            --adm-success: #16a34a;
            --adm-radius-sm: 6px;
            --adm-radius-md: 10px;
            --adm-radius-lg: 14px;
            --adm-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --adm-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --adm-font-display: 'Outfit', sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body.admin-body {
            background-color: var(--adm-bg);
            color: var(--adm-text-main);
            font-family: var(--adm-font);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            background: var(--adm-surface);
            border-right: 1px solid var(--adm-border);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 100;
            flex-shrink: 0;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-layout.sidebar-collapsed .admin-sidebar {
            margin-left: -250px;
        }

        .sidebar-header {
            height: 64px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--adm-border);
            flex-shrink: 0;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--adm-text-main);
        }

        .brand-logo {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .brand-text {
            font-family: var(--adm-font-display);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .brand-text .sea { color: #0f172a; }
        .brand-text .cv { color: var(--adm-primary); }

        .brand-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--adm-subtle);
            color: var(--adm-text-sub);
            padding: 2px 6px;
            border-radius: var(--adm-radius-sm);
            margin-left: 4px;
        }

        .btn-sidebar-close {
            display: none;
            background: none;
            border: none;
            color: var(--adm-text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
        }

        .btn-sidebar-close:hover {
            color: var(--adm-text-main);
            background: var(--adm-subtle);
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 14px;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--adm-text-muted);
            padding: 0 10px;
            margin-bottom: 8px;
            margin-top: 14px;
        }

        .nav-section-title:first-child {
            margin-top: 0;
        }

        .sidebar-menu {
            list-style: none;
            margin-bottom: 12px;
        }

        .sidebar-menu li {
            margin-bottom: 3px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--adm-radius-sm);
            color: var(--adm-text-sub);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar-link:hover {
            background: var(--adm-subtle);
            color: var(--adm-text-main);
        }

        .sidebar-link.active {
            background: var(--adm-primary-soft);
            color: var(--adm-primary);
            font-weight: 600;
        }

        .sidebar-icon {
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: inherit;
        }

        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fafbfc;
            flex-shrink: 0;
        }

        .sidebar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--adm-primary-soft);
            color: var(--adm-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid var(--adm-primary-border);
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--adm-text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .sidebar-user-role {
            font-size: 11.5px;
            color: var(--adm-text-muted);
            line-height: 1.2;
            margin-top: 2px;
        }

        .btn-sidebar-logout {
            width: 32px;
            height: 32px;
            border-radius: var(--adm-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--adm-text-muted);
            border: 1px solid var(--adm-border);
            background: var(--adm-surface);
            text-decoration: none;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .btn-sidebar-logout:hover {
            background: var(--adm-danger-soft);
            color: var(--adm-danger);
            border-color: var(--adm-danger-border);
        }

        /* Main */
        .admin-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: var(--adm-bg);
        }

        .admin-topbar {
            height: 64px;
            background: var(--adm-surface);
            border-bottom: 1px solid var(--adm-border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: var(--adm-shadow-sm);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-burger {
            width: 36px;
            height: 36px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: var(--adm-surface);
            color: var(--adm-text-sub);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-burger:hover {
            background: var(--adm-subtle);
            color: var(--adm-text-main);
            border-color: #cbd5e1;
        }

        .topbar-page-title {
            font-family: var(--adm-font-display);
            font-size: 16px;
            font-weight: 700;
            color: var(--adm-text-main);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-view-store {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            color: var(--adm-text-sub);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-view-store:hover {
            background: var(--adm-subtle);
            color: var(--adm-text-main);
        }

        /* Content Container */
        .admin-content {
            padding: 28px 24px 60px;
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--adm-text-muted);
            margin-bottom: 24px;
        }

        .breadcrumb a {
            color: var(--adm-text-sub);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: var(--adm-primary);
        }

        .breadcrumb-sep {
            color: var(--adm-text-muted);
        }

        .form-card {
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-lg);
            padding: 32px;
            box-shadow: var(--adm-shadow-sm);
        }

        .form-card-header {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--adm-border);
        }

        .form-card-title {
            font-family: var(--adm-font-display);
            font-size: 22px;
            font-weight: 800;
            color: var(--adm-text-main);
            letter-spacing: -0.01em;
        }

        .form-card-desc {
            font-size: 13.5px;
            color: var(--adm-text-sub);
            margin-top: 4px;
        }

        .alert-error {
            padding: 12px 16px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-danger-soft);
            border: 1px solid var(--adm-danger-border);
            color: var(--adm-danger);
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .form-card {
                padding: 20px;
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--adm-text-main);
            margin-bottom: 7px;
        }

        .form-hint {
            font-size: 12px;
            color: var(--adm-text-muted);
            margin-top: 5px;
            display: block;
        }

        .form-input, .form-select {
            width: 100%;
            height: 42px;
            padding: 0 14px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: var(--adm-surface);
            color: var(--adm-text-main);
            font-family: var(--adm-font);
            font-size: 14px;
            transition: all 0.15s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--adm-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .currency-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .currency-prefix {
            position: absolute;
            left: 14px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--adm-text-muted);
            pointer-events: none;
        }

        .form-input.has-prefix {
            padding-left: 42px;
            font-weight: 600;
        }

        .price-readable {
            font-size: 13px;
            color: var(--adm-primary);
            font-weight: 700;
            margin-top: 6px;
            display: block;
        }

        .current-image-box {
            background: #fafbfc;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-md);
            padding: 16px;
            text-align: center;
            margin-bottom: 14px;
        }

        .current-image-box img {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: #ffffff;
        }

        .current-image-meta {
            font-size: 12px;
            color: var(--adm-text-muted);
            margin-top: 8px;
            word-break: break-all;
        }

        .dropzone {
            border: 2px dashed var(--adm-border);
            border-radius: var(--adm-radius-md);
            padding: 24px 20px;
            text-align: center;
            cursor: pointer;
            background: #fafbfc;
            transition: all 0.15s ease;
            position: relative;
        }

        .dropzone:hover {
            border-color: var(--adm-primary);
            background: var(--adm-primary-soft);
        }

        .dropzone input[type="file"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .dropzone-icon {
            color: var(--adm-text-muted);
            margin-bottom: 8px;
        }

        .dropzone-title {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--adm-text-main);
        }

        .dropzone-sub {
            font-size: 12px;
            color: var(--adm-text-muted);
            margin-top: 3px;
        }

        .preview-box {
            display: none;
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-md);
            background: #f8fafc;
            padding: 16px;
            text-align: center;
            margin-top: 10px;
        }

        .preview-box img {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: #ffffff;
        }

        .preview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--adm-success);
        }

        .btn-change-img {
            background: none;
            border: none;
            color: var(--adm-danger);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .form-actions {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-primary);
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-submit:hover {
            background: var(--adm-primary-hover);
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            color: var(--adm-text-sub);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-cancel:hover {
            background: var(--adm-subtle);
            color: var(--adm-text-main);
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(2px);
            z-index: 95;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        @media (max-width: 1024px) {
            .admin-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                margin-left: 0 !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
            }
            
            .btn-sidebar-close {
                display: flex;
            }
            
            .admin-layout.mobile-sidebar-open .admin-sidebar {
                transform: translateX(0);
            }
            
            .admin-layout.mobile-sidebar-open .sidebar-backdrop {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>
<body class="admin-body">

    <div class="admin-layout" id="adminLayout">

        <!-- Mobile Dark Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebarMobile()"></div>

        <!-- Left Admin Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <!-- Sidebar Header / Brand -->
            <div class="sidebar-header">
                <a href="admin.php" class="brand-link">
                    <img src="logo.png" alt="SeaCV Logo" class="brand-logo">
                    <span class="brand-text">
                        <span class="sea">SEA</span><span class="cv">CV</span>
                    </span>
                    <span class="brand-badge">Admin</span>
                </a>

                <button type="button" class="btn-sidebar-close" onclick="closeSidebarMobile()" title="Tutup Sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="sidebar-nav">
                <div class="nav-section-title">Menu Utama</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="admin.php" class="sidebar-link">
                            <span class="sidebar-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </span>
                            <span>Katalog Template</span>
                        </a>
                    </li>
                    <li>
                        <a href="addproduct.php" class="sidebar-link">
                            <span class="sidebar-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                            </span>
                            <span>Tambah Template</span>
                        </a>
                    </li>
                    <li>
                        <a href="upgrade.php" class="sidebar-link">
                            <span class="sidebar-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="16 16 12 12 8 16"></polyline>
                                    <line x1="12" y1="12" x2="12" y2="21"></line>
                                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                                    <polyline points="16 16 12 12 8 16"></polyline>
                                </svg>
                            </span>
                            <span>Pembaruan Sistem</span>
                        </a>
                    </li>
                </ul>

                <div class="nav-section-title">Shortcut Toko</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="index.php" target="_blank" class="sidebar-link">
                            <span class="sidebar-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                            </span>
                            <span>Buka Toko SeaCV</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: auto; opacity: 0.5;">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Sidebar Bottom / Admin Profile -->
            <div class="sidebar-footer">
                <div class="sidebar-avatar"><?= $adminInitial ?></div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= $adminUsername ?></div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
                <a href="logout.php" class="btn-sidebar-logout" title="Logout dari Admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </aside>

        <!-- Right Main Column -->
        <div class="admin-main">
            <!-- Top Header Bar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button type="button" class="btn-burger" onclick="toggleSidebar()" title="Buka/Tutup Sidebar (Burger Menu)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="topbar-page-title">Edit Template</div>
                </div>

                <div class="topbar-right">
                    <a href="index.php" target="_blank" class="btn-view-store" title="Buka Toko SeaCV">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <span>Lihat Toko</span>
                    </a>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="admin-content">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="admin.php">Dashboard</a>
                    <span class="breadcrumb-sep">/</span>
                    <span>Edit <?= htmlspecialchars($displayName) ?></span>
                </div>

                <div class="form-card">
                    <div class="form-card-header">
                        <h1 class="form-card-title">Edit Template: <?= htmlspecialchars($displayName) ?></h1>
                        <p class="form-card-desc">ID Produk #<?= $product['id'] ?> • Perbarui rincian template atau unggah file foto pengganti.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert-error">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="edit.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
                        <div class="form-grid">
                            <!-- Left Column -->
                            <div>
                                <div class="form-group">
                                    <label class="form-label" for="name">Nama Template</label>
                                    <input type="text" id="name" name="name" class="form-input" 
                                           value="<?= htmlspecialchars($product['name']) ?>" required>
                                    <span class="form-hint">Nama template terdaftar</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="category">Kategori Template</label>
                                    <select id="category" name="category" class="form-select" required>
                                        <option value="CV Kreatif" <?= $product['category'] === 'CV Kreatif' ? 'selected' : '' ?>>CV Kreatif</option>
                                        <option value="CV ATS" <?= $product['category'] === 'CV ATS' ? 'selected' : '' ?>>CV ATS Friendly</option>
                                        <option value="Surat Lamaran Kerja" <?= $product['category'] === 'Surat Lamaran Kerja' ? 'selected' : '' ?>>Surat Lamaran Kerja</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="price">Harga Template (Rp)</label>
                                    <div class="currency-wrap">
                                        <span class="currency-prefix">Rp</span>
                                        <input type="number" id="price" name="price" class="form-input has-prefix" 
                                               value="<?= htmlspecialchars($product['price']) ?>" required>
                                    </div>
                                    <span id="priceFormatted" class="price-readable">
                                        Terbaca: Rp <?= number_format($product['price'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Right Column: Image Management -->
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Foto Template Saat Ini</label>
                                    
                                    <div class="current-image-box">
                                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='logo.png'">
                                        <div class="current-image-meta"><?= basename($product['image']) ?></div>
                                    </div>

                                    <label class="form-label" style="font-size: 12.5px; color: var(--adm-text-sub); margin-top: 14px;">
                                        Ganti Foto Template (Opsional):
                                    </label>
                                    
                                    <div class="dropzone" id="dropzoneContainer">
                                        <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                                        <div class="dropzone-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>
                                        <div class="dropzone-title">Pilih Foto Baru</div>
                                        <div class="dropzone-sub">JPG, PNG, WEBP (Menggantikan foto lama saat disimpan)</div>
                                    </div>

                                    <!-- Live New Image Preview Box -->
                                    <div id="newImagePreviewBox" class="preview-box">
                                        <div class="preview-top">
                                            <span>Foto Baru Terpilih:</span>
                                            <button type="button" id="cancelNewImageBtn" class="btn-change-img">Batalkan</button>
                                        </div>
                                        <img id="newImagePreviewImg" src="" alt="Pratinjau Baru">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="admin.php" class="btn-cancel">Batal</a>
                            <button type="submit" class="btn-submit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sidebar Burger Toggle Logic
        const adminLayout = document.getElementById('adminLayout');

        if (window.innerWidth > 1024) {
            const savedState = localStorage.getItem('seacv_admin_sidebar');
            if (savedState === 'collapsed') {
                adminLayout.classList.add('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            if (window.innerWidth <= 1024) {
                adminLayout.classList.toggle('mobile-sidebar-open');
            } else {
                adminLayout.classList.toggle('sidebar-collapsed');
                const isCollapsed = adminLayout.classList.contains('sidebar-collapsed');
                localStorage.setItem('seacv_admin_sidebar', isCollapsed ? 'collapsed' : 'expanded');
            }
        }

        function closeSidebarMobile() {
            adminLayout.classList.remove('mobile-sidebar-open');
        }

        // Rupiah Live Formatter
        const priceInput = document.getElementById('price');
        const priceFormatted = document.getElementById('priceFormatted');

        function updatePriceText() {
            const val = parseInt(priceInput.value, 10);
            if (!isNaN(val) && val > 0) {
                priceFormatted.textContent = 'Terbaca: Rp ' + val.toLocaleString('id-ID');
            } else {
                priceFormatted.textContent = 'Masukkan nominal harga yang valid';
            }
        }
        priceInput.addEventListener('input', updatePriceText);

        // New Image Live Preview
        const imageInput = document.getElementById('imageInput');
        const dropzoneContainer = document.getElementById('dropzoneContainer');
        const newImagePreviewBox = document.getElementById('newImagePreviewBox');
        const newImagePreviewImg = document.getElementById('newImagePreviewImg');
        const cancelNewImageBtn = document.getElementById('cancelNewImageBtn');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    newImagePreviewImg.src = e.target.result;
                    newImagePreviewBox.style.display = 'block';
                    dropzoneContainer.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        cancelNewImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            newImagePreviewImg.src = '';
            newImagePreviewBox.style.display = 'none';
            dropzoneContainer.style.display = 'block';
        });
    </script>
</body>
</html>