<?php
// public/admin.php
require_once "auth.php";
require_once "config.php";
require_once "helpers.php";

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// Calculate category counts
$stats = [
    'total' => count($products),
    'kreatif' => 0,
    'ats' => 0,
    'lamaran' => 0
];

foreach ($products as $p) {
    $cat = strtolower($p['category']);
    if (strpos($cat, 'kreatif') !== false) {
        $stats['kreatif']++;
    } elseif (strpos($cat, 'ats') !== false) {
        $stats['ats']++;
    } elseif (strpos($cat, 'lamaran') !== false) {
        $stats['lamaran']++;
    }
}

$flashMsg = isset($_GET['msg']) ? trim($_GET['msg']) : '';
$adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
$adminInitial = strtoupper(substr($adminUsername, 0, 1));

// Load version.json for sidebar repo info
$versionFile = __DIR__ . '/version.json';
$versionData = file_exists($versionFile) ? json_decode(file_get_contents($versionFile), true) : [];
$sidebarRepo = $versionData['github_repo'] ?? 'Theseadev/SEACV';
$sidebarBranch = $versionData['github_branch'] ?? 'main';
$sidebarCommit = substr($versionData['current_commit'] ?? '5540ac8', 0, 7);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Template - SeaCV Admin</title>
    <link rel="icon" type="image/png" href="logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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

        /* Layout Structure */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* --------------------------------------------------------------------------
           Sidebar Navigation (Modern, Sleek, Professional)
           -------------------------------------------------------------------------- */
        .admin-sidebar {
            width: 260px;
            background: #ffffff;
            border-right: 1px solid var(--adm-border);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 100;
            flex-shrink: 0;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.02);
        }

        /* Collapsed State on Desktop */
        .admin-layout.sidebar-collapsed .admin-sidebar {
            margin-left: -260px;
        }

        .sidebar-header {
            height: 68px;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--adm-border);
            flex-shrink: 0;
            background: #ffffff;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--adm-text-main);
        }

        .brand-logo {
            height: 34px;
            width: auto;
            object-fit: contain;
        }

        .brand-text {
            font-family: var(--adm-font-display);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.01em;
            line-height: 1.1;
        }

        .brand-text .sea { color: #0f172a; }
        .brand-text .cv { color: #2563eb; }

        .brand-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: linear-gradient(135deg, #2563eb 0%, #6366f1 100%);
            color: #ffffff;
            padding: 2px 7px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        }

        .sidebar-server-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 600;
            color: #059669;
            margin-top: 2px;
        }

        .status-pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
            animation: pulseGlow 2s infinite;
        }

        .btn-sidebar-close {
            display: none;
            background: none;
            border: none;
            color: var(--adm-text-muted);
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
        }

        .btn-sidebar-close:hover {
            color: var(--adm-text-main);
            background: var(--adm-subtle);
        }

        /* Sidebar Nav Links */
        .sidebar-nav {
            flex: 1;
            padding: 14px 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 999px;
        }

        .nav-section-title {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 0 8px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 10px;
            color: #475569;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid transparent;
        }

        .sidebar-link:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #f1f5f9;
        }

        .sidebar-link.active {
            background: #f0f7ff;
            color: #1d4ed8;
            border-color: #dbeafe;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.05);
        }

        /* Colorful Icon Boxes */
        .sidebar-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link:hover .sidebar-icon-box {
            transform: scale(1.08);
        }

        .icon-blue {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }
        .sidebar-link.active .icon-blue {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
        }

        .icon-emerald {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }
        .sidebar-link.active .icon-emerald {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35);
        }

        .icon-purple {
            background: #f5f3ff;
            color: #7c3aed;
            border: 1px solid #ede9fe;
        }
        .sidebar-link.active .icon-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.35);
        }

        .icon-cyan {
            background: #ecfeff;
            color: #0891b2;
            border: 1px solid #cffafe;
        }
        .sidebar-link.active .icon-cyan {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(8, 145, 178, 0.35);
        }

        .icon-amber {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }
        .sidebar-link.active .icon-amber {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(217, 119, 6, 0.35);
        }

        .icon-indigo {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #e0e7ff;
        }
        .sidebar-link.active .icon-indigo {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
        }

        .icon-orange {
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #ffedd5;
        }

        /* Vibrant Badges */
        .sidebar-badge {
            margin-left: auto;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
            letter-spacing: 0.02em;
        }

        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-emerald { background: #d1fae5; color: #065f46; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .badge-cyan { background: #cffafe; color: #155e75; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-indigo { background: #e0e7ff; color: #3730a3; }
        .badge-orange { background: #ffedd5; color: #9a3412; }

        .pulse-dot-purple {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #7c3aed;
            margin-right: 4px;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.3);
            animation: pulseGlow 1.8s infinite;
        }

        @keyframes pulseGlow {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        /* Mini Template Composition Bar */
        .sidebar-stat-mini {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
        }

        .ssm-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .ssm-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .ssm-val {
            font-size: 11.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .ssm-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            display: flex;
            margin-bottom: 6px;
        }

        .ssm-fill.ats { background: #06b6d4; }
        .ssm-fill.kreatif { background: #f59e0b; }
        .ssm-fill.lamaran { background: #6366f1; }

        .ssm-legend {
            display: flex;
            justify-content: space-between;
            font-size: 10.5px;
            font-weight: 600;
            color: #64748b;
        }

        .ssm-legend span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .ssm-legend .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .ssm-legend .dot-cyan { background: #06b6d4; }
        .ssm-legend .dot-amber { background: #f59e0b; }
        .ssm-legend .dot-indigo { background: #6366f1; }

        /* Sidebar System Status Card */
        .sidebar-system-card {
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
            border-radius: 12px;
            padding: 12px;
            color: #ffffff;
            box-shadow: 0 8px 16px -4px rgba(15, 23, 42, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: auto;
        }

        .ssc-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .ssc-git-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            color: #c084fc;
            background: rgba(192, 132, 252, 0.12);
            border: 1px solid rgba(192, 132, 252, 0.25);
            padding: 2px 7px;
            border-radius: 6px;
        }

        .ssc-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .ssc-repo-name {
            font-family: var(--adm-font-mono, monospace);
            font-size: 12px;
            font-weight: 600;
            color: #f8fafc;
            letter-spacing: -0.01em;
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ssc-info-row {
            display: flex;
            gap: 5px;
            margin-bottom: 9px;
            flex-wrap: wrap;
        }

        .ssc-branch-pill {
            font-size: 10px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            padding: 1px 6px;
            border-radius: 4px;
        }

        .ssc-shield-pill {
            font-size: 10px;
            font-weight: 600;
            background: rgba(20, 184, 166, 0.15);
            color: #2dd4bf;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid rgba(45, 212, 191, 0.2);
        }

        .ssc-upgrade-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 6px 10px;
            border-radius: 7px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .ssc-upgrade-btn:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        /* Sidebar Profile Footer */
        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            flex-shrink: 0;
        }

        .sidebar-avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 3px 8px rgba(59, 130, 246, 0.25);
        }

        .sidebar-online-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 9px;
            height: 9px;
            background: #10b981;
            border: 2px solid #ffffff;
            border-radius: 50%;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .sidebar-user-role {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            color: #7c3aed;
            background: #f5f3ff;
            border: 1px solid #ede9fe;
            padding: 1px 6px;
            border-radius: 4px;
            margin-top: 2px;
            line-height: 1.2;
        }

        .btn-sidebar-logout {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            border: 1px solid #fecaca;
            background: #fff5f5;
            text-decoration: none;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .btn-sidebar-logout:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            transform: scale(1.05);
        }

        /* --------------------------------------------------------------------------
           Main Content Area
           -------------------------------------------------------------------------- */
        .admin-main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: var(--adm-bg);
        }

        /* Top Header Bar */
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

        /* Burger Menu Button (Toggle Sidebar) */
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

        .btn-add-quick {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s ease;
        }

        .btn-add-quick:hover {
            background: var(--adm-primary-hover);
        }

        /* Main Scrollable Content */
        .admin-content {
            padding: 28px 24px 60px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .page-title-group h1 {
            font-family: var(--adm-font-display);
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--adm-text-main);
        }

        .page-title-group p {
            font-size: 13.5px;
            color: var(--adm-text-sub);
            margin-top: 3px;
        }

        /* --------------------------------------------------------------------------
           KPI Summary Tiles (Clean, Crisp, Minimalist)
           -------------------------------------------------------------------------- */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-md);
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .kpi-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--adm-shadow-sm);
        }

        .kpi-card.active {
            border-color: var(--adm-primary);
            background: #fcfdfe;
        }

        .kpi-title {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--adm-text-sub);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .kpi-num {
            font-family: var(--adm-font-display);
            font-size: 26px;
            font-weight: 800;
            color: var(--adm-text-main);
            line-height: 1.1;
            margin-top: 6px;
        }

        /* --------------------------------------------------------------------------
           Catalog Card & Table
           -------------------------------------------------------------------------- */
        .catalog-card {
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-lg);
            box-shadow: var(--adm-shadow-sm);
            overflow: hidden;
        }

        .catalog-toolbar {
            padding: 14px 18px;
            border-bottom: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 6px 12px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid transparent;
            background: transparent;
            color: var(--adm-text-sub);
            font-family: var(--adm-font);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tab:hover {
            background: var(--adm-subtle);
            color: var(--adm-text-main);
        }

        .filter-tab.active {
            background: var(--adm-subtle);
            border-color: var(--adm-border);
            color: var(--adm-text-main);
            font-weight: 600;
        }

        .filter-count {
            font-size: 11px;
            padding: 1px 6px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.05);
        }

        /* Search Box */
        .search-wrapper {
            position: relative;
            min-width: 250px;
            max-width: 320px;
            flex: 1;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--adm-text-muted);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 36px;
            padding: 0 32px 0 34px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: #ffffff;
            font-family: var(--adm-font);
            font-size: 13px;
            color: var(--adm-text-main);
            transition: all 0.15s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--adm-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--adm-text-muted);
            cursor: pointer;
            font-size: 14px;
            display: none;
        }

        /* Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .catalog-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .catalog-table thead {
            background: #fafbfc;
            border-bottom: 1px solid var(--adm-border);
        }

        .catalog-table th {
            padding: 12px 18px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--adm-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }

        .catalog-table td {
            padding: 12px 18px;
            border-bottom: 1px solid var(--adm-border);
            vertical-align: middle;
            font-size: 13.5px;
            color: var(--adm-text-main);
        }

        .catalog-table tbody tr:hover td {
            background-color: #fafbfc;
        }

        .catalog-table tbody tr:last-child td {
            border-bottom: none;
        }

        .td-id {
            color: var(--adm-text-muted);
            font-size: 12.5px;
            font-variant-numeric: tabular-nums;
            width: 60px;
        }

        .td-product {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 250px;
        }

        .table-thumb {
            width: 48px;
            height: 64px;
            min-width: 48px;
            max-width: 48px;
            border-radius: var(--adm-radius-sm);
            object-fit: cover;
            background: var(--adm-subtle);
            border: 1px solid var(--adm-border);
            cursor: pointer;
            flex-shrink: 0;
            transition: opacity 0.15s ease, transform 0.15s ease;
            display: block;
        }

        .table-thumb:hover {
            opacity: 0.85;
            transform: scale(1.04);
        }

        .product-info {
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 700;
            color: var(--adm-text-main);
            font-size: 14px;
        }

        .product-meta {
            font-size: 12px;
            color: var(--adm-text-muted);
            margin-top: 2px;
        }

        .badge-category {
            display: inline-block;
            padding: 3px 9px;
            border-radius: var(--adm-radius-sm);
            font-size: 12px;
            font-weight: 500;
            background: var(--adm-subtle);
            color: var(--adm-text-sub);
            border: 1px solid var(--adm-border);
            white-space: nowrap;
        }

        .td-price {
            font-weight: 700;
            color: var(--adm-text-main);
            font-variant-numeric: tabular-nums;
            font-size: 14px;
            white-space: nowrap;
        }

        .td-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-edit {
            padding: 5px 12px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: var(--adm-surface);
            color: var(--adm-text-sub);
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-edit:hover {
            background: var(--adm-subtle);
            color: var(--adm-text-main);
        }

        .btn-delete {
            padding: 5px 12px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid transparent;
            background: transparent;
            color: var(--adm-text-muted);
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-delete:hover {
            background: var(--adm-danger-soft);
            color: var(--adm-danger);
            border-color: var(--adm-danger-border);
        }

        /* Footer / Empty State */
        .table-footer {
            padding: 12px 18px;
            border-top: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: var(--adm-text-muted);
        }

        .empty-state {
            padding: 48px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .empty-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--adm-text-main);
        }

        .empty-desc {
            font-size: 13px;
            color: var(--adm-text-muted);
            margin-top: 4px;
        }

        .btn-reset {
            margin-top: 14px;
            padding: 6px 14px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-subtle);
            border: 1px solid var(--adm-border);
            color: var(--adm-text-sub);
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
        }

        /* --------------------------------------------------------------------------
           Mobile Backdrop Overlay
           -------------------------------------------------------------------------- */
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

        /* --------------------------------------------------------------------------
           Image Lightbox Modal
           -------------------------------------------------------------------------- */
        .lightbox-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(2px);
            z-index: 200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .lightbox-modal.open {
            display: flex;
        }

        .lightbox-card {
            background: #ffffff;
            border-radius: var(--adm-radius-md);
            max-width: 540px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        }

        .lightbox-header {
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--adm-border);
        }

        .lightbox-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--adm-text-main);
        }

        .lightbox-close {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--adm-text-muted);
            cursor: pointer;
        }

        .lightbox-body {
            padding: 20px;
            background: #f8fafc;
            text-align: center;
            max-height: 75vh;
            overflow-y: auto;
        }

        .lightbox-body img {
            max-width: 100%;
            height: auto;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
        }

        /* --------------------------------------------------------------------------
           Responsive Rules
           -------------------------------------------------------------------------- */
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

            .kpi-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .kpi-row {
                grid-template-columns: 1fr 1fr;
            }

            .catalog-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrapper {
                max-width: 100%;
            }

            .btn-view-store span {
                display: none;
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
                    <div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span class="brand-text">
                                <span class="sea">SEA</span><span class="cv">CV</span>
                            </span>
                            <span class="brand-badge">ADMIN</span>
                        </div>
                        <div class="sidebar-server-status">
                            <span class="status-pulse-dot"></span>
                            <span>Server Aktif & Online</span>
                        </div>
                    </div>
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
                <!-- Section 1: Menu Utama -->
                <div>
                    <div class="nav-section-title">
                        <span>Menu Utama</span>
                    </div>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="admin.php" class="sidebar-link active">
                                <span class="sidebar-icon-box icon-blue">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                </span>
                                <span>Katalog Template</span>
                                <span class="sidebar-badge badge-blue"><?= $stats['total'] ?> item</span>
                            </a>
                        </li>
                        <li>
                            <a href="addproduct.php" class="sidebar-link">
                                <span class="sidebar-icon-box icon-emerald">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg>
                                </span>
                                <span>Tambah Template</span>
                                <span class="sidebar-badge badge-emerald">+ Baru</span>
                            </a>
                        </li>
                        <li>
                            <a href="upgrade.php" class="sidebar-link">
                                <span class="sidebar-icon-box icon-purple">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="16 16 12 12 8 16"></polyline>
                                        <line x1="12" y1="12" x2="12" y2="21"></line>
                                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                                    </svg>
                                </span>
                                <span>Pembaruan Sistem</span>
                                <span class="sidebar-badge badge-purple"><span class="pulse-dot-purple"></span> Git</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section 2: Filter Cepat Kategori -->
                <div>
                    <div class="nav-section-title">
                        <span>Filter Kategori</span>
                        <span style="font-size: 9.5px; color: #94a3b8; font-weight: 600;">Cepat</span>
                    </div>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="admin.php?cat=ats" onclick="if(typeof filterByCategory === 'function'){ event.preventDefault(); filterByCategory('ats'); }" class="sidebar-link">
                                <span class="sidebar-icon-box icon-cyan">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </span>
                                <span>CV ATS-Friendly</span>
                                <span class="sidebar-badge badge-cyan"><?= $stats['ats'] ?> ATS</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin.php?cat=kreatif" onclick="if(typeof filterByCategory === 'function'){ event.preventDefault(); filterByCategory('kreatif'); }" class="sidebar-link">
                                <span class="sidebar-icon-box icon-amber">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m4.93 4.93 4.24 4.24"></path>
                                        <path d="m14.83 9.17 4.24-4.24"></path>
                                        <path d="m14.83 14.83 4.24 4.24"></path>
                                        <path d="m9.17 14.83-4.24 4.24"></path>
                                    </svg>
                                </span>
                                <span>CV Desain Kreatif</span>
                                <span class="sidebar-badge badge-amber"><?= $stats['kreatif'] ?> CV</span>
                            </a>
                        </li>
                        <li>
                            <a href="admin.php?cat=lamaran" onclick="if(typeof filterByCategory === 'function'){ event.preventDefault(); filterByCategory('lamaran'); }" class="sidebar-link">
                                <span class="sidebar-icon-box icon-indigo">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                                <span>Surat Lamaran</span>
                                <span class="sidebar-badge badge-indigo"><?= $stats['lamaran'] ?> Dok</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section 3: Ringkasan Template Progress Bar -->
                <div class="sidebar-stat-mini">
                    <div class="ssm-header">
                        <span class="ssm-label">Rasio Koleksi</span>
                        <span class="ssm-val"><?= $stats['total'] ?> Total</span>
                    </div>
                    <div class="ssm-bar">
                        <div class="ssm-fill ats" style="width: <?= $stats['total'] ? round(($stats['ats'] / $stats['total']) * 100) : 0 ?>%;" title="ATS: <?= $stats['ats'] ?>"></div>
                        <div class="ssm-fill kreatif" style="width: <?= $stats['total'] ? round(($stats['kreatif'] / $stats['total']) * 100) : 0 ?>%;" title="Kreatif: <?= $stats['kreatif'] ?>"></div>
                        <div class="ssm-fill lamaran" style="width: <?= $stats['total'] ? round(($stats['lamaran'] / $stats['total']) * 100) : 0 ?>%;" title="Lamaran: <?= $stats['lamaran'] ?>"></div>
                    </div>
                    <div class="ssm-legend">
                        <span><i class="dot dot-cyan"></i> ATS (<?= $stats['ats'] ?>)</span>
                        <span><i class="dot dot-amber"></i> Kreatif (<?= $stats['kreatif'] ?>)</span>
                        <span><i class="dot dot-indigo"></i> Surat (<?= $stats['lamaran'] ?>)</span>
                    </div>
                </div>

                <!-- Section 4: Live GitHub & Hosting Card -->
                <div class="sidebar-system-card">
                    <div class="ssc-top">
                        <div class="ssc-git-pill">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/>
                            </svg>
                            <span>GitHub</span>
                        </div>
                        <span class="ssc-status-dot" title="Terkoneksi"></span>
                    </div>
                    <div class="ssc-repo-name" title="<?= htmlspecialchars($sidebarRepo) ?>"><?= htmlspecialchars($sidebarRepo) ?></div>
                    <div class="ssc-info-row">
                        <span class="ssc-branch-pill">branch: <?= htmlspecialchars($sidebarBranch) ?></span>
                        <span class="ssc-shield-pill">🛡️ InfinityFree</span>
                    </div>
                    <a href="upgrade.php" class="ssc-upgrade-btn">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="16 16 12 12 8 16"></polyline>
                            <line x1="12" y1="12" x2="12" y2="21"></line>
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                        </svg>
                        <span>Pembaruan Sistem</span>
                    </a>
                </div>

                <!-- Section 5: Shortcut Toko -->
                <div>
                    <div class="nav-section-title">Toko Publik</div>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="index.php" target="_blank" class="sidebar-link">
                                <span class="sidebar-icon-box icon-orange">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                </span>
                                <span>Buka Toko SeaCV</span>
                                <span class="sidebar-badge badge-orange">Live ↗</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Bottom / Admin Profile -->
            <div class="sidebar-footer">
                <div class="sidebar-avatar-wrapper">
                    <div class="sidebar-avatar"><?= $adminInitial ?></div>
                    <span class="sidebar-online-badge"></span>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?= $adminUsername ?></div>
                    <span class="sidebar-user-role">Super Admin</span>
                </div>
                <a href="logout.php" class="btn-sidebar-logout" title="Logout dari Admin">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                    <!-- Burger Toggle Button (Buka/Tutup Sidebar) -->
                    <button type="button" class="btn-burger" id="burgerToggleBtn" onclick="toggleSidebar()" title="Buka/Tutup Sidebar (Burger Menu)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="topbar-page-title">Dashboard Katalog</div>
                </div>

                <div class="topbar-right">
                    <a href="index.php" target="_blank" class="btn-view-store" title="Buka Toko SeaCV di Tab Baru">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <span>Lihat Toko</span>
                    </a>
                    <a href="addproduct.php" class="btn-add-quick">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>+ Tambah Template</span>
                    </a>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="admin-content">
                <!-- Page Header Title -->
                <div class="page-header">
                    <div class="page-title-group">
                        <h1>Katalog Template Produk</h1>
                        <p>Kelola semua template CV & surat lamaran kerja yang ditampilkan di toko SeaCV.</p>
                    </div>

                    <a href="addproduct.php" class="btn-add-quick" style="padding: 9px 18px; font-size: 13.5px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Tambah Template Baru</span>
                    </a>
                </div>

                <!-- KPI Metric Tiles -->
                <div class="kpi-row">
                    <div class="kpi-card active" data-cat="all" onclick="filterByCategory('all')">
                        <span class="kpi-title">Total Template</span>
                        <span class="kpi-num"><?= $stats['total'] ?></span>
                    </div>
                    <div class="kpi-card" data-cat="kreatif" onclick="filterByCategory('kreatif')">
                        <span class="kpi-title">CV Kreatif</span>
                        <span class="kpi-num"><?= $stats['kreatif'] ?></span>
                    </div>
                    <div class="kpi-card" data-cat="ats" onclick="filterByCategory('ats')">
                        <span class="kpi-title">CV ATS</span>
                        <span class="kpi-num"><?= $stats['ats'] ?></span>
                    </div>
                    <div class="kpi-card" data-cat="lamaran" onclick="filterByCategory('lamaran')">
                        <span class="kpi-title">Surat Lamaran</span>
                        <span class="kpi-num"><?= $stats['lamaran'] ?></span>
                    </div>
                </div>

                <!-- Product Table Card -->
                <div class="catalog-card">
                    <!-- Toolbar Filter & Search -->
                    <div class="catalog-toolbar">
                        <div class="filter-group">
                            <button type="button" class="filter-tab active" data-category="all" onclick="filterByCategory('all')">
                                Semua <span class="filter-count"><?= $stats['total'] ?></span>
                            </button>
                            <button type="button" class="filter-tab" data-category="kreatif" onclick="filterByCategory('kreatif')">
                                CV Kreatif <span class="filter-count"><?= $stats['kreatif'] ?></span>
                            </button>
                            <button type="button" class="filter-tab" data-category="ats" onclick="filterByCategory('ats')">
                                CV ATS <span class="filter-count"><?= $stats['ats'] ?></span>
                            </button>
                            <button type="button" class="filter-tab" data-category="lamaran" onclick="filterByCategory('lamaran')">
                                Surat Lamaran <span class="filter-count"><?= $stats['lamaran'] ?></span>
                            </button>
                        </div>

                        <div class="search-wrapper">
                            <svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="searchInput" class="search-input" placeholder="Cari template..." autocomplete="off">
                            <button type="button" id="searchClear" class="search-clear">&times;</button>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="table-responsive">
                        <table class="catalog-table" id="productTable">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Template</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th style="text-align: right; width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <?php foreach ($products as $idx => $product): ?>
                                    <?php
                                        $displayName = resolveProductDisplayName($product, $idx);
                                        $cat = $product['category'];
                                        $catKey = 'kreatif';
                                        if (stripos($cat, 'ats') !== false) {
                                            $catKey = 'ats';
                                        } elseif (stripos($cat, 'lamaran') !== false) {
                                            $catKey = 'lamaran';
                                        }
                                    ?>
                                    <tr class="product-row"
                                        data-id="<?= $product['id'] ?>"
                                        data-name="<?= strtolower(htmlspecialchars($displayName)) ?>"
                                        data-raw="<?= strtolower(htmlspecialchars($product['name'])) ?>"
                                        data-category="<?= $catKey ?>">
                                        <td class="td-id">#<?= $product['id'] ?></td>
                                        <td>
                                            <div class="td-product">
                                                <img src="<?= htmlspecialchars($product['image']) ?>" 
                                                     alt="<?= htmlspecialchars($displayName) ?>" 
                                                     class="table-thumb" 
                                                     style="width: 48px; height: 64px; min-width: 48px; max-width: 48px; object-fit: cover; border-radius: 6px; display: block;"
                                                     loading="lazy"
                                                     onclick="openLightbox('<?= htmlspecialchars($product['image']) ?>', '<?= htmlspecialchars(addslashes($displayName)) ?>')"
                                                     onerror="this.src='logo.png'">
                                                <div class="product-info">
                                                    <span class="product-name"><?= htmlspecialchars($displayName) ?></span>
                                                    <span class="product-meta">File: <?= basename($product['image']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-category"><?= htmlspecialchars($product['category']) ?></span>
                                        </td>
                                        <td>
                                            <span class="td-price">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                        </td>
                                        <td>
                                            <div class="td-actions">
                                                <a href="edit.php?id=<?= $product['id'] ?>" class="btn-edit">Edit</a>
                                                <button type="button" class="btn-delete" onclick="confirmDelete(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($displayName)) ?>')">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyBox" class="empty-state" style="display: none;">
                        <div class="empty-title">Tidak ada template ditemukan</div>
                        <div class="empty-desc">Tidak ada hasil yang cocok dengan kriteria pencarian Anda.</div>
                        <button type="button" class="btn-reset" onclick="resetFilters()">Reset Filter</button>
                    </div>

                    <!-- Footer Counter -->
                    <div class="table-footer">
                        <span>Menampilkan <strong id="countDisplay"><?= count($products) ?></strong> dari <?= count($products) ?> template</span>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div id="imageModal" class="lightbox-modal" onclick="closeLightbox()">
        <div class="lightbox-card" onclick="event.stopPropagation()">
            <div class="lightbox-header">
                <span id="lightboxTitle" class="lightbox-title">Pratinjau Template</span>
                <button type="button" class="lightbox-close" onclick="closeLightbox()">&times;</button>
            </div>
            <div class="lightbox-body">
                <img id="lightboxImg" src="" alt="Pratinjau">
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Flash Message Toast
        const flashMsg = '<?= $flashMsg ?>';
        if (flashMsg === 'added') {
            Swal.fire({ icon: 'success', title: 'Template berhasil ditambahkan', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
        } else if (flashMsg === 'updated') {
            Swal.fire({ icon: 'success', title: 'Template berhasil diperbarui', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
        } else if (flashMsg === 'deleted') {
            Swal.fire({ icon: 'success', title: 'Template berhasil dihapus', timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
        }

        // Sidebar Burger Toggle Logic
        const adminLayout = document.getElementById('adminLayout');

        // Restore user's preferred sidebar state on desktop
        if (window.innerWidth > 1024) {
            const savedState = localStorage.getItem('seacv_admin_sidebar');
            if (savedState === 'collapsed') {
                adminLayout.classList.add('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            if (window.innerWidth <= 1024) {
                // Mobile behavior: drawer slide
                adminLayout.classList.toggle('mobile-sidebar-open');
            } else {
                // Desktop behavior: collapse / expand
                adminLayout.classList.toggle('sidebar-collapsed');
                const isCollapsed = adminLayout.classList.contains('sidebar-collapsed');
                localStorage.setItem('seacv_admin_sidebar', isCollapsed ? 'collapsed' : 'expanded');
            }
        }

        function closeSidebarMobile() {
            adminLayout.classList.remove('mobile-sidebar-open');
        }

        // Delete Confirmation
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus template?',
                text: 'Hapus "' + name + '" dari katalog toko?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus.php?id=' + id;
                }
            });
        }

        // Lightbox Preview
        function openLightbox(src, title) {
            document.getElementById('lightboxImg').src = src;
            document.getElementById('lightboxTitle').textContent = title;
            document.getElementById('imageModal').classList.add('open');
        }

        function closeLightbox() {
            document.getElementById('imageModal').classList.remove('open');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });

        // Instant Filter & Search
        let currentCat = 'all';
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        const filterTabs = document.querySelectorAll('.filter-tab');
        const kpiCards = document.querySelectorAll('.kpi-card');
        const rows = document.querySelectorAll('.product-row');
        const countDisplay = document.getElementById('countDisplay');
        const emptyBox = document.getElementById('emptyBox');
        const table = document.getElementById('productTable');

        function runFilter() {
            const q = searchInput.value.trim().toLowerCase();
            let visible = 0;

            searchClear.style.display = q ? 'block' : 'none';

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const raw = row.getAttribute('data-raw') || '';
                const id = row.getAttribute('data-id') || '';
                const cat = row.getAttribute('data-category') || '';

                const matchCat = (currentCat === 'all' || cat === currentCat);
                const matchQuery = (!q || name.includes(q) || raw.includes(q) || id.includes(q));

                if (matchCat && matchQuery) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            countDisplay.textContent = visible;

            if (visible === 0) {
                emptyBox.style.display = 'flex';
                table.style.display = 'none';
            } else {
                emptyBox.style.display = 'none';
                table.style.display = '';
            }
        }

        function filterByCategory(cat) {
            currentCat = cat;
            
            filterTabs.forEach(tab => {
                tab.classList.toggle('active', tab.getAttribute('data-category') === cat);
            });

            kpiCards.forEach(card => {
                card.classList.toggle('active', card.getAttribute('data-cat') === cat);
            });

            runFilter();
        }

        searchInput.addEventListener('input', runFilter);
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            runFilter();
            searchInput.focus();
        });

        function resetFilters() {
            searchInput.value = '';
            filterByCategory('all');
        }

        // Auto filter from URL query param if present
        const urlParams = new URLSearchParams(window.location.search);
        const catParam = urlParams.get('cat') || urlParams.get('kategori');
        if (catParam && ['ats', 'kreatif', 'lamaran', 'all'].includes(catParam.toLowerCase())) {
            filterByCategory(catParam.toLowerCase());
        }
    </script>
</body>
</html>