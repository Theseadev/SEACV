<?php
// public/upgrade.php
require_once "auth.php";
require_once "config.php";

$adminUsername = htmlspecialchars($_SESSION["username"] ?? 'Fahrul');
$adminInitial = strtoupper(substr($adminUsername, 0, 1));

// Load version config
$versionFile = __DIR__ . '/version.json';
$versionData = [];
if (file_exists($versionFile)) {
    $versionData = json_decode(file_get_contents($versionFile), true) ?: [];
}

$appVersion = $versionData['version'] ?? '1.0.0';
$githubRepo = $versionData['github_repo'] ?? '';
$githubBranch = $versionData['github_branch'] ?? 'main';
$currentCommit = $versionData['current_commit'] ?? 'initial';
$currentCommitMsg = $versionData['current_commit_msg'] ?? 'Versi Awal SeaCV';
$lastUpdated = $versionData['last_updated'] ?? 'Belum pernah';
$hasToken = !empty($versionData['github_token']);

// Load stats for sidebar
$stats = ['total' => 0, 'ats' => 0, 'kreatif' => 0, 'lamaran' => 0];
try {
    $st = $pdo->query("SELECT COUNT(*) as total, SUM(category LIKE '%ats%') as ats, SUM(category LIKE '%kreatif%') as kreatif, SUM(category LIKE '%lamaran%') as lamaran FROM products")->fetch();
    if ($st) {
        $stats['total'] = (int)($st['total'] ?? 0);
        $stats['ats'] = (int)($st['ats'] ?? 0);
        $stats['kreatif'] = (int)($st['kreatif'] ?? 0);
        $stats['lamaran'] = (int)($st['lamaran'] ?? 0);
    }
} catch (Exception $e) {}

$sidebarRepo = $versionData['github_repo'] ?? 'Theseadev/SEACV';
$sidebarBranch = $versionData['github_branch'] ?? 'main';
$sidebarCommit = substr($versionData['current_commit'] ?? '5540ac8', 0, 7);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembaruan Sistem & Terminal - SeaCV Admin</title>
    <link rel="icon" type="image/png" href="logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
            --adm-success: #16a34a;
            --adm-success-soft: #f0fdf4;
            --adm-success-border: #bbf7d0;
            --adm-danger: #dc2626;
            --adm-danger-soft: #fef2f2;
            --adm-danger-border: #fecaca;
            --adm-warning: #d97706;
            --adm-warning-soft: #fffbeb;
            --adm-warning-border: #fde68a;
            --adm-radius-sm: 6px;
            --adm-radius-md: 10px;
            --adm-radius-lg: 14px;
            --adm-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --adm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
            --adm-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --adm-font-display: 'Outfit', sans-serif;
            --adm-font-mono: 'Fira Code', Menlo, Monaco, Consolas, monospace;
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

        /* --------------------------------------------------------------------------
           Sidebar Navigation (Modern, Vibrant, Lively)
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
           Main Area & Topbar
           -------------------------------------------------------------------------- */
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

        /* --------------------------------------------------------------------------
           Two-Column Workspace (Left Controller, Right Terminal)
           -------------------------------------------------------------------------- */
        .admin-content {
            padding: 28px 24px 60px;
            max-width: 1500px;
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
            margin-bottom: 20px;
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

        .page-header {
            margin-bottom: 24px;
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

        .upgrade-grid {
            display: grid;
            grid-template-columns: 460px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1180px) {
            .upgrade-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card Styles */
        .card-box {
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            border-radius: var(--adm-radius-lg);
            padding: 24px;
            box-shadow: var(--adm-shadow-sm);
            margin-bottom: 20px;
        }

        .card-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--adm-border);
        }

        .card-box-title {
            font-family: var(--adm-font-display);
            font-size: 16px;
            font-weight: 700;
            color: var(--adm-text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Version Info Stats */
        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--adm-border);
            font-size: 13px;
        }

        .status-row:last-child {
            border-bottom: none;
        }

        .status-label {
            color: var(--adm-text-sub);
        }

        .status-val {
            font-weight: 600;
            color: var(--adm-text-main);
            font-variant-numeric: tabular-nums;
        }

        .badge-commit {
            font-family: var(--adm-font-mono);
            font-size: 11.5px;
            background: var(--adm-subtle);
            border: 1px solid var(--adm-border);
            padding: 2px 6px;
            border-radius: 4px;
            color: var(--adm-text-main);
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 999px;
        }

        .badge-status.ready {
            background: var(--adm-subtle);
            color: var(--adm-text-sub);
            border: 1px solid var(--adm-border);
        }

        .badge-status.available {
            background: var(--adm-warning-soft);
            color: var(--adm-warning);
            border: 1px solid var(--adm-warning-border);
        }

        .badge-status.uptodate {
            background: var(--adm-success-soft);
            color: var(--adm-success);
            border: 1px solid var(--adm-success-border);
        }

        /* Upgrade Buttons */
        .btn-upgrade-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-check-update {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px 16px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-surface);
            border: 1px solid var(--adm-primary);
            color: var(--adm-primary);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-check-update:hover {
            background: var(--adm-primary-soft);
        }

        .btn-apply-update {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px 16px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-primary);
            border: 1px solid transparent;
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-apply-update:hover {
            background: var(--adm-primary-hover);
        }

        .btn-apply-update:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--adm-text-main);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            border-radius: var(--adm-radius-sm);
            border: 1px solid var(--adm-border);
            background: #ffffff;
            font-family: var(--adm-font);
            font-size: 13px;
            color: var(--adm-text-main);
            transition: border-color 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--adm-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-save-settings {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 9px 14px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-subtle);
            border: 1px solid var(--adm-border);
            color: var(--adm-text-main);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-top: 6px;
        }

        .btn-save-settings:hover {
            background: #e2e8f0;
        }

        /* Safety Shield Card */
        .shield-card {
            background: #f0fdf4;
            border: 1px solid var(--adm-success-border);
            border-radius: var(--adm-radius-lg);
            padding: 18px 20px;
        }

        .shield-header {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--adm-success);
            font-weight: 700;
            font-size: 13.5px;
            margin-bottom: 8px;
        }

        .shield-desc {
            font-size: 12.5px;
            color: #166534;
            line-height: 1.5;
        }

        /* --------------------------------------------------------------------------
           Developer Terminal Console (Right Column)
           -------------------------------------------------------------------------- */
        .terminal-window {
            background: #090d16;
            border-radius: var(--adm-radius-lg);
            border: 1px solid #1e293b;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 560px;
            max-height: 720px;
        }

        .terminal-topbar {
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .terminal-dots {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
        }

        .dot-red { background: #ef4444; }
        .dot-yellow { background: #f59e0b; }
        .dot-green { background: #10b981; }

        .terminal-title {
            font-family: var(--adm-font-mono);
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        .terminal-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .terminal-status-pill {
            font-family: var(--adm-font-mono);
            font-size: 11px;
            color: #34d399;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .terminal-btn {
            background: transparent;
            border: 1px solid #334155;
            color: #94a3b8;
            font-family: var(--adm-font-mono);
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .terminal-btn:hover {
            background: #1e293b;
            color: #f8fafc;
            border-color: #475569;
        }

        /* Terminal Screen */
        .terminal-body {
            flex: 1;
            padding: 18px 20px;
            overflow-y: auto;
            font-family: var(--adm-font-mono);
            font-size: 12.5px;
            line-height: 1.65;
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .terminal-line {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            word-break: break-word;
        }

        .log-time {
            color: #475569;
            flex-shrink: 0;
            font-size: 11px;
            padding-top: 1px;
        }

        .log-tag {
            font-size: 10.5px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .tag-info { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .tag-git { background: rgba(168, 85, 247, 0.2); color: #c084fc; }
        .tag-success { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .tag-update { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .tag-protect { background: rgba(20, 184, 166, 0.2); color: #2dd4bf; }
        .tag-download { background: rgba(14, 165, 233, 0.2); color: #38bdf8; }
        .tag-extract { background: rgba(236, 72, 153, 0.2); color: #f472b6; }
        .tag-error { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .tag-warn { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }

        .log-text {
            flex: 1;
        }

        /* Terminal Interactive Command Prompt */
        .terminal-prompt-bar {
            background: #0f172a;
            border-top: 1px solid #1e293b;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .prompt-prefix {
            font-family: var(--adm-font-mono);
            font-size: 12.5px;
            color: #10b981;
            font-weight: 600;
            white-space: nowrap;
        }

        .terminal-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #f8fafc;
            font-family: var(--adm-font-mono);
            font-size: 12.5px;
            outline: none;
        }

        .terminal-input::placeholder {
            color: #475569;
        }

        /* Mobile rules */
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
                            <a href="admin.php" class="sidebar-link">
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
                            <a href="upgrade.php" class="sidebar-link active">
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
                            <a href="admin.php?cat=ats" class="sidebar-link">
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
                            <a href="admin.php?cat=kreatif" class="sidebar-link">
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
                            <a href="admin.php?cat=lamaran" class="sidebar-link">
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
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
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

        <!-- Main Column -->
        <div class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button type="button" class="btn-burger" onclick="toggleSidebar()" title="Buka/Tutup Sidebar (Burger Menu)">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="topbar-page-title">Pembaruan Sistem & Terminal Console</div>
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
                    <span>Pembaruan Sistem & Terminal</span>
                </div>

                <div class="page-header">
                    <div class="page-title-group">
                        <h1>Pembaruan Sistem (GitHub Sync)</h1>
                        <p>Tarik pembaruan kode terbaru dari repositori GitHub langsung ke hosting dengan engine native PHP & pantau log terminal secara real-time.</p>
                    </div>
                </div>

                <!-- 2-Column Grid: Left Controls, Right Terminal -->
                <div class="upgrade-grid">
                    
                    <!-- Left Column: Controller -->
                    <div class="upgrade-controls">
                        <!-- Card 1: System & Update Status -->
                        <div class="card-box">
                            <div class="card-box-header">
                                <div class="card-box-title">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path>
                                    </svg>
                                    <span>Status Sistem & Pembaruan</span>
                                </div>
                                <span class="badge-status ready" id="updateBadge">● Siap Cek</span>
                            </div>

                            <div class="status-row">
                                <span class="status-label">Versi Aplikasi</span>
                                <span class="status-val" id="lblVersion">v<?= htmlspecialchars($appVersion) ?></span>
                            </div>
                            <div class="status-row">
                                <span class="status-label">Commit Aktif</span>
                                <span class="status-val"><span class="badge-commit" id="lblCommit">#<?= htmlspecialchars(substr($currentCommit, 0, 7)) ?></span></span>
                            </div>
                            <div class="status-row">
                                <span class="status-label">Pesan Versi</span>
                                <span class="status-val" id="lblCommitMsg" style="max-width: 230px; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($currentCommitMsg) ?>
                                </span>
                            </div>
                            <div class="status-row">
                                <span class="status-label">Target Repositori</span>
                                <span class="status-val" id="lblRepo"><?= !empty($githubRepo) ? htmlspecialchars($githubRepo) . ' (' . htmlspecialchars($githubBranch) . ')' : '<em style="color:#94a3b8;">Belum diatur</em>' ?></span>
                            </div>
                            <div class="status-row">
                                <span class="status-label">Pembaruan Terakhir</span>
                                <span class="status-val" id="lblLastUpdated"><?= htmlspecialchars($lastUpdated) ?></span>
                            </div>

                            <div class="btn-upgrade-group">
                                <button type="button" class="btn-check-update" id="btnCheckUpdate" onclick="checkGitHubUpdate()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                    </svg>
                                    <span>Cek Pembaruan dari GitHub</span>
                                </button>

                                <button type="button" class="btn-apply-update" id="btnApplyUpdate" onclick="applyGitHubUpdate()" disabled>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="16 16 12 12 8 16"></polyline>
                                        <line x1="12" y1="12" x2="12" y2="21"></line>
                                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                                    </svg>
                                    <span id="btnApplyText">Mulai Upgrade Sekarang</span>
                                </button>
                            </div>
                        </div>

                        <!-- Card 2: GitHub Repository Settings -->
                        <div class="card-box">
                            <div class="card-box-header">
                                <div class="card-box-title">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                                    </svg>
                                    <span>Konfigurasi Repositori GitHub</span>
                                </div>
                            </div>

                            <form id="repoConfigForm" onsubmit="saveRepoConfig(event)">
                                <div class="form-group">
                                    <label class="form-label" for="cfgRepo">Nama Repositori GitHub (owner/repo)</label>
                                    <input type="text" id="cfgRepo" class="form-input" 
                                           placeholder="Contoh: Theseadev/SEACV" 
                                           value="<?= htmlspecialchars($githubRepo) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="cfgBranch">Nama Branch</label>
                                    <input type="text" id="cfgBranch" class="form-input" 
                                           placeholder="main" 
                                           value="<?= htmlspecialchars($githubBranch) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="cfgToken">Personal Access Token (Opsional)</label>
                                    <input type="password" id="cfgToken" class="form-input" 
                                           placeholder="<?= $hasToken ? '•••••••••••••••• (Tersimpan)' : 'Masukkan token jika repo private' ?>" 
                                           value="<?= $hasToken ? '***KEEP***' : '' ?>">
                                </div>

                                <button type="submit" class="btn-save-settings">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    <span>Simpan Pengaturan Repositori</span>
                                </button>
                            </form>
                        </div>

                        <!-- Card 3: Safety Shield Protection Notice -->
                        <div class="shield-card">
                            <div class="shield-header">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                <span>InfinityFree Safety Shield Aktif</span>
                            </div>
                            <div class="shield-desc">
                                Saat proses upgrade berjalan, file <strong>config.php</strong> (kredensial database hosting) dan folder <strong>uploads/</strong> (semua gambar template CV Anda) <strong>otomatis diproteksi dan tidak akan tertimpa atau terhapus</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Interactive Terminal Console -->
                    <div class="upgrade-terminal">
                        <div class="terminal-window">
                            <!-- Terminal Topbar -->
                            <div class="terminal-topbar">
                                <div class="terminal-dots">
                                    <div class="dot dot-red"></div>
                                    <div class="dot dot-yellow"></div>
                                    <div class="dot dot-green"></div>
                                </div>

                                <div class="terminal-title">seacv-updater@infinityfree:~$</div>

                                <div class="terminal-actions">
                                    <span class="terminal-status-pill" id="termStatus">
                                        <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#34d399;"></span>
                                        IDLE
                                    </span>
                                    <button type="button" class="terminal-btn" onclick="copyTerminalLog()" title="Salin seluruh log">Copy</button>
                                    <button type="button" class="terminal-btn" onclick="clearTerminal()" title="Bersihkan terminal">Clear</button>
                                </div>
                            </div>

                            <!-- Terminal Log Output Area -->
                            <div class="terminal-body" id="terminalLog">
                                <div class="terminal-line">
                                    <span class="log-time">[<?= date('H:i:s') ?>]</span>
                                    <span class="log-tag tag-info">READY</span>
                                    <span class="log-text">SeaCV Git Cloud Updater Engine v1.0.0 siap digunakan.</span>
                                </div>
                                <div class="terminal-line">
                                    <span class="log-time">[<?= date('H:i:s') ?>]</span>
                                    <span class="log-tag tag-protect">SHIELD</span>
                                    <span class="log-text">Safety Protection: config.php & uploads/ terkunci dari overwriting.</span>
                                </div>
                                <div class="terminal-line">
                                    <span class="log-time">[<?= date('H:i:s') ?>]</span>
                                    <span class="log-tag tag-info">HINT</span>
                                    <span class="log-text">Klik tombol "Cek Pembaruan" atau ketik command di baris prompt di bawah.</span>
                                </div>
                            </div>

                            <!-- Terminal Interactive Prompt -->
                            <div class="terminal-prompt-bar">
                                <span class="prompt-prefix">seacv@terminal:~$</span>
                                <input type="text" id="terminalCmdInput" class="terminal-input" 
                                       placeholder="Ketik command: check, update, status, clear, help..." 
                                       autocomplete="off" onkeydown="handleTerminalCommand(event)">
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sidebar Toggle
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

        // Terminal Logging Helper
        const terminalLog = document.getElementById('terminalLog');
        const termStatus = document.getElementById('termStatus');

        function appendLog(type, msg, customTime = null) {
            const now = customTime || new Date().toTimeString().split(' ')[0];
            const line = document.createElement('div');
            line.className = 'terminal-line';

            let tagClass = 'tag-info';
            const upper = type.toUpperCase();
            if (upper === 'SUCCESS') tagClass = 'tag-success';
            else if (upper === 'ERROR') tagClass = 'tag-error';
            else if (upper === 'WARN') tagClass = 'tag-warn';
            else if (upper === 'GIT') tagClass = 'tag-git';
            else if (upper === 'UPDATE') tagClass = 'tag-update';
            else if (upper === 'PROTECT') tagClass = 'tag-protect';
            else if (upper === 'DOWNLOAD') tagClass = 'tag-download';
            else if (upper === 'EXTRACT') tagClass = 'tag-extract';

            line.innerHTML = `
                <span class="log-time">[${now}]</span>
                <span class="log-tag ${tagClass}">${upper}</span>
                <span class="log-text">${escapeHtml(msg)}</span>
            `;

            terminalLog.appendChild(line);
            terminalLog.scrollTop = terminalLog.scrollHeight;
        }

        function setTerminalStatus(text, color = '#34d399') {
            termStatus.innerHTML = `<span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:${color};"></span> ${text}`;
        }

        function clearTerminal() {
            terminalLog.innerHTML = '';
            appendLog('INFO', 'Terminal log dibersihkan.');
        }

        function copyTerminalLog() {
            const text = terminalLog.innerText;
            navigator.clipboard.writeText(text).then(() => {
                appendLog('INFO', 'Log terminal berhasil disalin ke clipboard.');
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Save Repository Configuration
        async function saveRepoConfig(e) {
            e.preventDefault();
            const repo = document.getElementById('cfgRepo').value.trim();
            const branch = document.getElementById('cfgBranch').value.trim();
            const token = document.getElementById('cfgToken').value.trim();

            appendLog('INFO', 'Menyimpan konfigurasi repositori...');
            setTerminalStatus('SAVING', '#f59e0b');

            try {
                const formData = new FormData();
                formData.append('action', 'save_config');
                formData.append('repo', repo);
                formData.append('branch', branch);
                formData.append('token', token);

                const res = await fetch('api_upgrade.php', { method: 'POST', body: formData });
                const json = await res.json();

                if (json.status === 'success') {
                    appendLog('SUCCESS', json.message);
                    document.getElementById('lblRepo').textContent = `${json.repo} (${json.branch})`;
                    setTerminalStatus('READY', '#34d399');
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaturan Tersimpan',
                        text: 'Repositori GitHub berhasil dikonfigurasi.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    appendLog('ERROR', json.message || 'Gagal menyimpan.');
                    setTerminalStatus('ERROR', '#ef4444');
                }
            } catch (err) {
                appendLog('ERROR', 'Terjadi kesalahan jaringan: ' + err.message);
                setTerminalStatus('ERROR', '#ef4444');
            }
        }

        // Check GitHub Update
        async function checkGitHubUpdate() {
            const btn = document.getElementById('btnCheckUpdate');
            btn.disabled = true;
            btn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin">
                    <line x1="12" y1="2" x2="12" y2="6"></line>
                    <line x1="12" y1="18" x2="12" y2="22"></line>
                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                </svg>
                <span>Memeriksa GitHub...</span>
            `;

            setTerminalStatus('CHECKING', '#38bdf8');

            try {
                const res = await fetch('api_upgrade.php?action=check');
                const json = await res.json();

                if (json.logs && Array.isArray(json.logs)) {
                    json.logs.forEach(l => appendLog(l.type, l.msg, l.time));
                }

                const badge = document.getElementById('updateBadge');
                const btnApply = document.getElementById('btnApplyUpdate');

                if (json.status === 'success') {
                    if (json.has_update) {
                        badge.className = 'badge-status available';
                        badge.textContent = '★ Update Tersedia';
                        btnApply.disabled = false;
                        document.getElementById('btnApplyText').textContent = `Mulai Upgrade ke #${json.remote_sha}`;
                        setTerminalStatus('UPDATE FOUND', '#f59e0b');
                    } else {
                        badge.className = 'badge-status uptodate';
                        badge.textContent = '✓ Up to Date';
                        btnApply.disabled = true;
                        document.getElementById('btnApplyText').textContent = 'Sistem Sudah Mutakhir';
                        setTerminalStatus('UP TO DATE', '#34d399');
                    }
                } else {
                    badge.className = 'badge-status ready';
                    badge.textContent = '● Error';
                    setTerminalStatus('ERROR', '#ef4444');
                }
            } catch (err) {
                appendLog('ERROR', 'Gagal memanggil API: ' + err.message);
                setTerminalStatus('ERROR', '#ef4444');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    <span>Cek Pembaruan dari GitHub</span>
                `;
            }
        }

        // Apply GitHub Update
        async function applyGitHubUpdate() {
            const confirmResult = await Swal.fire({
                title: 'Mulai Upgrade Sistem?',
                text: 'File sistem akan diperbarui dari GitHub. File config.php & folder uploads/ akan otomatis diproteksi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Upgrade Sekarang',
                cancelButtonText: 'Batal'
            });

            if (!confirmResult.isConfirmed) return;

            const btnApply = document.getElementById('btnApplyUpdate');
            const btnCheck = document.getElementById('btnCheckUpdate');
            btnApply.disabled = true;
            btnCheck.disabled = true;
            btnApply.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin">
                    <line x1="12" y1="2" x2="12" y2="6"></line>
                    <line x1="12" y1="18" x2="12" y2="22"></line>
                </svg>
                <span>Sedang Memperbarui...</span>
            `;

            setTerminalStatus('UPGRADING...', '#f59e0b');

            try {
                const res = await fetch('api_upgrade.php?action=apply');
                const json = await res.json();

                if (json.logs && Array.isArray(json.logs)) {
                    json.logs.forEach(l => appendLog(l.type, l.msg, l.time));
                }

                if (json.status === 'success') {
                    setTerminalStatus('COMPLETED', '#34d399');
                    document.getElementById('lblCommit').textContent = '#' + json.new_commit;
                    document.getElementById('updateBadge').className = 'badge-status uptodate';
                    document.getElementById('updateBadge').textContent = '✓ Up to Date';
                    document.getElementById('btnApplyText').textContent = 'Sistem Berhasil Diperbarui';

                    Swal.fire({
                        icon: 'success',
                        title: 'Upgrade Berhasil!',
                        text: 'SeaCV telah berhasil diperbarui ke versi terbaru dari GitHub.',
                        confirmButtonText: 'Muat Ulang Halaman'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    setTerminalStatus('FAILED', '#ef4444');
                    btnApply.disabled = false;
                    document.getElementById('btnApplyText').textContent = 'Coba Upgrade Ulang';
                    Swal.fire({
                        icon: 'error',
                        title: 'Upgrade Gagal',
                        text: json.message || 'Periksa log terminal untuk detail masalah.'
                    });
                }
            } catch (err) {
                appendLog('ERROR', 'Terjadi kesalahan sistem: ' + err.message);
                setTerminalStatus('ERROR', '#ef4444');
                btnApply.disabled = false;
                document.getElementById('btnApplyText').textContent = 'Coba Upgrade Ulang';
            } finally {
                btnCheck.disabled = false;
            }
        }

        // Terminal Interactive Command Line Input
        function handleTerminalCommand(e) {
            if (e.key === 'Enter') {
                const input = document.getElementById('terminalCmdInput');
                const rawCmd = input.value.trim();
                if (!rawCmd) return;

                input.value = '';
                appendLog('GIT', `$ ${rawCmd}`);

                const cmd = rawCmd.toLowerCase();
                if (cmd === 'clear') {
                    clearTerminal();
                } else if (cmd === 'check' || cmd === 'fetch') {
                    checkGitHubUpdate();
                } else if (cmd === 'update' || cmd === 'upgrade' || cmd === 'pull') {
                    applyGitHubUpdate();
                } else if (cmd === 'status') {
                    appendLog('INFO', `Versi: ${document.getElementById('lblVersion').textContent} | Commit: ${document.getElementById('lblCommit').textContent}`);
                    appendLog('INFO', `Target: ${document.getElementById('lblRepo').textContent}`);
                } else if (cmd === 'help') {
                    appendLog('INFO', 'Daftar Perintah Terminal Tersedia:');
                    appendLog('INFO', '  check    : Memeriksa commit baru di GitHub');
                    appendLog('INFO', '  update   : Menjalankan sinkronisasi / upgrade sistem');
                    appendLog('INFO', '  status   : Menampilkan status versi & commit aktif');
                    appendLog('INFO', '  clear    : Membersihkan layar terminal');
                    appendLog('INFO', '  help     : Menampilkan bantuan perintah ini');
                } else {
                    appendLog('WARN', `Perintah '${rawCmd}' tidak dikenali. Ketik 'help' untuk daftar perintah.`);
                }
            }
        }
    </script>
</body>
</html>
