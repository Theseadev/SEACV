<?php
/**
 * -------------------------------------------------------------------------
 * SEACV - PREMIUM PROFESSIONAL RESUME & CV WRITING SERVICES PLATFORM
 * -------------------------------------------------------------------------
 * Theme       : Modern Clean & Professional Executive Theme
 * Typography  : Plus Jakarta Sans & Outfit / Poppins
 * Substack    : Dynamic Local Storage Cart Engine x SweetAlert2 x Lightbox
 * -------------------------------------------------------------------------
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database configuration
require_once __DIR__ . '/config.php';

// Request handling & filters
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$search_query    = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by         = isset($_GET['sort']) ? trim($_GET['sort']) : 'latest';

$products   = [];
$categories = [];

if (isset($pdo)) {
    try {
        // Fetch distinct categories
        $cat_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
        $raw_categories = $cat_stmt->fetchAll();
        foreach ($raw_categories as $c) {
            $categories[] = $c['category'];
        }

        // Default to 'CV Kreatif' if no category is provided
        if (empty($category_filter)) {
            if (in_array('CV Kreatif', $categories)) {
                $category_filter = 'CV Kreatif';
            } elseif (!empty($categories)) {
                $category_filter = $categories[0];
            } else {
                $category_filter = 'Semua';
            }
        }

        // Build dynamic query
        $query_string = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if ($category_filter !== 'Semua' && !empty($category_filter)) {
            $query_string .= " AND category = :category";
            $params[':category'] = $category_filter;
        }

        if (!empty($search_query)) {
            $query_string .= " AND (name LIKE :search OR category LIKE :search_cat)";
            $params[':search'] = "%" . $search_query . "%";
            $params[':search_cat'] = "%" . $search_query . "%";
        }

        if ($sort_by === 'price_low') {
            $query_string .= " ORDER BY price ASC";
        } elseif ($sort_by === 'price_high') {
            $query_string .= " ORDER BY price DESC";
        } elseif ($sort_by === 'alpha_asc') {
            $query_string .= " ORDER BY name ASC";
        } elseif ($category_filter === 'Semua') {
            $query_string .= " ORDER BY RAND()";
        } else {
            $query_string .= " ORDER BY id DESC";
        }

        $stmt = $pdo->prepare($query_string);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();
        $products = $stmt->fetchAll();

        // Identify top 3 newest product IDs per category for dynamic 'NEW' badge
        $newProductIds = [];
        try {
            $new_stmt = $pdo->query("
                SELECT id FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (PARTITION BY category ORDER BY id DESC) as rn
                    FROM products
                ) ranked
                WHERE rn <= 3
            ");
            $newProductIds = $new_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } catch (\Exception $e) {
            $newProductIds = [];
        }

    } catch (\Exception $ex) {
        $products = [];
    }
}

if (empty($categories)) {
    $categories = ['CV Kreatif', 'CV ATS', 'Surat Lamaran Kerja'];
}

/**
 * Helper function for clean sequential product naming:
 * e.g. CV Kreatif 01, CV Kreatif 02, CV ATS 01, Surat Lamaran 01, etc.
 */
function resolveProductDisplayName($product, $index = 0) {
    // 1. Check if image filename contains type and number:
    // e.g. cvkreatif01.webp, ats07.webp, slk04.webp, Surat Lamaran 02.webp, etc.
    $img = basename($product['image'] ?? '');
    if (preg_match('/(cv\s*kreatif|kreatif|ats|slk|surat\s*lamaran)[\w\s_-]*?(\d+)/i', $img, $matches)) {
        $type = strtolower($matches[1]);
        $num = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        if (strpos($type, 'ats') !== false) {
            return "CV ATS " . $num;
        } elseif (strpos($type, 'slk') !== false || strpos($type, 'lamaran') !== false) {
            return "Surat Lamaran " . $num;
        } else {
            return "CV Kreatif " . $num;
        }
    }
    
    // 2. Check if product name in DB already has category & number:
    $rawName = trim($product['name'] ?? '');
    if (preg_match('/(CV\s*Kreatif|CV\s*ATS|Surat\s*Lamaran|Surat\s*Pengunduran)\s*(\d+)/i', $rawName, $matches)) {
        $prefix = ucwords(strtolower(trim($matches[1])));
        $num = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        return $prefix . ' ' . $num;
    }

    // 3. Fallback based on category and index:
    $cat = $product['category'] ?? '';
    $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
    if (stripos($cat, 'kreatif') !== false) {
        return "CV Kreatif " . $num;
    } elseif (stripos($cat, 'ats') !== false) {
        return "CV ATS " . $num;
    } elseif (stripos($cat, 'lamaran') !== false) {
        return "Surat Lamaran " . $num;
    } elseif (stripos($cat, 'pengunduran') !== false) {
        return "Surat Resign " . $num;
    }
    
    return !empty($rawName) ? $rawName : ($cat . ' ' . $num);
}

/**
 * Helper function for product marketing badges (3 categories + polos)
 * 1. NEW (Dynamic: 3 newest templates in each category. Automatically shifts when new templates are added)
 * 2. Best Seller
 * 3. Paling Populer
 * 4. Polos / Clean (Natural balanced distribution)
 */
function resolveProductBadge($productId, $newProductIds = null) {
    if ($newProductIds === null) {
        global $newProductIds;
    }

    // 1. Dynamic NEW badge for the newest templates
    if (!empty($newProductIds) && in_array($productId, $newProductIds)) {
        return [
            'type' => 'new',
            'class' => 'badge-new',
            'text' => 'NEW',
            'icon' => '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2.6 7.4L22 10l-7.4 2.6L12 20l-2.6-7.4L2 10l7.4-2.6L12 0z"/></svg>'
        ];
    }

    // 2. Balanced distribution for remaining items: ~40% badges, ~60% polos
    $hash = ($productId * 2 + 1) % 5;
    
    switch ($hash) {
        case 0:
            return [
                'type' => 'best-seller',
                'class' => 'badge-best-seller',
                'text' => 'Best Seller',
                'icon' => '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.784 1.399 8.166-7.333-3.856-7.333 3.856 1.399-8.166-5.934-5.784 8.2-1.192z"/></svg>'
            ];
        case 1:
            return [
                'type' => 'populer',
                'class' => 'badge-populer',
                'text' => 'Paling Populer',
                'icon' => '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>'
            ];
        default:
            return null; // Polos (tanpa badge)
    }
}

/**
 * Helper function for product features list
 */
function resolveProductFeaturesContext($category_string) {
    $cat = strtolower($category_string);
    if (strpos($cat, 'kreatif') !== false) {
        return [
            'Kilat 30-60 Mnt',
            'Format PDF / PNG',
            'Bebas Revisi'
        ];
    } elseif (strpos($cat, 'ats') !== false) {
        return [
            'Kilat 30-60 Mnt',
            'Format ATS Clean',
            'Format PDF / PNG'
        ];
    } elseif (strpos($cat, 'lamaran') !== false) {
        return [
            'Kilat 30-60 Mnt',
            'Standar PUEBI Formal',
            'Format PDF / PNG'
        ];
    } else {
        return [
            'Kilat 30-60 Mnt',
            'Standar Formal Rapi',
            'Format PDF / PNG'
        ];
    }
}

/**
 * Helper function for decorative badges
 */
function resolveProductBadgeData($id_int, $category_string) {
    $cat = strtolower($category_string);
    if (strpos($cat, 'kreatif') !== false) {
        if ($id_int % 3 == 0) {
            return ['text' => 'BEST SELLER', 'icon' => '🔥', 'class' => 'badge-bestseller'];
        } elseif ($id_int % 2 == 0) {
            return ['text' => 'POPULAR', 'icon' => '⭐', 'class' => 'badge-popular'];
        } else {
            return ['text' => 'TRENDING', 'icon' => '⚡', 'class' => 'badge-trending'];
        }
    }
    if (strpos($cat, 'ats') !== false) {
        return ['text' => 'ATS CLEAN FORMAT', 'icon' => '🎯', 'class' => 'badge-ats'];
    }
    if (strpos($cat, 'lamaran') !== false) {
        return ['text' => 'PUEBI FORMAL', 'icon' => '💼', 'class' => 'badge-hrd'];
    }
    return ['text' => 'STANDAR FORMAL', 'icon' => '🛡️', 'class' => 'badge-formal'];
}

function resolveProductBadgeContext($id_int, $category_string) {
    $data = resolveProductBadgeData($id_int, $category_string);
    return $data['text'];
}

function resolveCategoryTagData($category_string) {
    $cat = strtolower($category_string);
    if (strpos($cat, 'kreatif') !== false) {
        return ['class' => 'cat-kreatif', 'format' => 'PDF / PNG'];
    } elseif (strpos($cat, 'ats') !== false) {
        return ['class' => 'cat-ats', 'format' => 'PDF ATS'];
    } elseif (strpos($cat, 'lamaran') !== false) {
        return ['class' => 'cat-lamaran', 'format' => 'PDF / PNG'];
    } else {
        return ['class' => 'cat-resign', 'format' => 'PDF / PNG'];
    }
}

/**
 * Helper function for category icons
 */
function resolveCategoryIcon($cat) {
    $cat_lower = strtolower($cat);
    if (strpos($cat_lower, 'kreatif') !== false) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    }
    if (strpos($cat_lower, 'ats') !== false) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>';
    }
    if (strpos($cat_lower, 'lamaran') !== false) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>';
    }
    if (strpos($cat_lower, 'pengunduran') !== false || strpos($cat_lower, 'resign') !== false) {
        return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    }
    return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SeaCV - Jasa Pembuatan CV Kreatif, ATS Friendly & Surat Lamaran Kerja Profesional</title>
    
    <meta name="description" content="SeaCV menyediakan layanan pembuatan CV Kreatif, CV ATS Friendly, dan Surat Lamaran Kerja berstandar HRD profesional dengan pengerjaan kilat 30-60 menit." />
    <meta name="keywords" content="cv, cv kreatif, cv ats friendly, surat lamaran kerja, resume builder, seacv, jasa buat cv, resume indonesia" />
    <meta name="author" content="SeaCV" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="logo.png" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700;800;900&display=swap" rel="stylesheet" />
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --primary-border: #bfdbfe;
            --secondary: #4f46e5;
            --accent-green: #10b981;
            --accent-green-hover: #059669;
            --accent-wa: #25D366;
            --accent-danger: #ef4444;
            
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-subtle: #f1f5f9;
            
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-pill: 9999px;
            
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.06), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
            --shadow-card-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            --shadow-float: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --font-heading: 'Outfit', 'Poppins', -apple-system, sans-serif;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 96px;
        }

        section[id], div[id] {
            scroll-margin-top: 96px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            outline: none;
        }

        /* Custom Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Top Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0 6%;
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-subtle);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 2px 8px rgba(37, 99, 235, 0.2));
            transition: transform var(--transition);
        }

        .brand-logo:hover .brand-logo-img {
            transform: scale(1.05);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-text h1 {
            font-family: 'Outfit', 'Poppins', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            line-height: 1;
            margin: 0;
            padding: 0;
            color: var(--primary);
        }

        .brand-text .logo-sea,
        .brand-text .logo-cv {
            color: var(--primary);
        }

        .brand-text span {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
            line-height: 1;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-link-item {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .nav-link-item:hover {
            color: var(--primary);
        }

        /* Prominent Blue CTA Button with Vibration Animation */
        .nav-cta-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
            padding: 8px 18px !important;
            border-radius: 30px !important;
            font-weight: 700 !important;
            font-size: 0.88rem !important;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.42);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.25);
            animation: ctaVibrate 2.8s infinite ease-in-out;
            transform-origin: center center;
            will-change: transform;
            transition: background 0.2s ease, box-shadow 0.2s ease;
        }

        .nav-cta-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.6);
            animation: ctaVibrateHover 0.35s infinite linear;
        }

        .nav-cta-btn:active {
            transform: scale(0.96);
        }

        @keyframes ctaVibrate {
            0%, 100% { transform: scale(1) rotate(0deg); }
            3% { transform: scale(1.05) translate(-1.5px, 1px) rotate(-2deg); }
            6% { transform: scale(1.05) translate(1.5px, -1px) rotate(2deg); }
            9% { transform: scale(1.05) translate(-1.5px, -1px) rotate(-2deg); }
            12% { transform: scale(1.05) translate(1.5px, 1px) rotate(2deg); }
            15% { transform: scale(1.02) translate(-0.5px, 0) rotate(-1deg); }
            18% { transform: scale(1) translate(0, 0) rotate(0deg); }
        }

        @keyframes ctaVibrateHover {
            0% { transform: scale(1.06) translate(0, 0) rotate(0deg); }
            25% { transform: scale(1.06) translate(-1.5px, 1px) rotate(-2deg); }
            50% { transform: scale(1.06) translate(1.5px, -1px) rotate(2deg); }
            75% { transform: scale(1.06) translate(-1px, -1px) rotate(-1.5deg); }
            100% { transform: scale(1.06) translate(0, 0) rotate(0deg); }
        }

        @media (max-width: 768px) {
            .navbar {
                height: 64px;
                padding: 0 14px;
            }
            .nav-links { display: none; }
            .brand-logo {
                gap: 8px;
                transform: none;
            }
            .brand-logo-img {
                width: 36px;
                height: 36px;
            }
            .brand-text {
                transform: none;
            }
            .brand-text h1 {
                font-size: 1.25rem;
                line-height: 1;
            }
            .brand-text span {
                font-size: 0.58rem;
                letter-spacing: 1px;
            }
            .nav-actions {
                gap: 8px;
            }
            .nav-cta-btn {
                padding: 7px 12px !important;
                font-size: 0.78rem !important;
                border-radius: 20px !important;
                box-shadow: 0 3px 10px rgba(37, 99, 235, 0.35) !important;
            }
            .cart-pill-btn {
                padding: 7px 12px;
                font-size: 0.78rem;
                gap: 6px;
            }
            .cart-badge {
                min-width: 18px;
                height: 18px;
                font-size: 0.7rem;
                padding: 0 5px;
            }
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cart-pill-btn {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 9px 18px;
            border-radius: var(--radius-pill);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: var(--shadow-subtle);
            animation: gentleFloat 3s ease-in-out infinite;
        }

        .cart-pill-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary-border);
            color: var(--primary);
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.16);
            animation-play-state: paused;
        }

        .cart-pill-btn:active {
            transform: scale(0.95);
        }

        .cart-badge {
            background: var(--primary);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: var(--radius-pill);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .cart-badge.bump {
            animation: badgePop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .admin-link-btn {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .admin-link-btn:hover {
            color: var(--primary);
            background: var(--bg-subtle);
        }

        /* Hero Showcase Slider */
        .hero-section {
            padding: 24px 6% 12px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .hero-banner-card {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            position: relative;
            box-shadow: var(--shadow-card);
        }

        .slider-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 1366 / 573;
            overflow: hidden;
            background: #ffffff;
        }

        .slider-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .slider-slide.active {
            opacity: 1;
        }

        .slider-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
            transition: transform 6s cubic-bezier(0.16, 1, 0.3, 1);
            transform: scale(1);
        }

        .slider-slide.active img {
            transform: scale(1.035);
        }

        .slider-dots {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 20;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            padding: 5px 12px;
            border-radius: var(--radius-pill);
        }

        .slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .slider-dot.active {
            background: #ffffff;
            width: 24px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.7);
        }

        /* Metrics Strip */
        .metrics-section {
            max-width: 1400px;
            margin: 36px auto 0;
            padding: 0 6%;
            scroll-margin-top: 96px;
        }

        .metrics-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .metrics-header h3 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .metrics-header p {
            font-size: 0.88rem;
            color: var(--text-muted);
            max-width: 520px;
            margin: 0 auto;
        }

        /* Metrics Keunggulan: Desktop & Laptop (Grid of 4 Cards) */
        .metrics-accordion {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 1080px) and (min-width: 769px) {
            .metrics-accordion {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .accordion-item {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px 20px;
            box-shadow: var(--shadow-subtle);
            display: flex;
            flex-direction: column;
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s ease, border-color 0.28s ease;
        }

        .accordion-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.4);
        }

        .accordion-item.active {
            border-color: var(--border-color);
            box-shadow: var(--shadow-subtle);
        }

        .accordion-item.active:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.4);
        }

        .accordion-header {
            padding: 0;
            background: transparent;
            border: none;
            outline: none;
            cursor: default;
            pointer-events: none;
            text-align: left;
            width: 100%;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .accordion-header-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
            width: 100%;
        }

        .accordion-header-left .metric-icon {
            width: 46px;
            height: 46px;
            background: var(--primary-light);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
            transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
        }

        .accordion-item:hover .accordion-header-left .metric-icon {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .accordion-header-left h4 {
            font-family: var(--font-heading);
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.35;
        }

        .accordion-chevron {
            display: none !important;
        }

        .accordion-body {
            max-height: none !important;
            overflow: visible !important;
            padding: 0 !important;
            opacity: 1 !important;
            display: block !important;
            flex: 1;
        }

        .accordion-body p {
            font-size: 0.88rem;
            line-height: 1.55;
            color: var(--text-muted);
            margin: 0;
        }

        /* Testimonials Section */
        .testimonials-section {
            max-width: 1400px;
            margin: 10px auto 64px;
            padding: 0 6%;
            scroll-margin-top: 96px;
        }

        .testimonials-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .testimonials-badge {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: var(--radius-pill);
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .testimonials-header h3 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .testimonials-header p {
            font-size: 0.88rem;
            color: var(--text-muted);
            max-width: 520px;
            margin: 0 auto;
        }

        /* Testimonials Slider (Desktop 3 visible, auto-sliding) */
        .testimonials-slider-container {
            position: relative;
            width: 100%;
        }

        .testimonials-slider-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 8px 4px 16px;
        }

        .testimonials-track {
            display: flex;
            gap: 20px;
            width: 100%;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
            will-change: transform;
        }

        .testimonial-card {
            flex: 0 0 calc((100% - 40px) / 3);
            width: calc((100% - 40px) / 3);
            min-width: calc((100% - 40px) / 3);
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: var(--shadow-subtle);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            box-sizing: border-box;
            user-select: none;
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-card);
            border-color: var(--primary-border);
        }

        /* Desktop Nav Arrows */
        .testi-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
            z-index: 10;
        }

        .testi-nav-btn:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.3);
            transform: translateY(-50%) scale(1.08);
        }

        .testi-nav-prev {
            left: -22px;
        }

        .testi-nav-next {
            right: -22px;
        }

        /* Dots Indicator */
        .testimonials-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
        }

        .testi-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .testi-dot.active {
            width: 24px;
            border-radius: 12px;
            background: var(--primary);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
        }

        @media (max-width: 1024px) and (min-width: 769px) {
            .testimonial-card {
                flex: 0 0 calc((100% - 20px) / 2);
                width: calc((100% - 20px) / 2);
                min-width: calc((100% - 20px) / 2);
            }
            .testimonials-track {
                gap: 20px;
            }
            .testi-nav-prev { left: -14px; }
            .testi-nav-next { right: -14px; }
        }

        .testimonial-rating {
            display: flex;
            gap: 2px;
            margin-bottom: 12px;
        }

        .testimonial-rating .star {
            color: #f59e0b;
            font-size: 1.1rem;
        }

        .testimonial-text {
            font-size: 0.86rem;
            color: #334155;
            line-height: 1.6;
            margin-bottom: 16px;
            flex-grow: 1;
            font-style: italic;
        }

        .testimonial-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }

        .testimonial-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
            background: #f1f5f9;
            flex-shrink: 0;
        }

        .user-info h5 {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.2;
        }

        .user-info span {
            font-size: 0.76rem;
            color: var(--text-muted);
            display: block;
            margin-top: 2px;
        }

        /* Main Catalog Workspace */
        .catalog-workspace {
            max-width: 1400px;
            margin: 40px auto 80px;
            padding: 0 6%;
        }

        .catalog-header-panel {
            position: relative;
            background: linear-gradient(135deg, #070d1e 0%, #0d1b3e 50%, #0f2757 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: var(--radius-lg);
            padding: 34px 38px;
            margin-bottom: 32px;
            box-shadow: 0 16px 36px -4px rgba(7, 13, 30, 0.45);
            overflow: hidden;
        }

        .catalog-header-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('batik-megamendung.jpg');
            background-size: cover;
            background-position: center 40%;
            background-repeat: no-repeat;
            opacity: 0.42;
            pointer-events: none;
            z-index: 0;
            filter: contrast(1.15) saturate(1.25);
        }

        .catalog-header-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(7, 13, 30, 0.90) 0%, rgba(13, 27, 62, 0.62) 50%, rgba(7, 13, 30, 0.85) 100%),
                        linear-gradient(180deg, rgba(7, 13, 30, 0.35) 0%, rgba(7, 13, 30, 0.8) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .catalog-panel-top,
        .category-pills-row {
            position: relative;
            z-index: 1;
        }

        .catalog-panel-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .catalog-title-group h2 {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .catalog-title-group p {
            font-size: 0.9rem;
            color: #cbd5e1;
            line-height: 1.4;
        }

        .catalog-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 8px 18px;
            border-radius: var(--radius-pill);
            font-size: 0.82rem;
            color: #ffffff;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #34d399;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.35);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.5); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }

        /* Category Filter Tabs */
        .category-pills-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .category-pill {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            padding: 10px 20px;
            font-size: 0.86rem;
            font-weight: 600;
            border-radius: var(--radius-pill);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .category-pill svg {
            color: #93c5fd;
            transition: var(--transition);
        }

        .category-pill:hover {
            background: rgba(255, 255, 255, 0.22);
            border-color: rgba(255, 255, 255, 0.35);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .category-pill.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-color: rgba(255, 255, 255, 0.35);
            color: #ffffff;
            box-shadow: 0 4px 18px rgba(37, 99, 235, 0.55);
        }

        .category-pill.active svg {
            color: #ffffff;
        }

        .category-filter-guide {
            display: none;
        }

        .category-check-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #ffffff;
            color: #2563eb;
            font-size: 10px;
            font-weight: 900;
            margin-left: 4px;
            flex-shrink: 0;
        }

        /* Product Catalog Grid */
        .product-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
        }

        @media (min-width: 640px) { .product-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1360px) { .product-grid { grid-template-columns: repeat(4, 1fr); } }

        /* Load More / Tampilkan Lebih Banyak Controls */
        .load-more-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 38px;
            margin-bottom: 24px;
            width: 100%;
        }

        .btn-load-more {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            color: #0f172a;
            border: 2px solid #e2e8f0;
            padding: 13px 28px;
            border-radius: 40px;
            font-size: 0.94rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: inherit;
            outline: none;
        }

        .btn-load-more:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #f0f7ff;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.18);
        }

        .btn-load-more:active {
            transform: translateY(0) scale(0.97);
        }

        .btn-load-more-badge {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 20px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.32);
            letter-spacing: 0.3px;
        }

        .btn-load-more-icon {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-load-more:hover .btn-load-more-icon {
            transform: translateY(2px);
        }

        .product-card.product-hidden {
            display: none !important;
        }

        .product-card.product-revealing {
            animation: productCardReveal 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes productCardReveal {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Clean & Large Preview Product Card */
        .product-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px) scale(1.008);
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow: 0 22px 35px -8px rgba(37, 99, 235, 0.18), 0 0 0 1.5px rgba(37, 99, 235, 0.22);
        }

        /* Dynamic Product Marketing Badges (Best Seller, NEW, Paling Populer) */
        .product-corner-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 5;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.68rem;
            font-weight: 800;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        .product-corner-badge.badge-new,
        .product-corner-badge.badge-rekomendasi {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45);
        }

        .product-corner-badge.badge-best-seller {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.45);
        }

        .product-corner-badge.badge-populer {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.45);
        }

        /* Large Image Showcase Stage */
        .product-image-stage {
            width: 100%;
            height: 380px;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 14px 10px;
            border-bottom: 1px solid #edf2f7;
            cursor: pointer;
        }

        /* Diagonal light shimmer sweep on hover */
        .product-image-stage::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: skewX(-25deg);
            pointer-events: none;
            z-index: 3;
            transition: none;
        }

        .product-card:hover .product-image-stage::before {
            left: 220%;
            transition: left 0.85s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cv-mockup-frame {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cv-mockup-frame img {
            max-width: 96%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 6px;
            background: #ffffff;
            box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.16), 0 2px 8px -2px rgba(15, 23, 42, 0.06);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .product-card:hover .cv-mockup-frame img {
            transform: scale(1.04);
            box-shadow: 0 18px 36px -6px rgba(15, 23, 42, 0.25), 0 6px 12px -2px rgba(15, 23, 42, 0.1);
        }

        /* Hover Overlay */
        .stage-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
            z-index: 4;
            pointer-events: none;
        }

        .product-card:hover .stage-overlay {
            opacity: 1;
        }

        .stage-preview-pill {
            background: #ffffff;
            color: #0f172a;
            font-size: 0.84rem;
            font-weight: 700;
            padding: 9px 18px;
            border-radius: var(--radius-pill);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            transform: translateY(6px);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .product-card:hover .stage-preview-pill {
            transform: translateY(0);
        }

        /* Card Information Body (Nama CV + Harga + Actions) */
        .product-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            background: #ffffff;
        }

        /* Title */
        .product-title {
            font-size: 1.02rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.2s ease;
        }

        .product-card:hover .product-title {
            color: #2563eb;
        }

        /* Price */
        .product-price {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 14px;
        }

        .price-coret {
            font-family: var(--font-heading);
            font-size: 0.92rem;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 600;
        }

        .price-active {
            display: inline-flex;
            align-items: baseline;
            gap: 3px;
        }

        .price-currency {
            font-family: var(--font-heading);
            font-size: 0.95rem;
            font-weight: 700;
            color: #2563eb;
        }

        .price-val {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
        }

        /* Action Buttons Dock */
        .product-actions-dock {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            gap: 8px;
            margin-top: auto;
        }

        .btn-order-wa {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 10px 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, background 0.2s ease;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
            text-decoration: none;
            border: none;
            white-space: nowrap;
        }

        .btn-order-wa:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.35);
            color: #ffffff;
        }

        .btn-order-wa:active {
            transform: scale(0.95);
        }

        .btn-order-wa svg {
            flex-shrink: 0;
            width: 19px;
            height: 19px;
        }

        .btn-add-cart {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 10px 10px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
            white-space: nowrap;
        }

        .btn-add-cart:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.3);
        }

        .btn-add-cart:active {
            transform: scale(0.95);
        }

        .btn-add-cart svg {
            flex-shrink: 0;
            transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-add-cart:hover svg {
            transform: scale(1.18);
        }

        /* Empty State */
        .empty-state-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 60px 20px;
            text-align: center;
            grid-column: 1 / -1;
        }

        .empty-state-card span {
            font-size: 3rem;
            display: block;
            margin-bottom: 12px;
        }

        .empty-state-card h3 {
            font-size: 1.25rem;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .empty-state-card p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Cart Sidebar Drawer */
        .cart-drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .cart-drawer-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .cart-drawer {
            position: fixed;
            top: 0;
            right: -420px;
            width: 100%;
            max-width: 400px;
            height: 100%;
            background: #ffffff;
            z-index: 2001;
            padding: 30px 24px;
            display: flex;
            flex-direction: column;
            transition: right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-float);
        }

        .cart-drawer.active {
            right: 0;
        }

        .cart-drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
        }

        .cart-drawer-header h3 {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .cart-close-btn {
            font-size: 1.6rem;
            color: var(--text-muted);
            cursor: pointer;
            line-height: 1;
            transition: var(--transition);
        }

        .cart-close-btn:hover {
            color: var(--accent-danger);
        }

        .cart-items-container {
            flex-grow: 1;
            overflow-y: auto;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-right: 4px;
        }

        .cart-item-card {
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .cart-item-info h5 {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .cart-item-info p {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .cart-item-remove-btn {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .cart-item-remove-btn:hover {
            color: var(--accent-danger);
            background: #fee2e2;
        }

        .cart-drawer-footer {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .cart-total-row span {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .cart-total-row strong {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            color: var(--text-main);
        }

        .btn-checkout-mass-wa {
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 14px;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-checkout-mass-wa:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
        }

        /* Lightbox Image Preview Modal */
        .lightbox-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .lightbox-modal.active {
            display: flex;
        }

        .lightbox-content {
            max-width: 90%;
            max-height: 85vh;
            object-fit: contain;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-float);
            animation: zoomModal 0.25s ease-out;
        }

        @keyframes zoomModal {
            from { transform: scale(0.94); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .lightbox-close {
            position: absolute;
            top: 24px;
            right: 32px;
            color: #ffffff;
            font-size: 40px;
            cursor: pointer;
            transition: var(--transition);
            line-height: 1;
        }

        .lightbox-close:hover {
            color: var(--accent-danger);
            transform: scale(1.1);
        }

        /* Workflow Steps */
        /* Workflow Steps Panel - Harmonized with Catalog Header Panel */
        .workflow-section {
            max-width: 1400px;
            margin: 20px auto 60px;
            padding: 0 6%;
            scroll-margin-top: 96px;
        }

        .workflow-card-panel {
            position: relative;
            background: linear-gradient(135deg, #070d1e 0%, #0d1b3e 50%, #0f2757 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: var(--radius-lg);
            padding: 42px 38px 44px;
            box-shadow: 0 16px 36px -4px rgba(7, 13, 30, 0.35);
            overflow: hidden;
            text-align: center;
        }

        .workflow-card-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('batik-megamendung.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            opacity: 0.42;
            pointer-events: none;
            z-index: 0;
            filter: contrast(1.15) saturate(1.25);
        }

        .workflow-card-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(7, 13, 30, 0.90) 0%, rgba(13, 27, 62, 0.62) 50%, rgba(7, 13, 30, 0.85) 100%),
                        linear-gradient(180deg, rgba(7, 13, 30, 0.35) 0%, rgba(7, 13, 30, 0.8) 100%);
            pointer-events: none;
            z-index: 0;
        }

        @media (max-width: 768px) {
            .workflow-card-panel {
                padding: 30px 20px 34px;
            }
        }

        .workflow-container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .section-header {
            margin-bottom: 26px;
        }

        .section-header h3 {
            font-family: var(--font-heading);
            font-size: 1.65rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        .section-header p {
            color: #93c5fd;
            font-size: 0.92rem;
            font-weight: 500;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .steps-grid { grid-template-columns: 1fr; }
        }

        .step-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.85);
            border-radius: var(--radius-lg);
            padding: 22px 20px;
            text-align: center;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
        }

        .step-card:hover {
            transform: translateY(-6px) scale(1.015);
            box-shadow: 0 16px 32px -4px rgba(37, 99, 235, 0.18);
            border-color: rgba(37, 99, 235, 0.35);
            background: #ffffff;
        }

        .step-number {
            width: 38px;
            height: 38px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.05rem;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
            animation: pulseRing 2.4s infinite;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .step-card:hover .step-number {
            transform: scale(1.14) rotate(8deg);
        }

        .step-card h4 {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .step-card p {
            font-size: 0.84rem;
            color: var(--text-muted);
            line-height: 1.45;
        }

        /* Footer Minimal */
        .footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 18px 6%;
            text-align: center;
        }

        .footer-copy {
            color: #94a3b8;
            font-size: 0.82rem;
            margin: 0;
            font-weight: 500;
        }

        /* Khusus Android & Mobile: Sembunyikan Footer */
        .is-android .footer,
        .android-hidden {
            display: none !important;
        }

        /* SweetAlert2 Styling */
        .swal2-popup {
            border-radius: var(--radius-lg) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            box-shadow: var(--shadow-float) !important;
        }

        /* ============================================================
           COMPREHENSIVE WEBSITE ANIMATION SYSTEM
           ============================================================ */

        /* Top Scroll Reading Progress Bar */
        .scroll-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3.5px;
            background: linear-gradient(90deg, #2563eb 0%, #38bdf8 50%, #10b981 100%);
            z-index: 99999;
            width: 0%;
            transition: width 0.08s ease-out;
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.6);
            pointer-events: none;
        }

        /* Floating Back To Top Button */
        .back-to-top-btn {
            position: fixed;
            bottom: 96px;
            right: 26px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.38);
            cursor: pointer;
            z-index: 850;
            opacity: 0;
            visibility: hidden;
            transform: translateY(18px) scale(0.85);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            outline: none;
        }

        .back-to-top-btn.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .back-to-top-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-4px) scale(1.08);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.5);
        }

        .back-to-top-btn:active {
            transform: translateY(-1px) scale(0.95);
        }

        /* Floating WhatsApp Button (Toggles AI Chatbot) */
        .floating-wa-btn {
            position: fixed;
            bottom: 26px;
            right: 26px;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4), 0 0 0 1.5px rgba(37, 211, 102, 0.25);
            cursor: pointer;
            z-index: 890;
            border: none;
            outline: none;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        .floating-wa-btn:hover {
            transform: scale(1.08) translateY(-3px);
            box-shadow: 0 12px 32px rgba(37, 211, 102, 0.55), 0 0 0 2px rgba(37, 211, 102, 0.4);
        }

        .floating-wa-btn:active {
            transform: scale(0.95);
        }

        .floating-wa-btn svg {
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.15));
        }

        .floating-wa-btn .wa-pulse-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            border: 2px solid #25D366;
            animation: waPulse 2.4s infinite;
            pointer-events: none;
            opacity: 0.7;
        }

        @keyframes waPulse {
            0% { transform: scale(0.95); opacity: 0.7; }
            70% { transform: scale(1.35); opacity: 0; }
            100% { transform: scale(1.35); opacity: 0; }
        }

        .floating-wa-btn .wa-ai-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            background: linear-gradient(135deg, #091a3c 0%, #1e3a8a 100%);
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            padding: 2.5px 5.5px;
            border-radius: 8px;
            border: 1.5px solid #ffffff;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 2px;
            line-height: 1;
        }

        /* AI Chatbot Widget Modal */
        .ai-chat-widget {
            position: fixed;
            bottom: 92px;
            right: 24px;
            width: 360px;
            max-width: calc(100vw - 32px);
            height: 480px;
            max-height: calc(100vh - 110px);
            max-height: calc(100dvh - 110px);
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(226, 232, 240, 0.9);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transform: translateY(24px) scale(0.92);
            transform-origin: bottom right;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .ai-chat-widget.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .ai-chat-header {
            background: linear-gradient(135deg, #091a3c 0%, #0d275c 100%);
            color: #ffffff;
            padding: 15px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            flex-shrink: 0;
        }

        .ai-chat-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(56, 189, 248, 0.22) 0%, transparent 60%);
            pointer-events: none;
        }

        .ai-chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .ai-chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
            padding: 4px;
        }

        .ai-chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .ai-chat-avatar .online-indicator {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            border: 2px solid #091a3c;
        }

        .ai-chat-title-wrap h4 {
            margin: 0;
            font-size: 14.5px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ai-chat-title-wrap span.bot-pill {
            font-size: 9px;
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            padding: 1px 6px;
            border-radius: 6px;
            font-weight: 700;
            border: 1px solid rgba(56, 189, 248, 0.4);
        }

        .ai-chat-title-wrap p {
            margin: 2px 0 0 0;
            font-size: 11.5px;
            color: #94a3b8;
        }

        .ai-chat-header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            position: relative;
            z-index: 1;
        }

        .ai-chat-close-btn {
            background: rgba(255, 255, 255, 0.12);
            border: none;
            color: #ffffff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ai-chat-close-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }

        .ai-chat-body {
            flex: 1 1 auto;
            min-height: 0;
            padding: 16px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 12px;
            scroll-behavior: smooth;
        }

        .ai-chat-body::-webkit-scrollbar {
            width: 5px;
        }
        .ai-chat-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .chat-message {
            display: flex;
            flex-direction: column;
            max-width: 86%;
            animation: chatFadeIn 0.3s ease;
        }

        @keyframes chatFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chat-message.bot {
            align-self: flex-start;
        }

        .chat-message.user {
            align-self: flex-end;
        }

        .chat-bubble {
            padding: 11px 14px;
            border-radius: 16px;
            font-size: 13px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .chat-message.bot .chat-bubble {
            background: #ffffff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .chat-message.user .chat-bubble {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.28);
        }

        .chat-time {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 4px;
            align-self: flex-start;
        }

        .chat-message.user .chat-time {
            align-self: flex-end;
        }

        .chat-quick-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .chat-chip {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }

        .chat-chip:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .chat-connect-wa-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #25D366;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 12.5px;
            padding: 9px 14px;
            border-radius: 12px;
            text-decoration: none;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.35);
            transition: all 0.2s ease;
        }

        .chat-connect-wa-btn:hover {
            background: #1eb956;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.5);
        }

        /* Typing indicator animation */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            border-bottom-left-radius: 4px;
            width: fit-content;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .typing-dot {
            width: 6.5px;
            height: 6.5px;
            background: #94a3b8;
            border-radius: 50%;
            animation: typingBounce 1.3s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingBounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.4; }
            40% { transform: translateY(-5px); opacity: 1; }
        }

        .ai-chat-footer {
            padding: 12px 14px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .ai-chat-input-form {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 4px 6px 4px 14px;
            border-radius: 30px;
            border: 1px solid #cbd5e1;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .ai-chat-input-form:focus-within {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .ai-chat-input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 13px;
            color: #0f172a;
            outline: none;
            padding: 6px 0;
            font-family: inherit;
        }

        .ai-chat-input::placeholder {
            color: #94a3b8;
        }

        .ai-chat-send-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .ai-chat-send-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .ai-chat-footer-note {
            text-align: center;
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 6px;
        }

        @media (max-width: 640px) {
            /* Hero Banner Slider */
            .hero-section {
                padding: 10px 14px 4px;
            }
            .hero-banner-card {
                border-radius: 14px;
            }
            .slider-dots {
                bottom: 8px;
                right: 12px;
                left: auto;
                transform: none;
                padding: 3px 8px;
                gap: 5px;
            }
            .slider-dot {
                width: 6px;
                height: 6px;
            }
            .slider-dot.active {
                width: 14px;
                border-radius: 4px;
            }

            /* Metrics / Keunggulan */
            .metrics-section {
                margin: 20px auto 0;
                padding: 0 14px;
            }
            .metrics-header {
                margin-bottom: 18px;
            }
            .metrics-header h3 {
                font-size: 1.28rem;
            }
            .metrics-header p {
                font-size: 0.8rem;
                line-height: 1.45;
            }
            /* Metrics / Keunggulan Dropdown Accordion (Mobile Only) */
            .metrics-accordion {
                display: flex;
                flex-direction: column;
                gap: 10px;
                max-width: 100%;
            }
            .accordion-item {
                padding: 0;
                border-radius: 12px;
                overflow: hidden;
                display: block;
                transform: none !important;
            }
            .accordion-item:hover {
                transform: none !important;
                box-shadow: var(--shadow-subtle);
                border-color: var(--border-color);
            }
            .accordion-item.active {
                border-color: var(--primary);
                box-shadow: 0 8px 24px rgba(37, 99, 235, 0.12);
            }
            .accordion-header {
                padding: 13px 14px;
                cursor: pointer;
                pointer-events: auto;
                margin-bottom: 0;
                justify-content: space-between;
                gap: 10px;
                -webkit-tap-highlight-color: transparent;
            }
            .accordion-header-left {
                flex-direction: row;
                align-items: center;
                gap: 11px;
                width: auto;
            }
            .accordion-header-left .metric-icon {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                transform: none !important;
                box-shadow: none !important;
            }
            .accordion-item:hover .accordion-header-left .metric-icon {
                background: var(--primary-light);
                color: var(--primary);
                transform: none !important;
                box-shadow: none !important;
            }
            .accordion-item.active .accordion-header-left .metric-icon {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
                color: #ffffff !important;
                transform: scale(1.05) !important;
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
            }
            .accordion-header-left .metric-icon svg {
                width: 18px;
                height: 18px;
            }
            .accordion-header-left h4 {
                font-size: 0.92rem;
                line-height: 1.25;
            }
            .accordion-item.active .accordion-header-left h4 {
                color: var(--primary);
            }
            .accordion-chevron {
                display: flex !important;
                color: var(--text-muted);
                transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease, background 0.2s ease;
                flex-shrink: 0;
                align-items: center;
                justify-content: center;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: #f8fafc;
            }
            .accordion-chevron svg {
                width: 14px;
                height: 14px;
            }
            .accordion-item.active .accordion-chevron {
                transform: rotate(180deg);
                color: #ffffff;
                background: var(--primary);
            }
            .accordion-body {
                max-height: 0 !important;
                overflow: hidden !important;
                transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), padding 0.3s ease;
                padding: 0 14px !important;
            }
            .accordion-item.active .accordion-body {
                max-height: 260px !important;
                padding: 0 14px 14px 61px !important;
            }
            .accordion-body p {
                font-size: 0.82rem;
                line-height: 1.5;
            }

            /* Testimonials (1 per 1 Auto-Horizontal Slide on Mobile) */
            .testimonials-section {
                padding: 0 14px;
                margin: 20px auto 32px;
            }
            .testimonials-header h3 {
                font-size: 1.25rem;
            }
            .testimonials-header p {
                font-size: 0.8rem;
                line-height: 1.45;
            }
            .testimonials-slider-wrapper {
                position: relative;
                overflow: hidden !important;
                width: 100%;
                padding: 4px 0 8px;
            }
            .testimonials-grid,
            .testimonials-track {
                display: flex !important;
                grid-template-columns: none !important;
                gap: 0 !important;
                width: 100%;
                transition: transform 0.55s cubic-bezier(0.25, 1, 0.5, 1);
            }
            .testimonial-card {
                flex: 0 0 100% !important;
                width: 100% !important;
                min-width: 100% !important;
                padding: 20px 18px;
                border-radius: 14px;
                box-sizing: border-box;
                box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            }
            .testimonial-card:hover {
                transform: none;
            }
            .testi-nav-btn {
                display: none !important;
            }
            .testimonial-rating {
                display: flex;
                gap: 3px;
                margin-bottom: 10px;
            }
            .testimonial-rating .star {
                font-size: 1rem;
                color: #f59e0b;
            }
            .testimonial-text {
                font-size: 0.86rem;
                line-height: 1.55;
                margin-bottom: 14px;
                font-style: italic;
                color: #334155;
            }
            .testimonial-user {
                display: flex;
                align-items: center;
                gap: 10px;
                padding-top: 10px;
                border-top: 1px solid #f1f5f9;
            }
            .testimonial-avatar {
                width: 38px;
                height: 38px;
                border-radius: 50%;
            }
            .user-info h5 {
                font-size: 0.88rem;
                font-weight: 700;
                line-height: 1.2;
            }
            .user-info span {
                font-size: 0.74rem;
                color: var(--text-muted);
                margin-top: 2px;
            }
            .testimonials-dots {
                display: flex !important;
                align-items: center;
                justify-content: center;
                gap: 5px;
                margin-top: 14px;
            }
            .testi-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #cbd5e1;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            .testi-dot.active {
                width: 18px;
                border-radius: 10px;
                background: #2563eb;
                box-shadow: 0 2px 6px rgba(37, 99, 235, 0.4);
            }

            /* Catalog Workspace & Filter Tabs */
            .catalog-workspace {
                margin: 20px auto 40px;
                padding: 0 10px;
            }
            .catalog-header-panel {
                padding: 18px 14px;
                border-radius: 14px;
                margin-bottom: 18px;
            }
            .catalog-panel-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 14px;
            }
            .catalog-title-group h2 {
                font-size: 1.25rem;
                margin-bottom: 4px;
            }
            .catalog-title-group p {
                font-size: 0.8rem;
            }
            .catalog-count-badge {
                font-size: 0.76rem;
                padding: 5px 12px;
            }
            .category-filter-guide {
                display: flex !important;
                align-items: center;
                gap: 6px;
                font-size: 0.74rem;
                font-weight: 700;
                color: #93c5fd;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 10px;
            }
            .category-pills-row {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
                width: 100% !important;
                padding: 0 !important;
                overflow-x: visible !important;
            }
            .category-pills-row form {
                display: flex !important;
                width: 100% !important;
                margin: 0 !important;
            }
            .category-pill {
                width: 100% !important;
                padding: 11px 8px !important;
                font-size: 0.78rem !important;
                border-radius: 12px !important;
                justify-content: center !important;
                text-align: center !important;
                white-space: nowrap !important;
                box-sizing: border-box !important;
                gap: 6px !important;
                flex-shrink: 0;
            }
            .category-pill.active {
                background: #2563eb !important;
                border-color: #60a5fa !important;
                color: #ffffff !important;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45) !important;
                font-weight: 700 !important;
            }

            /* Product Grid & Cards (3 Kolom Sejajar Khusus Mobile) */
            .product-grid {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 8px;
            }
            @media (max-width: 325px) {
                .product-grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }
            .product-card {
                border-radius: 10px;
            }
            .product-card:hover {
                transform: none;
            }
            .product-corner-badge {
                top: 4px;
                left: 4px;
                padding: 2px 4px;
                font-size: 0.52rem;
                gap: 2px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            }
            .product-corner-badge svg {
                width: 7px;
                height: 7px;
            }
            .product-image-stage {
                height: 140px;
                padding: 5px 4px 4px;
            }
            .cv-mockup-frame img {
                border-radius: 4px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }
            .stage-preview-pill {
                display: none !important;
            }
            .product-body {
                padding: 6px 5px 8px;
            }
            .product-title {
                font-size: 0.72rem;
                margin-bottom: 2px;
                line-height: 1.25;
            }
            .product-price {
                flex-direction: column;
                align-items: flex-start;
                gap: 1px;
                margin-bottom: 6px;
            }
            .price-coret {
                font-size: 0.60rem;
                line-height: 1;
            }
            .price-active {
                gap: 1.5px;
            }
            .price-currency {
                font-size: 0.62rem;
            }
            .price-val {
                font-size: 0.84rem;
            }
            .product-actions-dock {
                grid-template-columns: 1fr 1fr;
                gap: 3px;
            }
            .btn-order-wa {
                padding: 6px 2px;
                font-size: 0.68rem;
                gap: 0;
                border-radius: 6px;
                min-height: 28px;
                justify-content: center;
            }
            .btn-order-wa svg {
                width: 15px;
                height: 15px;
            }
            .btn-order-wa span {
                display: none;
            }
            .btn-add-cart {
                padding: 6px 2px;
                font-size: 0.68rem;
                gap: 0;
                border-radius: 6px;
                min-height: 28px;
                justify-content: center;
            }
            .btn-add-cart svg {
                width: 14px;
                height: 14px;
            }
            .btn-add-cart span {
                display: none;
            }
            .btn-load-more {
                padding: 10px 20px;
                font-size: 0.82rem;
                border-radius: 25px;
                gap: 8px;
            }
            .btn-load-more-badge {
                font-size: 0.7rem;
                padding: 2px 7px;
            }

            /* Workflow / Cara Pemesanan */
            .workflow-workspace {
                padding: 0 14px;
                margin-bottom: 36px;
            }
            .workflow-card-panel {
                padding: 22px 14px;
                border-radius: 14px;
            }
            .section-header h3 {
                font-size: 1.3rem;
            }
            .section-header p {
                font-size: 0.82rem;
            }
            .steps-grid {
                gap: 14px;
            }
            .step-card {
                padding: 16px 14px;
                border-radius: 12px;
            }

            /* Cart Drawer on Mobile */
            .cart-drawer {
                max-width: 100vw;
                padding: 22px 16px;
            }

            /* Floating WhatsApp Button & Widget */
            .floating-wa-btn {
                bottom: 18px;
                right: 16px;
                width: 50px;
                height: 50px;
                padding: 0;
            }
            .floating-wa-btn > svg {
                width: 27px;
                height: 27px;
            }
            .back-to-top-btn {
                bottom: 78px;
                right: 19px;
                width: 38px;
                height: 38px;
            }
            .ai-chat-widget {
                bottom: 76px;
                right: 12px;
                left: 12px;
                width: auto;
                max-width: none;
                height: auto;
                max-height: calc(100vh - 90px);
                max-height: calc(100dvh - 90px);
                border-radius: 18px;
            }

            /* Khusus Mobile & Android: Gausah pakai footer */
            .footer {
                display: none !important;
            }
        }

        /* ============================================================
           MOBILE NATIVE-STYLE APP BOTTOM NAVIGATION BAR
           ============================================================ */
        .mobile-bottom-bar {
            display: none; /* Hidden on Desktop */
        }

        @media (max-width: 768px) {
            body {
                padding-bottom: calc(65px + env(safe-area-inset-bottom, 0px));
            }

            .mobile-bottom-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: calc(60px + env(safe-area-inset-bottom, 0px));
                padding-bottom: env(safe-area-inset-bottom, 0px);
                background: rgba(255, 255, 255, 0.94);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border-top: 1px solid rgba(226, 232, 240, 0.9);
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                align-items: center;
                z-index: 1200;
                box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.08);
            }

            .bottom-bar-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;
                text-decoration: none;
                color: #64748b;
                height: 100%;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                position: relative;
                -webkit-tap-highlight-color: transparent;
            }

            .bottom-bar-item .bottom-bar-icon {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .bottom-bar-item svg {
                transition: transform 0.2s ease, stroke 0.2s ease;
            }

            .bottom-bar-item span {
                font-size: 0.68rem;
                font-weight: 600;
                line-height: 1;
                letter-spacing: 0.2px;
                transition: color 0.2s ease, font-weight 0.2s ease;
            }

            .bottom-bar-item:active {
                transform: scale(0.92);
            }

            .bottom-bar-item.active {
                color: #2563eb;
            }

            .bottom-bar-item.active .bottom-bar-icon {
                background: #eff6ff;
                color: #2563eb;
            }

            .bottom-bar-item.active svg {
                stroke: #2563eb;
                transform: translateY(-1px);
            }

            .bottom-bar-item.active span {
                color: #2563eb;
                font-weight: 800;
            }

            /* Mulai Beli CTA highlight in Bottom Bar */
            .bottom-bar-item.bottom-bar-cta {
                color: #2563eb;
            }

            .bottom-bar-item.bottom-bar-cta .bottom-bar-icon {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                color: #2563eb;
                border: 1px solid rgba(37, 99, 235, 0.2);
            }

            .bottom-bar-item.bottom-bar-cta span {
                font-weight: 800;
                color: #2563eb;
            }

            .bottom-bar-item.bottom-bar-cta.active .bottom-bar-icon {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                color: #ffffff;
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            }

            .bottom-bar-item.bottom-bar-cta.active svg {
                stroke: #ffffff;
            }

            /* Elevate Floating AI Button & Back to Top above bottom bar */
            .floating-ai-btn,
            .floating-wa-btn {
                bottom: calc(72px + env(safe-area-inset-bottom, 0px)) !important;
                right: 16px !important;
            }

            .back-to-top-btn {
                bottom: calc(134px + env(safe-area-inset-bottom, 0px)) !important;
                right: 19px !important;
            }

            .ai-chat-widget {
                bottom: calc(70px + env(safe-area-inset-bottom, 0px)) !important;
            }
        }


        /* Scroll Reveal Elements */
        .reveal-init {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1), transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal-init.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Keyframes */
        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.45); }
            70% { box-shadow: 0 0 0 12px rgba(37, 99, 235, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
        }

        @keyframes badgePop {
            0% { transform: scale(1); }
            40% { transform: scale(1.4) rotate(-8deg); }
            70% { transform: scale(0.92) rotate(4deg); }
            100% { transform: scale(1) rotate(0); }
        }

        /* Metric & Testimonial Card Elevation on Hover */
        .metric-card, .testimonial-card {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
        }

        .metric-card:hover, .testimonial-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 14px 28px -4px rgba(37, 99, 235, 0.12);
            border-color: rgba(37, 99, 235, 0.35);
        }

        /* Reduced Motion Accessibility */
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            .reveal-init {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Scroll Reading Progress Bar -->
    <div class="scroll-progress-bar" id="scrollProgressBar"></div>

    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="brand-logo">
            <img src="logo.png" alt="SeaCV Logo" class="brand-logo-img" />
            <div class="brand-text">
                <h1>SEACV</h1>
                <span>Professional Hub</span>
            </div>
        </a>

        <div class="nav-links">
            <a href="#katalog-layanan" class="nav-link-item nav-cta-btn">Mulai Beli!</a>
            <a href="#keunggulan" class="nav-link-item">Keunggulan</a>
            <a href="#cara-pemesanan" class="nav-link-item">Cara Pemesanan</a>
            <a href="#testimoni" class="nav-link-item">Testimoni</a>
        </div>

        <div class="nav-actions">
            <button type="button" class="cart-pill-btn" onclick="toggleCartDrawer()">
                <span class="cart-label-text">Pesanan Saya</span>
                <span class="cart-badge" id="cartBadgeCount">0</span>
            </button>
        </div>
    </nav>

    <!-- Hero Showcase Slider -->
    <section class="hero-section">
        <div class="hero-banner-card">
            <div class="slider-wrapper" id="heroSlider">
                <div class="slider-slide active">
                    <img src="banner/banner03.webp" alt="SeaCV Banner Showcase 01" />
                </div>
                <div class="slider-slide">
                    <img src="banner/banner01.webp" alt="SeaCV Banner Showcase 02" />
                </div>
                <div class="slider-slide">
                    <img src="banner/banner02.webp" alt="SeaCV Banner Showcase 03" />
                </div>
                <div class="slider-slide">
                    <img src="banner/banner04.webp" alt="SeaCV Banner Showcase 04" />
                </div>
                <div class="slider-dots" id="sliderDots"></div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Layanan Bar -->
    <section class="metrics-section" id="keunggulan">
        <div class="metrics-header">
            <span class="testimonials-badge">Nilai Lebih</span>
            <h3>Keunggulan Layanan SeaCV</h3>
            <p>Standar mutu tinggi untuk menjamin kepuasan, kerahasiaan, dan pengerjaan kilat dokumen lamaran Anda</p>
        </div>
        <div class="metrics-accordion" id="metricsAccordion">
            <div class="accordion-item active">
                <button type="button" class="accordion-header" onclick="toggleMetricAccordion(this)">
                    <div class="accordion-header-left">
                        <div class="metric-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h4>Keamanan Data & UU PDP</h4>
                    </div>
                    <div class="accordion-chevron">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>
                <div class="accordion-body">
                    <p>Privasi terlindungi penuh berlandaskan UU No. 27 Tahun 2022 (Pelindungan Data Pribadi). Berkas Anda 100% aman, rahasia, dan tidak pernah disebarluaskan.</p>
                </div>
            </div>
            <div class="accordion-item">
                <button type="button" class="accordion-header" onclick="toggleMetricAccordion(this)">
                    <div class="accordion-header-left">
                        <div class="metric-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <h4>Proses Cepat 30–60 Menit</h4>
                    </div>
                    <div class="accordion-chevron">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>
                <div class="accordion-body">
                    <p>Solusi tepat saat deadline lamaran mendesak. Draft langsung diproses kilat oleh tim berpengalaman dengan kualitas tata letak terbaik.</p>
                </div>
            </div>
            <div class="accordion-item">
                <button type="button" class="accordion-header" onclick="toggleMetricAccordion(this)">
                    <div class="accordion-header-left">
                        <div class="metric-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </div>
                        <h4>Bebas Revisi Sampai Cocok</h4>
                    </div>
                    <div class="accordion-chevron">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>
                <div class="accordion-body">
                    <p>Bisa konsultasi dan meminta revisi teks, perbaikan typo, atau penyesuaian detail tanpa biaya tambahan sampai Anda benar-benar yakin dan puas.</p>
                </div>
            </div>
            <div class="accordion-item">
                <button type="button" class="accordion-header" onclick="toggleMetricAccordion(this)">
                    <div class="accordion-header-left">
                        <div class="metric-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <h4>Format PDF / PNG Siap Pakai</h4>
                    </div>
                    <div class="accordion-chevron">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </button>
                <div class="accordion-body">
                    <p>Menerima file PDF kualitas tajam yang siap langsung dipakai melamar kerja, serta bisa meminta format PNG jernih jika dibutuhkan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Catalog Workspace -->
    <main class="catalog-workspace" id="katalog-layanan">
        <div class="catalog-header-panel">
            <!-- Top bar: Title & Product Counter -->
            <div class="catalog-panel-top">
                <div class="catalog-title-group">
                    <h2>Katalog Template & Layanan</h2>
                    <p>Pilih desain profesional yang rapi, modern, dan dirancang khusus agar CV Anda tampil lebih menarik dan elegan.</p>
                </div>
                <div class="catalog-count-badge">
                    <span class="pulse-dot"></span>
                    <span><strong><?= count($products) ?></strong> Template Tersedia</span>
                </div>
            </div>

            <!-- Petunjuk Kategori Gaptek-Friendly -->
            <div class="category-filter-guide">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                <span>Pilih Kategori Layanan:</span>
            </div>

            <!-- Category Filter Pills (2x2 Grid di Mobile, Tanpa Perlu Scroll) -->
            <div class="category-pills-row">
                <form method="get" action="index.php#katalog-layanan">
                    <input type="hidden" name="category" value="Semua" />
                    <button type="submit" class="category-pill <?= ($category_filter === 'Semua') ? 'active' : '' ?>">
                        <?= resolveCategoryIcon('semua') ?>
                        <span>Semua Layanan</span>
                        <?php if ($category_filter === 'Semua'): ?>
                            <span class="category-check-badge">✓</span>
                        <?php endif; ?>
                    </button>
                </form>

                <?php foreach ($categories as $cat_slug): ?>
                    <?php 
                        $labelDisplay = ($cat_slug === 'Surat Lamaran Kerja') ? 'Surat Lamaran' : $cat_slug;
                    ?>
                    <form method="get" action="index.php#katalog-layanan">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($cat_slug) ?>" />
                        <button type="submit" class="category-pill <?= ($category_filter === $cat_slug) ? 'active' : '' ?>">
                            <?= resolveCategoryIcon($cat_slug) ?>
                            <span><?= htmlspecialchars($labelDisplay) ?></span>
                            <?php if ($category_filter === $cat_slug): ?>
                                <span class="category-check-badge">✓</span>
                            <?php endif; ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="product-grid" id="mainProductGrid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $idx => $product): ?>
                    <?php 
                        $displayName = resolveProductDisplayName($product, $idx);
                        $isHiddenClass = ($idx >= 8) ? 'product-hidden' : '';
                        $productBadge = resolveProductBadge($product['id'] ?? $idx, $newProductIds);
                    ?>
                    <div class="product-card <?= $isHiddenClass ?>" data-product-index="<?= $idx ?>">
                        <!-- Large Showcase Image Stage -->
                        <div class="product-image-stage" onclick="openLightbox('<?= htmlspecialchars($product['image']) ?>')">
                            <?php if ($productBadge): ?>
                                <span class="product-corner-badge <?= $productBadge['class'] ?>">
                                    <?= $productBadge['icon'] ?>
                                    <span><?= $productBadge['text'] ?></span>
                                </span>
                            <?php endif; ?>
                            <div class="cv-mockup-frame">
                                <img src="<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($displayName) ?>" 
                                     title="<?= htmlspecialchars($displayName) ?>" 
                                     loading="lazy" />
                            </div>

                            <div class="stage-overlay">
                                <span class="stage-preview-pill">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Pratinjau
                                </span>
                            </div>
                        </div>

                        <!-- Product Information Body: Nama CV & Harga Saja -->
                        <div class="product-body">
                            <h3 class="product-title" title="<?= htmlspecialchars($displayName) ?>"><?= htmlspecialchars($displayName) ?></h3>

                            <div class="product-price">
                                <span class="price-coret">Rp 15.000</span>
                                <div class="price-active">
                                    <span class="price-currency">Rp</span>
                                    <span class="price-val">10.000</span>
                                </div>
                            </div>

                            <div class="product-actions-dock">
                                <a href="https://wa.me/+62895396356914?text=Halo%20Admin%20SeaCV,%20saya%20tertarik%20pesan%20*<?= urlencode($displayName) ?>*%20seharga%20*Rp%2010.000*.%20Mohon%20segera%20diproses." 
                                   class="btn-order-wa" 
                                   target="_blank">
                                    <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12.031 2C6.505 2 2.012 6.487 2.01 12.014a10.015 10.015 0 001.536 5.346L2 22l4.802-1.507A10.01 10.01 0 0012.031 22c5.526 0 10.019-4.487 10.02-10.015A10.027 10.027 0 0012.031 2zm5.836 14.185c-.244.688-1.42 1.314-1.956 1.4-.492.079-1.127.135-3.567-.84-2.946-1.183-4.834-4.184-4.981-4.38-.147-.197-1.194-1.585-1.194-3.023 0-1.439.755-2.146 1.023-2.438.268-.293.585-.366.78-.366.195 0 .39.002.56.01.18.008.42-.068.657.502.244.585.83 2.025.903 2.172.073.147.122.317.024.512-.097.195-.146.317-.292.488-.147.17-.309.38-.44.51-.147.146-.3.305-.13.597.17.293.758 1.25 1.626 2.023 1.118.997 2.06 1.306 2.353 1.452.293.146.464.122.635-.073.17-.195.732-.854.928-1.147.195-.293.39-.244.659-.146.268.098 1.708.805 2 .952.293.146.488.22.561.341.073.122.073.708-.171 1.396z"/>
                                    </svg>
                                    <span>Pesan Cepat</span>
                                </a>
                                <button type="button" 
                                        class="btn-add-cart" 
                                        onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars(addslashes($displayName)) ?>', 10000)">
                                    <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span>+ Keranjang</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state-card">
                    <span>🔍</span>
                    <h3>Tidak ada produk yang ditemukan</h3>
                    <p>Silakan ganti kata kunci pencarian Anda atau pilih kategori lainnya.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($products) > 8): ?>
            <!-- Load More Controls -->
            <div class="load-more-container" id="loadMoreContainer">
                <button type="button" class="btn-load-more" id="btnLoadMore" onclick="handleLoadMoreToggle()">
                    <span class="btn-load-more-text" id="btnLoadMoreText">Tampilkan Lebih Banyak Desain</span>
                    <span class="btn-load-more-badge" id="btnLoadMoreBadge">+<?= (count($products) - 8) ?> Template</span>
                    <svg class="btn-load-more-icon" id="btnLoadMoreIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </main>

    <!-- Workflow Steps Section -->
    <section class="workflow-section" id="cara-pemesanan">
        <div class="workflow-card-panel">
            <div class="workflow-container">
                <div class="section-header">
                    <h3>Cara Mudah Memesan di SeaCV</h3>
                    <p>3 Langkah praktis untuk mendapatkan dokumen lamaran kerja impian Anda</p>
                </div>

                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Pilih Desain</h4>
                        <p>Pilih template CV Kreatif, CV ATS, atau Surat Lamaran yang sesuai dengan industri tujuan Anda.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Kirim Data ke WhatsApp</h4>
                        <p>Klik Pesan Langsung atau pesan via keranjang. Kirimkan data riwayat hidup atau draft lama Anda ke Admin.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>CV Selesai 30-60 Menit</h4>
                        <p>Tim profesional SeaCV mengerjakan CV Anda. Hasil dikirim dalam format PDF berkualitas jernih (bisa minta PNG jika dibutuhkan), siap langsung dipakai melamar kerja!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Social Proof -->
    <section class="testimonials-section" id="testimoni">
        <div class="testimonials-header">
            <span class="testimonials-badge">Ulasan Klien</span>
            <h3>Apa Kata Pelanggan Tentang SeaCV?</h3>
            <p>Ulasan jujur pelanggan tentang pelayanan fast respon, hasil rapi, amanah, dan pengerjaan kilat kami.</p>
        </div>

        <div class="testimonials-slider-container">
            <!-- Desktop Nav Buttons -->
            <button class="testi-nav-btn testi-nav-prev" id="testiPrevBtn" aria-label="Sebelumnya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="testi-nav-btn testi-nav-next" id="testiNextBtn" aria-label="Selanjutnya">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </button>

            <div class="testimonials-slider-wrapper" id="testimonialsSliderWrapper">
                <div class="testimonials-track" id="testimonialsTrack">
                    <!-- 1 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Pengerjaannya beneran kilat gak sampai 1 jam sudah selesai! Format ATS-nya rapi banget dan susunan kalimatnya enak dibaca. Adminnya juga fast respon waktu saya minta ganti warna font."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80" alt="Sarah Aurelia" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Sarah+Aurelia&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Sarah Aurelia</h5>
                                <span>Management Trainee</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Puas banget sama pelayanannya. Adminnya ramah dan fast respon, minta revisi minor langsung ditangani dengan sabar. Data diri amanah dan hasil file PDF-nya sangat jernih serta rapi."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80" alt="Dimas Pratama" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Dimas+Pratama&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Dimas Pratama</h5>
                                <span>Software Engineer</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Desain CV Kreatifnya bagus dan elegan banget, warnanya pas dan gak pasaran. Tatanan teksnya rapi, prosesnya juga cepat tanpa ribet. Recommended banget buat yang mau CV rapi!"</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&auto=format&fit=crop&q=80" alt="Nadira Putri" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Nadira+Putri&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Nadira Putri</h5>
                                <span>Fresh Graduate</span>
                            </div>
                        </div>
                    </div>

                    <!-- 4 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Hasil rombak CV di SeaCV rapi banget. Riwayat kerja dan keahlian yang tadinya berantakan jadi tertata runut dan enak dilihat. Pengerjaannya cepat dan admin komunikatif."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80" alt="Rizky Firmansyah" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Rizky+Firmansyah&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Rizky Firmansyah</h5>
                                <span>Digital Marketing</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Awalnya ragu pesan jasa CV online, ternyata SeaCV amanah banget. File PDF dan PNG yang dikirim rapi banget, jernih tanpa pecah, dan bebas watermark. CS-nya fast respon dan solutif."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&auto=format&fit=crop&q=80" alt="Anisa Rahmawati" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Anisa+Rahmawati&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Anisa Rahmawati</h5>
                                <span>Staff Akuntansi</span>
                            </div>
                        </div>
                    </div>

                    <!-- 6 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Prosesnya cepat dan hasilnya presisi. Format ATS-nya clean, tata letak teks teratur, dan pengerjaannya gak pakai lama. Pelayanan ramah serta amanah, harga juga sangat terjangkau!"</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80" alt="Fajar Ramadhan" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Fajar+Ramadhan&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Fajar Ramadhan</h5>
                                <span>Admin Operasional</span>
                            </div>
                        </div>
                    </div>

                    <!-- 7 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Tipografi dan layout CV Kreatifnya bagus banget! Pemilihan font proporsional, estetika modern dan clean. Pelayanannya kilat, pengerjaan benar-benar profesional."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=120&auto=format&fit=crop&q=80" alt="Bella Savira" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Bella+Savira&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Bella Savira</h5>
                                <span>Graphic Designer</span>
                            </div>
                        </div>
                    </div>

                    <!-- 8 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Gak bertele-tele dan fast respon. Cukup kirim riwayat via WhatsApp, gak sampai 45 menit CV sudah beres dan hasilnya rapi siap pakai. Sangat membantu kalau lagi butuh cepat."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=120&auto=format&fit=crop&q=80" alt="Hendra Setiawan" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Hendra+Setiawan&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Hendra Setiawan</h5>
                                <span>Logistik & Distribusi</span>
                            </div>
                        </div>
                    </div>

                    <!-- 9 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Desainnya bagus dan rapi banget, sertifikasi dan data riwayat saya disusun runtut. Adminnya amanah menjaga kerahasiaan data, respon cepat, dan revisi langsung selesai dalam hitungan menit."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&auto=format&fit=crop&q=80" alt="Citra Kirana" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Citra+Kirana&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Citra Kirana</h5>
                                <span>Tenaga Medis</span>
                            </div>
                        </div>
                    </div>

                    <!-- 10 -->
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span>
                        </div>
                        <p class="testimonial-text">"Pelayanan SeaCV sangat memuaskan. Desain modern, tata letak rapi dan profesional, serta prosesnya kilat. Admin amanah dan fast respon dari awal konsultasi sampai file dikirim."</p>
                        <div class="testimonial-user">
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=120&auto=format&fit=crop&q=80" alt="Kevin Sanjaya" class="testimonial-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Kevin+Sanjaya&background=eff6ff&color=2563eb&bold=true'" />
                            <div class="user-info">
                                <h5>Kevin Sanjaya</h5>
                                <span>Business Development</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dots Indicator -->
        <div class="testimonials-dots" id="testimonialDots"></div>
    </section>

    <!-- Cart Drawer & Overlay -->
    <div class="cart-drawer-overlay" id="cartOverlay" onclick="toggleCartDrawer()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-header">
            <h3>Keranjang Layanan</h3>
            <span class="cart-close-btn" onclick="toggleCartDrawer()">&times;</span>
        </div>

        <div class="cart-items-container" id="cartItemsList"></div>

        <div class="cart-drawer-footer">
            <div class="cart-total-row">
                <span>Subtotal Pesanan:</span>
                <strong id="cartTotalPrice">Rp 0</strong>
            </div>
            <button type="button" class="btn-checkout-mass-wa" onclick="checkoutCartToWhatsApp()">
                <span>Pesan Semua via WhatsApp</span>
            </button>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div class="lightbox-modal" id="imageLightbox" onclick="closeLightbox()">
        <span class="lightbox-close">&times;</span>
        <img class="lightbox-content" id="lightboxTargetImg" alt="Preview Gambar SeaCV" />
    </div>

    <!-- Footer (Hanya tampil di Laptop/Desktop, otomatis disembunyikan di Android & Mobile) -->
    <footer class="footer <?= (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) ? 'android-hidden' : '' ?>">
        <p class="footer-copy">&copy; 2025 - <?= date('Y') ?> SeaCV. All rights reserved.</p>
    </footer>

    <!-- Floating Back to Top Button -->
    <button type="button" class="back-to-top-btn" id="backToTopBtn" title="Kembali ke atas" aria-label="Kembali ke atas">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <!-- Floating WhatsApp Button (Toggles AI Chatbot) -->
    <button type="button" class="floating-wa-btn" id="floatingWaBtn" onclick="toggleAiChat()" title="Tanya AI Konsultan SeaCV" aria-label="Buka Chatbot AI SeaCV">
        <span class="wa-pulse-ring"></span>
        <span class="wa-ai-badge">
            <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0l2.6 7.4L22 10l-7.4 2.6L12 20l-2.6-7.4L2 10l7.4-2.6L12 0z"/>
            </svg>
            AI
        </span>
        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12.031 2C6.505 2 2.012 6.487 2.01 12.014a10.015 10.015 0 001.536 5.346L2 22l4.802-1.507A10.01 10.01 0 0012.031 22c5.526 0 10.019-4.487 10.02-10.015A10.027 10.027 0 0012.031 2zm5.836 14.185c-.244.688-1.42 1.314-1.956 1.4-.492.079-1.127.135-3.567-.84-2.946-1.183-4.834-4.184-4.981-4.38-.147-.197-1.194-1.585-1.194-3.023 0-1.439.755-2.146 1.023-2.438.268-.293.585-.366.78-.366.195 0 .39.002.56.01.18.008.42-.068.657.502.244.585.83 2.025.903 2.172.073.147.122.317.024.512-.097.195-.146.317-.292.488-.147.17-.309.38-.44.51-.147.146-.3.305-.13.597.17.293.758 1.25 1.626 2.023 1.118.997 2.06 1.306 2.353 1.452.293.146.464.122.635-.073.17-.195.732-.854.928-1.147.195-.293.39-.244.659-.146.268.098 1.708.805 2 .952.293.146.488.22.561.341.073.122.073.708-.171 1.396z"/>
        </svg>
    </button>

    <!-- AI Chatbot Assistant Widget Modal -->
    <div class="ai-chat-widget" id="aiChatWidget">
        <div class="ai-chat-header">
            <div class="ai-chat-header-info">
                <div class="ai-chat-avatar">
                    <img src="logo.png" alt="SeaCV Logo" />
                    <span class="online-indicator"></span>
                </div>
                <div class="ai-chat-title-wrap">
                    <h4>SeaCV Assistant <span class="bot-pill">AI BOT</span></h4>
                    <p>Konsultan CV & Karir Otomatis</p>
                </div>
            </div>
            <div class="ai-chat-header-actions">
                <button type="button" class="ai-chat-close-btn" onclick="toggleAiChat()" title="Tutup Chat" aria-label="Tutup Chat">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="ai-chat-body" id="aiChatMessages">
            <!-- Messages rendered dynamically -->
        </div>

        <div class="ai-chat-footer">
            <form class="ai-chat-input-form" id="aiChatForm" onsubmit="handleChatSubmit(event)">
                <input type="text" class="ai-chat-input" id="aiChatInput" placeholder="Tanya sesuatu tentang CV / layanan..." autocomplete="off" />
                <button type="submit" class="ai-chat-send-btn" id="aiChatSendBtn" title="Kirim Pesan" aria-label="Kirim Pesan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
            <div class="ai-chat-footer-note">SeaCV AI siap menjawab 24 jam &bull; Pengerjaan kilat 30-60 mnt</div>
        </div>
    </div>

    <!-- Mobile App-Style Bottom Navigation Bar -->
    <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Navigasi Menu Mobile">
        <a href="#katalog-layanan" class="bottom-bar-item bottom-bar-cta" data-section="katalog-layanan">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                </svg>
            </div>
            <span>Mulai Beli!</span>
        </a>
        <a href="#keunggulan" class="bottom-bar-item" data-section="keunggulan">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
            <span>Keunggulan</span>
        </a>
        <a href="#cara-pemesanan" class="bottom-bar-item" data-section="cara-pemesanan">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <span>Cara Pesan</span>
        </a>
        <a href="#testimoni" class="bottom-bar-item" data-section="testimoni">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <span>Testimoni</span>
        </a>
    </nav>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Deteksi Perangkat Android
        if (/android/i.test(navigator.userAgent)) {
            document.documentElement.classList.add('is-android');
        }

        // Hero Slider Logic
        const slides = document.querySelectorAll('.slider-slide');
        const dotsContainer = document.getElementById('sliderDots');
        let currentSlideIndex = 0;
        let slideInterval;

        if (slides.length > 0) {
            slides.forEach((_, idx) => {
                const dot = document.createElement('div');
                dot.classList.add('slider-dot');
                if (idx === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(idx));
                dotsContainer.appendChild(dot);
            });

            function goToSlide(index) {
                slides[currentSlideIndex].classList.remove('active');
                dotsContainer.children[currentSlideIndex].classList.remove('active');
                currentSlideIndex = index;
                slides[currentSlideIndex].classList.add('active');
                dotsContainer.children[currentSlideIndex].classList.add('active');
                resetInterval();
            }

            function nextSlide() {
                let nextIdx = (currentSlideIndex + 1) % slides.length;
                goToSlide(nextIdx);
            }

            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 4500);
            }

            slideInterval = setInterval(nextSlide, 4500);
        }

        // ============================================================
        // Testimonials Auto-Horizontal Slider (Desktop 3 visible, Mobile 1 visible)
        // ============================================================
        (function initTestimonialsSlider() {
            const track = document.getElementById('testimonialsTrack');
            const wrapper = document.getElementById('testimonialsSliderWrapper');
            const prevBtn = document.getElementById('testiPrevBtn');
            const nextBtn = document.getElementById('testiNextBtn');
            const dotsContainer = document.getElementById('testimonialDots');
            
            if (!track) return;
            const cards = track.querySelectorAll('.testimonial-card');
            const total = cards.length;
            if (total === 0) return;

            let currentTestiIdx = 0;
            let testiTimer = null;
            const INTERVAL_MS = 3800;

            function getVisibleCount() {
                const w = window.innerWidth;
                if (w <= 768) return 1;
                if (w <= 1024) return 2;
                return 3;
            }

            function getMaxIdx() {
                return Math.max(0, total - getVisibleCount());
            }

            function getStepWidth() {
                if (!cards.length) return 0;
                const card = cards[0];
                const trackStyle = window.getComputedStyle(track);
                const gap = parseFloat(trackStyle.gap) || 0;
                return card.offsetWidth + gap;
            }

            function renderDots() {
                if (!dotsContainer) return;
                const maxIdx = getMaxIdx();
                const totalDots = maxIdx + 1;
                dotsContainer.innerHTML = '';

                for (let i = 0; i < totalDots; i++) {
                    const dot = document.createElement('span');
                    dot.className = 'testi-dot' + (i === currentTestiIdx ? ' active' : '');
                    dot.setAttribute('aria-label', `Ulasan ${i + 1}`);
                    dot.addEventListener('click', () => goToTestimonial(i));
                    dotsContainer.appendChild(dot);
                }
            }

            function updateSlider(animate = true) {
                if (!track) return;
                const maxIdx = getMaxIdx();
                if (currentTestiIdx > maxIdx) currentTestiIdx = maxIdx;
                if (currentTestiIdx < 0) currentTestiIdx = 0;

                const step = getStepWidth();
                track.style.transition = animate ? 'transform 0.6s cubic-bezier(0.25, 1, 0.5, 1)' : 'none';
                track.style.transform = `translateX(-${currentTestiIdx * step}px)`;

                // Update dots
                if (dotsContainer) {
                    const dots = dotsContainer.querySelectorAll('.testi-dot');
                    dots.forEach((dot, idx) => {
                        if (idx === currentTestiIdx) dot.classList.add('active');
                        else dot.classList.remove('active');
                    });
                }
            }

            window.goToTestimonial = function(idx) {
                currentTestiIdx = idx;
                updateSlider(true);
                restartTimer();
            };

            function nextTestimonial() {
                const maxIdx = getMaxIdx();
                if (currentTestiIdx >= maxIdx) {
                    currentTestiIdx = 0;
                } else {
                    currentTestiIdx++;
                }
                updateSlider(true);
            }

            function prevTestimonial() {
                const maxIdx = getMaxIdx();
                if (currentTestiIdx <= 0) {
                    currentTestiIdx = maxIdx;
                } else {
                    currentTestiIdx--;
                }
                updateSlider(true);
            }

            function startTimer() {
                clearInterval(testiTimer);
                testiTimer = setInterval(nextTestimonial, INTERVAL_MS);
            }

            function restartTimer() {
                clearInterval(testiTimer);
                startTimer();
            }

            // Desktop Nav Buttons
            if (nextBtn) nextBtn.addEventListener('click', () => { nextTestimonial(); restartTimer(); });
            if (prevBtn) prevBtn.addEventListener('click', () => { prevTestimonial(); restartTimer(); });

            // Hover pause on desktop (pauses auto-sliding so user can read)
            const sliderContainer = document.querySelector('.testimonials-slider-container');
            if (sliderContainer) {
                sliderContainer.addEventListener('mouseenter', () => clearInterval(testiTimer));
                sliderContainer.addEventListener('mouseleave', () => startTimer());
            }

            // Touch Swipe for Mobile
            if (wrapper) {
                let startX = 0;
                let endX = 0;

                wrapper.addEventListener('touchstart', e => {
                    startX = e.changedTouches[0].screenX;
                    clearInterval(testiTimer);
                }, { passive: true });

                wrapper.addEventListener('touchend', e => {
                    endX = e.changedTouches[0].screenX;
                    const diff = startX - endX;
                    if (diff > 40) {
                        nextTestimonial();
                    } else if (diff < -40) {
                        prevTestimonial();
                    }
                    startTimer();
                }, { passive: true });
            }

            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    renderDots();
                    updateSlider(false);
                }, 100);
            });

            renderDots();
            updateSlider(false);
            startTimer();
        })();

        // Lightbox Preview Logic
        function openLightbox(imageSrc) {
            const modal = document.getElementById('imageLightbox');
            const img = document.getElementById('lightboxTargetImg');
            img.src = imageSrc;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const modal = document.getElementById('imageLightbox');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });

        // Cart Drawer Logic
        function toggleCartDrawer() {
            const drawer = document.getElementById('cartDrawer');
            const overlay = document.getElementById('cartOverlay');
            drawer.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function addToCart(id, name, price) {
            let cart = localStorage.getItem('seacv_cart');
            cart = cart ? JSON.parse(cart) : [];

            if (cart.find(item => item.id === id)) {
                Swal.fire({
                    title: 'Layanan Sudah Dipilih',
                    text: 'Layanan ini sudah ada di dalam keranjang belanja Anda.',
                    icon: 'info',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            cart.push({ id, name, price });
            localStorage.setItem('seacv_cart', JSON.stringify(cart));
            renderCart();

            // Trigger cart badge bump animation
            const badge = document.getElementById('cartBadgeCount');
            if (badge) {
                badge.classList.remove('bump');
                void badge.offsetWidth;
                badge.classList.add('bump');
            }

            Swal.fire({
                title: 'Berhasil Masuk Keranjang!',
                text: `${name} telah ditambahkan ke keranjang pesanan.`,
                icon: 'success',
                confirmButtonColor: '#10b981',
                timer: 1600,
                showConfirmButton: false
            });
        }

        function removeFromCart(id) {
            let cart = localStorage.getItem('seacv_cart');
            if (!cart) return;
            cart = JSON.parse(cart).filter(item => item.id !== id);
            localStorage.setItem('seacv_cart', JSON.stringify(cart));
            renderCart();
        }

        function renderCart() {
            let cart = localStorage.getItem('seacv_cart');
            cart = cart ? JSON.parse(cart) : [];

            const badgeEl = document.getElementById('cartBadgeCount');
            if (badgeEl) badgeEl.innerText = cart.length;
            const container = document.getElementById('cartItemsList');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px 10px; color: #94a3b8;">
                        <span style="font-size: 2.5rem; display: block; margin-bottom: 8px;">🛒</span>
                        <p style="font-size: 0.9rem; font-weight: 600;">Keranjang belanja Anda kosong</p>
                    </div>`;
                document.getElementById('cartTotalPrice').innerText = 'Rp 0';
                return;
            }

            let total = 0;
            cart.forEach(item => {
                total += item.price;
                container.innerHTML += `
                    <div class="cart-item-card">
                        <div class="cart-item-info">
                            <h5>${item.name}</h5>
                            <p>Rp ${item.price.toLocaleString('id-ID')}</p>
                        </div>
                        <button type="button" class="cart-item-remove-btn" onclick="removeFromCart(${item.id})">Hapus</button>
                    </div>`;
            });

            document.getElementById('cartTotalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function checkoutCartToWhatsApp() {
            let cart = localStorage.getItem('seacv_cart');
            cart = cart ? JSON.parse(cart) : [];

            if (cart.length === 0) {
                Swal.fire({
                    title: 'Keranjang Masih Kosong',
                    text: 'Silakan tambahkan minimal satu layanan sebelum memesan via WhatsApp.',
                    icon: 'warning',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            let productNames = cart.map(item => `*${item.name}*`);
            let formattedProducts = "";
            if (productNames.length === 1) {
                formattedProducts = productNames[0];
            } else if (productNames.length === 2) {
                formattedProducts = productNames.join(" & ");
            } else {
                let last = productNames.pop();
                formattedProducts = productNames.join(", ") + " & " + last;
            }

            let total = cart.reduce((acc, curr) => acc + curr.price, 0);
            let message = `Halo Admin SeaCV, saya tertarik memesan ${formattedProducts}.\n\n*Total Tagihan:* Rp ${total.toLocaleString('id-ID')}\nMohon segera diproses ya, terima kasih!`;

            localStorage.removeItem('seacv_cart');
            renderCart();
            toggleCartDrawer();

            window.open('https://wa.me/+62895396356914?text=' + encodeURIComponent(message), '_blank');
        }

        // ============================================================
        // SeaCV AI Assistant Chatbot Logic
        // ============================================================
        const aiChatWidget = document.getElementById('aiChatWidget');
        const aiChatMessages = document.getElementById('aiChatMessages');
        const aiChatInput = document.getElementById('aiChatInput');
        let chatInitialised = false;

        function toggleAiChat() {
            if (!aiChatWidget) return;

            // Close cart drawer if open to prevent screen crowding
            const cartDrawer = document.getElementById('cartDrawer');
            if (cartDrawer && cartDrawer.classList.contains('active')) {
                toggleCartDrawer();
            }

            aiChatWidget.classList.toggle('active');
            
            if (aiChatWidget.classList.contains('active')) {
                if (!chatInitialised) {
                    chatInitialised = true;
                    setTimeout(initAiChatGreeting, 350);
                } else {
                    setTimeout(() => aiChatInput && aiChatInput.focus(), 200);
                }
            }
        }

        function getCurrentTime() {
            const now = new Date();
            return now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        }

        function scrollToChatBottom() {
            if (aiChatMessages) {
                aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
            }
        }

        function appendBotMessage(text, chips = []) {
            if (!aiChatMessages) return;
            
            const msgEl = document.createElement('div');
            msgEl.className = 'chat-message bot';
            
            let chipsHtml = '';
            if (chips && chips.length > 0) {
                chipsHtml = '<div class="chat-quick-chips">' + 
                    chips.map(chip => `<button type="button" class="chat-chip" onclick="handleChipClick('${chip.replace(/'/g, "\\'")}')">${chip}</button>`).join('') + 
                    '</div>';
            }

            msgEl.innerHTML = `
                <div style="display: flex; gap: 8px; align-items: flex-start;">
                    <img src="logo.png" alt="SeaCV" style="width: 22px; height: 22px; border-radius: 6px; background: #ffffff; padding: 2px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; flex-shrink: 0; margin-top: 2px;" />
                    <div style="flex: 1; min-width: 0;">
                        <div class="chat-bubble">${text}</div>
                        ${chipsHtml}
                        <span class="chat-time">${getCurrentTime()}</span>
                    </div>
                </div>
            `;
            
            aiChatMessages.appendChild(msgEl);
            scrollToChatBottom();
        }

        function appendUserMessage(text) {
            if (!aiChatMessages) return;
            const msgEl = document.createElement('div');
            msgEl.className = 'chat-message user';
            msgEl.innerHTML = `
                <div class="chat-bubble">${escapeHtml(text)}</div>
                <span class="chat-time">${getCurrentTime()}</span>
            `;
            aiChatMessages.appendChild(msgEl);
            scrollToChatBottom();
        }

        function showTypingIndicator() {
            if (!aiChatMessages) return;
            const typingEl = document.createElement('div');
            typingEl.className = 'chat-message bot';
            typingEl.id = 'chatTypingIndicator';
            typingEl.innerHTML = `
                <div style="display: flex; gap: 8px; align-items: center;">
                    <img src="logo.png" alt="SeaCV" style="width: 22px; height: 22px; border-radius: 6px; background: #ffffff; padding: 2px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; flex-shrink: 0;" />
                    <div class="typing-indicator">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                </div>
            `;
            aiChatMessages.appendChild(typingEl);
            scrollToChatBottom();
        }

        function hideTypingIndicator() {
            const typingEl = document.getElementById('chatTypingIndicator');
            if (typingEl) typingEl.remove();
        }

        function escapeHtml(string) {
            const entityMap = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            };
            return String(string).replace(/[&<>"']/g, function (s) {
                return entityMap[s];
            });
        }

        function initAiChatGreeting() {
            showTypingIndicator();
            setTimeout(() => {
                hideTypingIndicator();
                const welcomeText = `Halo! 👋 Saya <b>SeaCV AI Assistant</b>.<br>Ada yang bisa kami bantu seputar pembuatan CV profesional, optimasi ATS, atau surat lamaran kerja?`;
                const defaultChips = [
                    '⚡ Berapa lama pengerjaan CV?',
                    '🕒 Jam operasional layanan?',
                    '🔒 Aman & Dilindungi UU PDP?',
                    '📄 Beda CV ATS vs CV Kreatif?',
                    '💰 Berapa harga promo saat ini?',
                    '🔄 Apakah ada garansi revisi?',
                    '🛒 Bagaimana alur pemesanannya?',
                    '💬 Hubungi WhatsApp Admin Asli'
                ];
                appendBotMessage(welcomeText, defaultChips);
                if (aiChatInput) aiChatInput.focus();
            }, 550);
        }

        function handleChipClick(chipText) {
            if (chipText.includes('WhatsApp Admin')) {
                appendUserMessage(chipText);
                showTypingIndicator();
                setTimeout(() => {
                    hideTypingIndicator();
                    appendBotMessage(
                        `Siap! Kamu bisa langsung konsultasi privat atau kirim materi CV ke admin WhatsApp kami:<br><a href="https://wa.me/+62895396356914?text=Halo%20Admin%20SeaCV,%20saya%20ingin%20konsultasi%20pembuatan%20dokumen%20karir" target="_blank" class="chat-connect-wa-btn"><span>Hubungi WhatsApp: +62 895-3963-56914</span></a>`,
                        ['⚡ Berapa lama pengerjaan CV?', '📄 Beda CV ATS vs CV Kreatif?']
                    );
                }, 400);
                return;
            }

            appendUserMessage(chipText);
            showTypingIndicator();
            setTimeout(() => {
                hideTypingIndicator();
                processAiResponse(chipText);
            }, 500);
        }

        function handleChatSubmit(e) {
            e.preventDefault();
            if (!aiChatInput) return;
            const query = aiChatInput.value.trim();
            if (!query) return;

            appendUserMessage(query);
            aiChatInput.value = '';

            showTypingIndicator();
            setTimeout(() => {
                hideTypingIndicator();
                processAiResponse(query);
            }, 600);
        }

        function processAiResponse(query) {
            const q = query.toLowerCase();
            let reply = '';
            let followUpChips = [];

            if (q.includes('jam buka') || q.includes('jam operasional') || q.includes('jam berapa') || q.includes('buka jam') || q.includes('tutup jam') || q.includes('operasional') || q.includes('jadwal') || q.includes('buka') || q.includes('tutup') || q.includes('libur') || q.includes('hari apa') || q.includes('jumat') || q.includes('jumatan')) {
                const now = new Date();
                const isFriday = now.getDay() === 5;
                const curHour = now.getHours() + (now.getMinutes() / 60);
                let liveFridayStatus = '';
                if (isFriday && curHour >= 11.5 && curHour < 13.0) {
                    liveFridayStatus = `<br>🕌 <b>Status Saat Ini:</b> Admin sedang jeda ibadah Sholat Jumat (11.30 - 13.00 WIB). Pesanan & konsultasi Anda akan langsung diproses mulai pukul 13.00 WIB ya!<br>`;
                }

                reply = `🕒 <b>Jam Operasional Layanan SeaCV:</b><br><br>
                Tim desainer & admin kami aktif melayani pembuatan dan revisi CV:<br>
                &bull; <b>Senin - Minggu:</b> 08.00 - 22.00 WIB<br>
                &bull; <b>Khusus Hari Jumat:</b> Jeda istirahat Sholat Jumat pukul <b>11.30 - 13.00 WIB</b> (layanan aktif normal kembali pukul 13.00 WIB)<br>
                &bull; <b>Chatbot AI SeaCV:</b> Siaga 24 Jam Nonstop<br>${liveFridayStatus}<br>
                Pesanan yang masuk saat jam istirahat atau di atas pukul 22.00 WIB tetap ditampung aman dan langsung diproses prioritas pertama saat jam operasional aktif!`;
                followUpChips = ['🔒 Aman & Dilindungi UU PDP?', '⚡ Berapa lama pengerjaan CV?', '💬 Hubungi WhatsApp Admin Asli'];
            }
            else if (q.includes('aman') || q.includes('privasi') || q.includes('rahasia') || q.includes('terpercaya') || q.includes('bocor') || q.includes('amanah') || q.includes('nipu') || q.includes('penipuan') || q.includes('asli') || q.includes('keamanan') || q.includes('data diri') || q.includes('hukum') || q.includes('undang') || q.includes('uu') || q.includes('pdp') || q.includes('legal') || q.includes('aturan')) {
                reply = `🔒 <b>100% Aman, Terpercaya, & Dilindungi Undang-Undang!</b><br><br>
                SeaCV berkomitmen penuh menjaga kerahasiaan data pribadi Anda berlandaskan payung hukum Republik Indonesia:<br><br>
                ⚖️ <b>UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP):</b><br>
                Seluruh data profil, nomor HP, alamat, riwayat karir, dan foto Anda <b>hanya digunakan secara spesifik untuk menyusun dokumen CV</b> yang Anda pesan. Data Anda dijamin 100% tidak akan pernah disebarluaskan, dibocorkan, atau diperjualbelikan.<br><br>
                📜 <b>Kepatuhan UU ITE (Informasi & Transaksi Elektronik):</b><br>
                Seluruh pengiriman berkas elektronik dan transaksi dilakukan secara aman dan terenkripsi langsung melalui kontak WhatsApp resmi SeaCV.<br><br>
                🛡️ <b>Garansi Kerahasiaan Penuh:</b><br>
                Telah dipercaya oleh ribuan fresh graduate & pencari kerja dengan rating kepuasan 4.9/5 dan garansi revisi sampai tuntas!`;
                followUpChips = ['🕒 Jam operasional layanan?', '💰 Berapa harga promo saat ini?', '💬 Hubungi WhatsApp Admin Asli'];
            }
            else if (q.includes('lama') || q.includes('waktu') || q.includes('durasi') || q.includes('kapan') || q.includes('menit') || q.includes('kilat') || q.includes('cepat') || q.includes('berapa lama')) {
                reply = `⚡ <b>Pengerjaan Kilat 30 - 60 Menit Saja!</b><br><br>Setelah kamu mengirimkan data diri dan memilih template, tim desainer SeaCV akan langsung memproses dokumenmu secara prioritas tanpa antre berhari-hari. File PDF siap melamar langsung dikirim ke WhatsApp kamu!`;
                followUpChips = ['💰 Berapa harga promo saat ini?', '📄 Beda CV ATS vs CV Kreatif?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('ats') || q.includes('kreatif') || q.includes('beda') || q.includes('perbedaan') || q.includes('maksud') || q.includes('pilih mana')) {
                reply = `📄 <b>Perbedaan CV ATS vs CV Kreatif:</b><br><br>
                <b>1. CV ATS-Friendly:</b><br>
                Format teks clean & terstruktur khusus agar lolos pemindaian sistem otomatis HRD (Applicant Tracking System) di perusahaan BUMN, Multinasional, atau Korporat besar.<br><br>
                <b>2. CV Kreatif Modern:</b><br>
                Tata letak visual estetik, elegan, dan menonjol. Sangat efektif untuk melamar di Agensi, Startup, Media, Desain, IT, dan Industri Kreatif.`;
                followUpChips = ['⚡ Berapa lama pengerjaan CV?', '💰 Berapa harga promo saat ini?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('harga') || q.includes('biaya') || q.includes('promo') || q.includes('tarif') || q.includes('bayar') || q.includes('diskon') || q.includes('murah') || q.includes('ongkos') || q.includes('berapa')) {
                reply = `💰 <b>Promo Spesial SeaCV Hari Ini:</b><br><br>
                Semua template CV premium didiskon dari <s>Rp 15.000</s> menjadi hanya <b>Rp 10.000</b> saja!<br><br>
                <b>Paket sudah termasuk:</b><br>
                &bull; File PDF Resolusi Tinggi (Siap Apply)<br>
                &bull; Format PNG Berkualitas Jernih (Bisa Request)<br>
                &bull; Free konsultasi & garansi revisi rapi`;
                followUpChips = ['🔄 Apakah ada garansi revisi?', '🛒 Bagaimana alur pemesanannya?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('revisi') || q.includes('garansi') || q.includes('salah ketik') || q.includes('edit lagi') || q.includes('ubah')) {
                reply = `🔄 <b>Garansi Revisi Sepuasnya!</b><br><br>
                Kami mengutamakan kepuasan klien 100%. Jika terdapat kesalahan ketik, pembaruan kontak, atau perubahan pengalaman kerja yang ingin disesuaikan, tim kami siap merevisinya hingga sesuai dan sempurna.`;
                followUpChips = ['⚡ Berapa lama pengerjaan CV?', '🛒 Bagaimana alur pemesanannya?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('pesan') || q.includes('cara') || q.includes('order') || q.includes('beli') || q.includes('langkah') || q.includes('proses') || q.includes('alur')) {
                reply = `🛒 <b>4 Langkah Mudah Pemesanan:</b><br><br>
                1. <b>Pilih Template:</b> Telusuri katalog desain di halaman ini.<br>
                2. <b>Pesan / Masukkan Keranjang:</b> Klik tombol 'Pesan via WA' atau kumpulkan di keranjang.<br>
                3. <b>Kirim Data Diri:</b> Kirim draf profil / CV lama kamu ke admin WhatsApp.<br>
                4. <b>Selesai Kilat:</b> CV final siap kamu gunakan dalam 30-60 menit!`;
                followUpChips = ['💰 Berapa harga promo saat ini?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('surat lamaran') || q.includes('cover letter') || q.includes('lamaran')) {
                reply = `✉️ <b>Surat Lamaran Kerja Formal:</b><br><br>
                Kami juga menyediakan kurasi surat lamaran kerja profesional (Bahasa Indonesia / Inggris) dengan formula copywriting persuasif yang menonjolkan kualifikasi kamu di mata recruiter.`;
                followUpChips = ['💰 Berapa harga promo saat ini?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('resign') || q.includes('pengunduran diri') || q.includes('surat resign')) {
                reply = `📜 <b>Layanan Surat Pengunduran Diri:</b><br><br>
                Saat ini SeaCV memfokuskan layanan pada pembuatan <b>CV ATS-Friendly, CV Kreatif Modern,</b> dan <b>Surat Lamaran Kerja Profesional</b>.<br><br>
                Layanan surat pengunduran diri sedang dinonaktifkan sementara. Namun jika kamu butuh konsultasi atau request khusus, silakan langsung chat admin WhatsApp kami ya!`;
                followUpChips = ['📄 Beda CV ATS vs CV Kreatif?', '⚡ Berapa lama pengerjaan CV?', '💬 Hubungi WhatsApp Admin Asli'];
            } 
            else if (q.includes('admin') || q.includes('wa') || q.includes('whatsapp') || q.includes('manusia') || q.includes('cs') || q.includes('kontak') || q.includes('nomor') || q.includes('telepon') || q.includes('hubungi')) {
                reply = `💬 <b>Hubungi Admin WhatsApp SeaCV:</b><br><br>
                Admin kami siap melayani konsultasi dan pemesanan secara personal di WhatsApp resmi:<br>
                <a href="https://wa.me/+62895396356914?text=Halo%20Admin%20SeaCV,%20saya%20ingin%20konsultasi%20pembuatan%20dokumen%20karir" target="_blank" class="chat-connect-wa-btn"><span>Chat WhatsApp Sekarang (+62 895-3963-56914)</span></a>`;
                followUpChips = ['⚡ Berapa lama pengerjaan CV?', '💰 Berapa harga promo saat ini?'];
            } 
            else if (q.includes('halo') || q.includes('hai') || q.includes('hello') || q.includes('assalamualaikum') || q.includes('pagi') || q.includes('siang') || q.includes('sore') || q.includes('malam') || q.includes('permisi')) {
                reply = `Halo kak! Selamat datang di <b>SeaCV</b> 😊 Ada yang bisa kami bantu seputar pembuatan CV ATS, CV Kreatif, atau Surat Lamaran kerja hari ini?`;
                followUpChips = ['⚡ Berapa lama pengerjaan CV?', '📄 Beda CV ATS vs CV Kreatif?', '💰 Berapa harga promo saat ini?'];
            } 
            else if (q.includes('makasih') || q.includes('terima kasih') || q.includes('thanks') || q.includes('oke') || q.includes('ok') || q.includes('siap') || q.includes('mantap')) {
                reply = `Sama-sama kak! Sukses selalu dalam melamar pekerjaan dan mewujudkan karir impiannya! 🚀 Jangan ragu hubungi kami jika butuh bantuan lebih lanjut ya.`;
                followUpChips = ['💬 Hubungi WhatsApp Admin Asli'];
            } 
            else {
                reply = `Terima kasih pertanyaannya kak! Untuk konsultasi detail atau request custom, kamu bisa langsung terhubung dengan tim admin resmi SeaCV via WhatsApp ya:<br><br>
                <a href="https://wa.me/+62895396356914?text=Halo%20Admin%20SeaCV,%20saya%20ingin%20bertanya%20tentang:%20${encodeURIComponent(query)}" target="_blank" class="chat-connect-wa-btn"><span>Chat Admin WhatsApp Langsung</span></a>`;
                followUpChips = [
                    '⚡ Berapa lama pengerjaan CV?',
                    '📄 Beda CV ATS vs CV Kreatif?',
                    '💰 Berapa harga promo saat ini?',
                    '🔄 Apakah ada garansi revisi?'
                ];
            }

            appendBotMessage(reply, followUpChips);
        }

        // Reading Scroll Progress Bar & Back to Top Logic
        const progressBar = document.getElementById('scrollProgressBar');
        const backToTopBtn = document.getElementById('backToTopBtn');

        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

            if (progressBar) {
                progressBar.style.width = scrollPercent + '%';
            }

            if (backToTopBtn) {
                if (scrollTop > 380) {
                    backToTopBtn.classList.add('visible');
                } else {
                    backToTopBtn.classList.remove('visible');
                }
            }
        }, { passive: true });

        if (backToTopBtn) {
            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // ============================================================
        // Dynamic "Tampilkan Lebih Banyak" (Load More) Logic
        // ============================================================
        const INITIAL_VISIBLE_COUNT = 8;
        const BATCH_LOAD_SIZE = 8;
        let currentVisibleCount = INITIAL_VISIBLE_COUNT;
        let isExpandedAll = false;

        function handleLoadMoreToggle() {
            const grid = document.getElementById('mainProductGrid');
            if (!grid) return;
            const allCards = Array.from(grid.querySelectorAll('.product-card'));
            const totalCards = allCards.length;

            const btnText = document.getElementById('btnLoadMoreText');
            const btnBadge = document.getElementById('btnLoadMoreBadge');
            const btnIcon = document.getElementById('btnLoadMoreIcon');

            if (!isExpandedAll) {
                // Reveal next batch
                const nextTargetCount = currentVisibleCount + BATCH_LOAD_SIZE;
                let newlyRevealed = 0;

                allCards.forEach((card, idx) => {
                    if (idx < nextTargetCount && card.classList.contains('product-hidden')) {
                        card.classList.remove('product-hidden');
                        card.classList.add('revealed', 'product-revealing');
                        card.style.transitionDelay = `${(newlyRevealed % 4) * 0.08}s`;
                        newlyRevealed++;
                    }
                });

                currentVisibleCount = Math.min(nextTargetCount, totalCards);
                const remaining = totalCards - currentVisibleCount;

                if (remaining > 0) {
                    if (btnText) btnText.textContent = 'Tampilkan Lebih Banyak Desain';
                    if (btnBadge) {
                        btnBadge.style.display = 'inline-block';
                        btnBadge.textContent = '+' + remaining + ' Template';
                    }
                } else {
                    // All cards are now visible! Change button to "Tampilkan Lebih Sedikit"
                    isExpandedAll = true;
                    if (btnText) btnText.textContent = 'Tampilkan Lebih Sedikit';
                    if (btnBadge) btnBadge.style.display = 'none';
                    if (btnIcon) {
                        btnIcon.innerHTML = '<polyline points="18 15 12 9 6 15"></polyline>';
                    }
                }
            } else {
                // Collapse back to initial count
                allCards.forEach((card, idx) => {
                    if (idx >= INITIAL_VISIBLE_COUNT) {
                        card.classList.add('product-hidden');
                        card.classList.remove('product-revealing');
                    }
                });

                currentVisibleCount = INITIAL_VISIBLE_COUNT;
                isExpandedAll = false;

                if (btnText) btnText.textContent = 'Tampilkan Lebih Banyak Desain';
                if (btnBadge) {
                    btnBadge.style.display = 'inline-block';
                    btnBadge.textContent = '+' + (totalCards - INITIAL_VISIBLE_COUNT) + ' Template';
                }
                if (btnIcon) {
                    btnIcon.innerHTML = '<polyline points="6 9 12 15 18 9"></polyline>';
                }

                // Smooth scroll back to catalog header
                const catalogHeader = document.getElementById('katalog-layanan');
                if (catalogHeader) {
                    catalogHeader.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }

        // ============================================================
        // Metrics Accordion Dropdown Logic ("Keunggulan")
        // ============================================================
        function toggleMetricAccordion(btn) {
            if (window.innerWidth > 768) return; // Khusus mobile saja, tidak aktif di laptop/desktop
            const item = btn.closest('.accordion-item');
            if (!item) return;
            const container = item.parentElement;
            const wasActive = item.classList.contains('active');

            if (container) {
                container.querySelectorAll('.accordion-item').forEach(el => {
                    el.classList.remove('active');
                });
            }

            if (!wasActive) {
                item.classList.add('active');
            }
        }

        // IntersectionObserver for Fluid Scroll Reveal Animations
        const revealElements = document.querySelectorAll(
            '.accordion-item, .testimonial-card, .catalog-header-panel, .filter-bar-wrapper, .product-card, .workflow-section .section-header, .step-card'
        );

        if ('IntersectionObserver' in window && revealElements.length > 0) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -30px 0px'
            });

            revealElements.forEach((el, idx) => {
                if (el.classList.contains('product-hidden')) {
                    // Do not observe hidden cards yet
                    return;
                }
                el.classList.add('reveal-init');
                let delay = 0;
                if (el.classList.contains('accordion-item')) {
                    delay = (idx % 4) * 0.08;
                } else if (el.classList.contains('testimonial-card')) {
                    delay = (idx % 3) * 0.12;
                } else if (el.classList.contains('product-card')) {
                    delay = (idx % 4) * 0.08;
                } else if (el.classList.contains('step-card')) {
                    delay = (idx % 3) * 0.14;
                }
                if (delay > 0) {
                    el.style.transitionDelay = `${delay}s`;
                }
                revealObserver.observe(el);
            });
        }

        // Initialize cart & remember scroll position
        window.addEventListener('load', () => {
            renderCart();
            const savedScroll = localStorage.getItem('seacv_scroll_y');
            if (savedScroll !== null) {
                window.scrollTo(0, parseInt(savedScroll));
                localStorage.removeItem('seacv_scroll_y');
            }
        });

        window.addEventListener('beforeunload', () => {
            localStorage.setItem('seacv_scroll_y', window.scrollY);
        });

        // ============================================================
        // Mobile Bottom Bar Active Spy & Smooth Navigation
        // ============================================================
        (function initMobileBottomBar() {
            const bottomItems = document.querySelectorAll('.bottom-bar-item');
            if (!bottomItems.length) return;

            const sectionIds = ['katalog-layanan', 'keunggulan', 'cara-pemesanan', 'testimoni'];

            function updateActiveTab() {
                const scrollPos = window.scrollY + 220;
                let activeId = '';

                for (let i = 0; i < sectionIds.length; i++) {
                    const el = document.getElementById(sectionIds[i]);
                    if (el) {
                        const top = el.offsetTop;
                        const height = el.offsetHeight;
                        if (scrollPos >= top && scrollPos < top + height + 120) {
                            activeId = sectionIds[i];
                        }
                    }
                }

                bottomItems.forEach(item => {
                    if (item.getAttribute('data-section') === activeId) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            window.addEventListener('scroll', updateActiveTab, { passive: true });
            updateActiveTab();
        })();
    </script>
</body>
</html>