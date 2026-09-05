<?php
// helpers.php - Shared helpers for SeaCV

if (!function_exists('resolveProductDisplayName')) {
    /**
     * Resolves the clean display name for templates:
     * e.g. CV Kreatif 01, CV Kreatif 25, CV ATS 01, Surat Lamaran 06, etc.
     */
    function resolveProductDisplayName($product, $index = 0) {
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
        
        $rawName = trim($product['name'] ?? '');
        if (preg_match('/(CV\s*Kreatif|CV\s*ATS|Surat\s*Lamaran|Surat\s*Pengunduran)\s*(\d+)/i', $rawName, $matches)) {
            $prefix = ucwords(strtolower(trim($matches[1])));
            $num = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            return $prefix . ' ' . $num;
        }

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
}

if (!function_exists('getCategoriesWithCounts')) {
    /**
     * Fetch all categories with product counts
     */
    function getCategoriesWithCounts($pdo) {
        try {
            $stmt = $pdo->query("
                SELECT c.id, c.name, COUNT(p.id) AS count 
                FROM categories c
                LEFT JOIN products p ON LOWER(p.category) = LOWER(c.name)
                GROUP BY c.id, c.name
                ORDER BY c.id ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('getCategoryIconSvg')) {
    /**
     * Get SVG icon based on category name
     */
    function getCategoryIconSvg($name) {
        $n = strtolower($name);
        if (strpos($n, 'ats') !== false) {
            return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
        } elseif (strpos($n, 'kreatif') !== false) {
            return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m4.93 4.93 4.24 4.24"></path><path d="m14.83 9.17 4.24-4.24"></path><path d="m14.83 14.83 4.24 4.24"></path><path d="m9.17 14.83-4.24 4.24"></path></svg>';
        } elseif (strpos($n, 'lamaran') !== false) {
            return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
        } else {
            return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>';
        }
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL relative to application base path
     */
    function url($path = '') {
        $base = '';
        if (class_exists('Flight', false) && \Flight::request()) {
            $base = \Flight::request()->base ?? '';
        } elseif (isset($_SERVER['SCRIPT_NAME'])) {
            $base = dirname($_SERVER['SCRIPT_NAME']);
        }
        $base = trim(str_replace('\\', '/', $base));
        if ($base === '/' || $base === '.') {
            $base = '';
        }
        $path = '/' . ltrim($path, '/');
        return ($base !== '' ? $base : '') . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate URL for static assets (with auto-resolution to assets subdirectories)
     */
    function asset($path = '') {
        $clean = ltrim(trim($path), '/');
        if (!str_starts_with($clean, 'assets/') && !str_starts_with($clean, 'uploads/')) {
            if (preg_match('/\.(css)$/i', $clean) && file_exists(__DIR__ . '/assets/css/' . $clean)) {
                $clean = 'assets/css/' . $clean;
            } elseif (preg_match('/\.(js)$/i', $clean) && file_exists(__DIR__ . '/assets/js/' . $clean)) {
                $clean = 'assets/js/' . $clean;
            } elseif (preg_match('/\.(png|jpg|jpeg|svg|gif|webp)$/i', $clean) && file_exists(__DIR__ . '/assets/images/' . $clean)) {
                $clean = 'assets/images/' . $clean;
            } elseif (str_starts_with($clean, 'banner/') && file_exists(__DIR__ . '/assets/' . $clean)) {
                $clean = 'assets/' . $clean;
            }
        }
        return url($clean);
    }
}

if (!function_exists('resolveProductBadge')) {
    function resolveProductBadge($productId, $newProductIds = null) {
        if ($newProductIds === null) {
            global $newProductIds;
        }

        if (!empty($newProductIds) && in_array($productId, $newProductIds)) {
            return [
                'type' => 'new',
                'class' => 'badge-new',
                'text' => 'NEW',
                'icon' => '<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l2.6 7.4L22 10l-7.4 2.6L12 20l-2.6-7.4L2 10l7.4-2.6L12 0z"/></svg>'
            ];
        }

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
                return null;
        }
    }
}

if (!function_exists('resolveProductFeaturesContext')) {
    function resolveProductFeaturesContext($category_string) {
        $cat = strtolower($category_string);
        if (strpos($cat, 'kreatif') !== false) {
            return ['Kilat 30-60 Mnt', 'Format PDF / PNG', 'Bebas Revisi'];
        } elseif (strpos($cat, 'ats') !== false) {
            return ['Kilat 30-60 Mnt', 'Format ATS Clean', 'Format PDF / PNG'];
        } elseif (strpos($cat, 'lamaran') !== false) {
            return ['Kilat 30-60 Mnt', 'Standar PUEBI Formal', 'Format PDF / PNG'];
        } else {
            return ['Kilat 30-60 Mnt', 'Standar Formal Rapi', 'Format PDF / PNG'];
        }
    }
}

if (!function_exists('resolveProductBadgeData')) {
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
}

if (!function_exists('resolveProductBadgeContext')) {
    function resolveProductBadgeContext($id_int, $category_string) {
        $data = resolveProductBadgeData($id_int, $category_string);
        return $data['text'];
    }
}

if (!function_exists('resolveCategoryTagData')) {
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
}

if (!function_exists('resolveCategoryIcon')) {
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
}


