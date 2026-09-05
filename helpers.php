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
