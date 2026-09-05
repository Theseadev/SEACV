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
