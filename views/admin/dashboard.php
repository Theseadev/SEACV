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
           Sidebar Navigation (Clean, Sleek & Modern)
           -------------------------------------------------------------------------- */
        .admin-sidebar {
            width: 250px;
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
        }

        /* Collapsed State on Desktop */
        .admin-layout.sidebar-collapsed .admin-sidebar {
            margin-left: -250px;
        }

        .sidebar-header {
            height: 64px;
            padding: 0 18px;
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
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .brand-text {
            font-family: var(--adm-font-display);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--adm-primary);
        }

        .brand-name {
            color: var(--adm-primary);
        }

        .brand-text .sea,
        .brand-text .cv {
            color: var(--adm-primary);
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--adm-primary-soft);
            color: var(--adm-primary);
            border: 1px solid var(--adm-primary-border);
            padding: 1px 7px;
            border-radius: 6px;
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
            padding: 18px 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 999px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--adm-text-muted);
            padding: 0 10px;
            margin-bottom: 6px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
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
            color: var(--adm-text-muted);
            transition: color 0.15s ease;
        }

        .sidebar-link:hover .sidebar-icon {
            color: var(--adm-text-main);
        }

        .sidebar-link.active .sidebar-icon {
            color: var(--adm-primary);
        }

        /* Subtle Pill Badges */
        .sidebar-badge {
            margin-left: auto;
            font-size: 11px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 12px;
            background: var(--adm-subtle);
            color: var(--adm-text-muted);
        }

        .sidebar-link.active .sidebar-badge {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .sidebar-badge.badge-green {
            background: #ecfdf5;
            color: #059669;
        }

        /* Profile Footer */
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
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--adm-primary);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13.5px;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--adm-text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: var(--adm-text-muted);
            line-height: 1.3;
        }

        .btn-sidebar-logout {
            width: 32px;
            height: 32px;
            border-radius: 6px;
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

        .btn-manage-cat {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 15px;
            border-radius: var(--adm-radius-sm);
            background: var(--adm-surface);
            border: 1px solid var(--adm-border);
            color: var(--adm-text-sub);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-manage-cat:hover {
            background: var(--adm-subtle);
            border-color: #cbd5e1;
            color: var(--adm-primary);
        }

        .page-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

        .filter-tab-add {
            padding: 6px 12px;
            border-radius: var(--adm-radius-sm);
            border: 1px dashed var(--adm-primary);
            background: var(--adm-primary-soft);
            color: var(--adm-primary);
            font-family: var(--adm-font);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .filter-tab-add:hover {
            background: #dbeafe;
            border-color: #1d4ed8;
            color: #1d4ed8;
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
            html, body.admin-body {
                overflow-x: hidden !important;
                max-width: 100vw !important;
                width: 100% !important;
            }

            .admin-layout,
            .admin-main,
            .admin-content {
                overflow-x: hidden !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .admin-topbar {
                padding: 0 12px;
                height: 56px;
                max-width: 100%;
                box-sizing: border-box;
            }

            .topbar-left {
                gap: 8px;
                min-width: 0;
            }

            .topbar-page-title {
                font-size: 14px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 140px;
            }

            .topbar-right {
                gap: 8px;
                flex-shrink: 0;
            }

            .btn-view-store {
                padding: 0;
                width: 36px;
                height: 36px;
                justify-content: center;
                border-radius: 8px;
                flex-shrink: 0;
            }

            .btn-view-store span {
                display: none;
            }

            .topbar-right .btn-add-quick {
                padding: 0;
                width: 36px;
                height: 36px;
                justify-content: center;
                border-radius: 8px;
                flex-shrink: 0;
            }

            .topbar-right .btn-add-quick span {
                display: none;
            }

            .admin-content {
                padding: 14px 12px 48px;
                box-sizing: border-box;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 18px;
            }

            .page-title-group h1 {
                font-size: 20px;
            }

            .page-title-group p {
                font-size: 12.5px;
                line-height: 1.4;
            }

            .page-header-actions {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .page-header-actions .btn-manage-cat,
            .page-header-actions .btn-add-quick {
                width: 100%;
                justify-content: center;
                padding: 10px 8px;
                font-size: 12.5px;
                border-radius: 8px;
                font-weight: 600;
                box-sizing: border-box;
                text-align: center;
            }

            .page-header-actions .btn-add-quick {
                box-shadow: 0 3px 10px rgba(37, 99, 235, 0.2);
            }

            .kpi-row {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 16px;
            }

            .kpi-card {
                padding: 12px 14px;
                border-radius: 10px;
            }

            .kpi-title {
                font-size: 10.5px;
            }

            .kpi-num {
                font-size: 22px;
                margin-top: 3px;
            }

            .catalog-card {
                border-radius: 12px;
                overflow: hidden;
                width: 100%;
            }

            .catalog-toolbar {
                padding: 12px;
                gap: 10px;
                flex-direction: column;
                align-items: stretch;
                width: 100%;
                box-sizing: border-box;
            }

            /* 2x2 Grid for filter tabs - NO horizontal scroll */
            .filter-group {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 6px;
                width: 100%;
            }

            .filter-tab {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 8px 6px;
                font-size: 12px;
                border-radius: 8px;
                box-sizing: border-box;
            }

            .search-wrapper {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            /* Zero Horizontal Scroll: Native Card View for Products */
            .table-responsive {
                overflow-x: hidden !important;
                width: 100%;
            }

            .catalog-table thead {
                display: none !important;
            }

            .catalog-table, 
            .catalog-table tbody, 
            .catalog-table tr {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box;
            }

            .catalog-table tbody tr {
                position: relative;
                padding: 14px 12px;
                border-bottom: 1px solid var(--adm-border);
                background: #ffffff;
            }

            .catalog-table tbody tr:hover {
                background: #ffffff;
            }

            .catalog-table tbody tr td {
                display: block;
                padding: 0;
                border: none;
                width: 100%;
                box-sizing: border-box;
            }

            .catalog-table .td-id {
                position: absolute;
                top: 12px;
                right: 12px;
                width: auto;
                font-size: 10.5px;
                font-weight: 700;
                color: var(--adm-text-muted);
                background: var(--adm-subtle);
                padding: 2px 7px;
                border-radius: 6px;
            }

            .catalog-table .td-product {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                min-width: 0;
                width: 100%;
                padding-right: 50px;
                margin-bottom: 10px;
                box-sizing: border-box;
            }

            .catalog-table .table-thumb {
                width: 52px;
                height: 70px;
                min-width: 52px;
                max-width: 52px;
                border-radius: 8px;
                object-fit: cover;
                flex-shrink: 0;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            .catalog-table .product-info {
                flex: 1;
                min-width: 0;
            }

            .catalog-table .product-name {
                font-size: 13.5px;
                font-weight: 700;
                line-height: 1.35;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .catalog-table .product-meta {
                font-size: 11px;
                color: var(--adm-text-muted);
                margin-top: 3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .catalog-table tbody tr td:nth-child(3) {
                display: inline-block;
                width: auto;
                margin-right: 8px;
                margin-bottom: 10px;
            }

            .catalog-table tbody tr td:nth-child(4) {
                display: inline-block;
                width: auto;
                margin-bottom: 10px;
            }

            .badge-category {
                font-size: 11.5px;
                padding: 3px 8px;
            }

            .td-price {
                font-size: 14px;
                font-weight: 800;
                color: var(--adm-primary);
            }

            .catalog-table tbody tr td:nth-child(5) {
                display: block;
                width: 100%;
                padding-top: 10px;
                border-top: 1px dashed var(--adm-border);
            }

            .td-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                width: 100%;
            }

            .btn-edit, 
            .btn-delete {
                width: 100%;
                justify-content: center;
                text-align: center;
                padding: 8px 12px;
                font-size: 12.5px;
                border-radius: 8px;
                font-weight: 600;
                box-sizing: border-box;
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
            <!-- Header Brand -->
            <div class="sidebar-header">
                <a href="admin.php" class="brand-link">
                    <img src="logo.png" alt="SeaCV Logo" class="brand-logo">
                    <span class="brand-text">
                        <span class="brand-name">SEACV</span>
                        <span class="brand-badge">Admin</span>
                    </span>
                </a>

                <button type="button" class="btn-sidebar-close" onclick="closeSidebarMobile()" title="Tutup Sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="sidebar-nav">
                <!-- Section: Menu Utama -->
                <div>
                    <div class="nav-section-title">Menu Utama</div>
                    <ul class="sidebar-menu">
                        <li>
                            <a href="admin.php" class="sidebar-link active">
                                <span class="sidebar-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                </span>
                                <span>Katalog Template</span>
                                <span class="sidebar-badge"><?= $stats['total'] ?></span>
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
                                    </svg>
                                </span>
                                <span>Pembaruan Sistem</span>
                                <span class="sidebar-badge badge-green">Git</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section: Filter Kategori -->
                <div>
                    <div class="nav-section-title" style="display: flex; align-items: center; justify-content: space-between;">
                        <span>Filter Kategori</span>
                        <button type="button" onclick="openManageCategoriesModal()" style="background:none; border:none; color:var(--adm-primary); font-size:11px; font-weight:700; cursor:pointer; padding:0;" title="Kelola Kategori">+ Kelola</button>
                    </div>
                    <ul class="sidebar-menu">
                        <?php foreach ($categoriesList as $sc): ?>
                            <li>
                                <a href="admin.php?cat=<?= urlencode(strtolower($sc['name'])) ?>" onclick="if(typeof filterByCategory === 'function'){ event.preventDefault(); filterByCategory('<?= htmlspecialchars(addslashes(strtolower(trim($sc['name'])))) ?>'); }" class="sidebar-link">
                                    <span class="sidebar-icon">
                                        <?= getCategoryIconSvg($sc['name']) ?>
                                    </span>
                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($sc['name']) ?>"><?= htmlspecialchars($sc['name']) ?></span>
                                    <span class="sidebar-badge"><?= (int)$sc['count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Section: Pintasan Toko -->
                <div>
                    <div class="nav-section-title">Pintasan</div>
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
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: auto; color: #94a3b8;">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Admin Profile Footer -->
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
                        <span>Tambah Template</span>
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

                    <div class="page-header-actions">
                        <button type="button" class="btn-manage-cat" onclick="openManageCategoriesModal()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg>
                            <span>Kelola Kategori</span>
                        </button>
                        <a href="addproduct.php" class="btn-add-quick" style="padding: 9px 18px; font-size: 13.5px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Template Baru</span>
                        </a>
                    </div>
                </div>

                <!-- KPI Metric Tiles -->
                <div class="kpi-row">
                    <div class="kpi-card active" data-cat="all" onclick="filterByCategory('all')">
                        <span class="kpi-title">Total Template</span>
                        <span class="kpi-num"><?= count($products) ?></span>
                    </div>
                    <?php foreach ($categoriesList as $cItem): ?>
                        <div class="kpi-card" data-cat="<?= htmlspecialchars(strtolower(trim($cItem['name']))) ?>" onclick="filterByCategory('<?= htmlspecialchars(addslashes(strtolower(trim($cItem['name'])))) ?>')">
                            <span class="kpi-title" title="<?= htmlspecialchars($cItem['name']) ?>"><?= htmlspecialchars($cItem['name']) ?></span>
                            <span class="kpi-num"><?= (int)$cItem['count'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Product Table Card -->
                <div class="catalog-card">
                    <!-- Toolbar Filter & Search -->
                    <div class="catalog-toolbar">
                        <div class="filter-group">
                            <button type="button" class="filter-tab active" data-category="all" onclick="filterByCategory('all')">
                                Semua <span class="filter-count"><?= count($products) ?></span>
                            </button>
                            <?php foreach ($categoriesList as $cItem): ?>
                                <button type="button" class="filter-tab" data-category="<?= htmlspecialchars(strtolower(trim($cItem['name']))) ?>" onclick="filterByCategory('<?= htmlspecialchars(addslashes(strtolower(trim($cItem['name'])))) ?>')">
                                    <?= htmlspecialchars($cItem['name']) ?> <span class="filter-count"><?= (int)$cItem['count'] ?></span>
                                </button>
                            <?php endforeach; ?>
                            <button type="button" class="filter-tab-add" onclick="openManageCategoriesModal()" title="Kelola atau Tambah Kategori">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>Kategori</span>
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
                                        $catRaw = trim($product['category']);
                                    ?>
                                    <tr class="product-row"
                                        data-id="<?= $product['id'] ?>"
                                        data-name="<?= strtolower(htmlspecialchars($displayName)) ?>"
                                        data-raw="<?= strtolower(htmlspecialchars($product['name'])) ?>"
                                        data-category="<?= strtolower(htmlspecialchars($catRaw)) ?>">
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
                const cat = (row.getAttribute('data-category') || '').toLowerCase();

                let matchCat = false;
                if (currentCat === 'all') {
                    matchCat = true;
                } else if (cat === currentCat) {
                    matchCat = true;
                } else if (cat.includes(currentCat) || currentCat.includes(cat)) {
                    matchCat = true;
                }

                const matchQuery = (!q || name.includes(q) || raw.includes(q) || id.includes(q) || cat.includes(q));

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
            currentCat = (cat || 'all').toLowerCase();
            
            filterTabs.forEach(tab => {
                const tabCat = (tab.getAttribute('data-category') || '').toLowerCase();
                tab.classList.toggle('active', tabCat === currentCat);
            });

            kpiCards.forEach(card => {
                const cardCat = (card.getAttribute('data-cat') || '').toLowerCase();
                card.classList.toggle('active', cardCat === currentCat);
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
        if (catParam) {
            filterByCategory(catParam.trim().toLowerCase());
        }

        // --------------------------------------------------------------------------
        // Dynamic Category Management Modal (SweetAlert2)
        // --------------------------------------------------------------------------
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, function(m) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
            });
        }

        async function openManageCategoriesModal() {
            try {
                Swal.fire({
                    title: 'Memuat kategori...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const res = await fetch('api_category.php?action=list');
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Gagal memuat kategori');

                renderCategoryManager(data.categories);
            } catch (err) {
                Swal.fire('Error', err.message || 'Gagal memuat kategori', 'error');
            }
        }

        function renderCategoryManager(categories) {
            let listHtml = '';
            if (categories && categories.length > 0) {
                categories.forEach(c => {
                    const pCount = parseInt(c.product_count, 10) || 0;
                    const canDelete = pCount === 0;
                    listHtml += `
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                <span style="font-size: 13.5px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(c.name)}</span>
                                <span style="font-size: 11px; background: #e2e8f0; color: #475569; padding: 1px 7px; border-radius: 999px; font-weight: 700; flex-shrink: 0;">${pCount} template</span>
                            </div>
                            ${canDelete ? `
                                <button type="button" onclick="deleteCategory(${c.id}, '${escapeHtml(c.name)}')" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer; flex-shrink: 0; transition: all 0.15s ease;">Hapus</button>
                            ` : `
                                <span style="font-size: 11px; color: #94a3b8; font-weight: 600; flex-shrink: 0;">Aktif Digunakan</span>
                            `}
                        </div>
                    `;
                });
            } else {
                listHtml = '<div style="text-align: center; color: #94a3b8; padding: 16px;">Belum ada kategori.</div>';
            }

            const modalHtml = `
                <div style="text-align: left;">
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12.5px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Tambah Kategori Baru:</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" id="newCatInput" style="flex: 1; height: 40px; padding: 0 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13.5px; box-sizing: border-box;" placeholder="Contoh: Portofolio Desain, CV Fresh Grad..." maxlength="100">
                            <button type="button" id="btnSubmitNewCat" onclick="submitNewCategory()" style="padding: 0 16px; height: 40px; border-radius: 6px; background: #2563eb; color: #fff; border: none; font-size: 13px; font-weight: 700; cursor: pointer; white-space: nowrap; flex-shrink: 0;">+ Tambah</button>
                        </div>
                        <div id="newCatError" style="font-size: 12px; color: #dc2626; margin-top: 5px; display: none;"></div>
                    </div>

                    <div style="font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 18px 0 8px;">Daftar Kategori Tersedia (${categories.length})</div>
                    <div style="display: flex; flex-direction: column; gap: 6px; max-height: 220px; overflow-y: auto; padding-right: 2px;">
                        ${listHtml}
                    </div>
                </div>
            `;

            Swal.fire({
                title: 'Kelola Kategori Template',
                html: modalHtml,
                showConfirmButton: true,
                confirmButtonText: 'Tutup & Simpan',
                confirmButtonColor: '#2563eb',
                width: 520,
                didOpen: () => {
                    const input = document.getElementById('newCatInput');
                    if (input) {
                        input.focus();
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                submitNewCategory();
                            }
                        });
                    }
                }
            }).then(() => {
                window.location.reload();
            });
        }

        async function submitNewCategory() {
            const input = document.getElementById('newCatInput');
            const errDiv = document.getElementById('newCatError');
            const btn = document.getElementById('btnSubmitNewCat');
            if (!input) return;

            const name = input.value.trim();
            if (!name) {
                errDiv.textContent = 'Nama kategori tidak boleh kosong.';
                errDiv.style.display = 'block';
                return;
            }

            errDiv.style.display = 'none';
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';

            try {
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('name', name);

                const res = await fetch('api_category.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (!data.success) {
                    errDiv.textContent = data.message || 'Gagal menambahkan kategori';
                    errDiv.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = '+ Tambah';
                    return;
                }

                // Refresh categories list inside modal
                const listRes = await fetch('api_category.php?action=list');
                const listData = await listRes.json();
                renderCategoryManager(listData.categories);
            } catch (e) {
                errDiv.textContent = 'Terjadi kesalahan: ' + e.message;
                errDiv.style.display = 'block';
                btn.disabled = false;
                btn.textContent = '+ Tambah';
            }
        }

        async function deleteCategory(id, name) {
            const confirm = await Swal.fire({
                title: 'Hapus Kategori?',
                text: `Hapus kategori "${name}"? Kategori ini sedang tidak memiliki produk.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });

            if (!confirm.isConfirmed) return;

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                const res = await fetch('api_category.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (!data.success) {
                    Swal.fire('Gagal', data.message || 'Gagal menghapus kategori', 'error');
                    return;
                }

                // Refresh categories list inside modal
                const listRes = await fetch('api_category.php?action=list');
                const listData = await listRes.json();
                renderCategoryManager(listData.categories);
            } catch (e) {
                Swal.fire('Error', 'Terjadi kesalahan: ' + e.message, 'error');
            }
        }
    </script>
</body>
</html>