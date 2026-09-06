<?php
/**
 * navbar.php - SeaCV Global Top Navigation Bar Component
 * Digunakan secara terpadu di Beranda (Storefront), Berita & Artikel, Detail Artikel, dll.
 */

// Deteksi konteks halaman aktif untuk URL link dan state aktif
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = basename($_SERVER['SCRIPT_NAME'] ?? '');
$isArticlePage = (strpos($requestUri, 'artikel') !== false || strpos($requestUri, 'berita') !== false || $scriptName === 'artikel.php');
$isHomepage = !$isArticlePage;
$homePrefix = $isHomepage ? '' : 'index.php';
?>

<!-- SeaCV Global Navbar Styles -->
<style id="seacv-navbar-styles">
.navbar {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 0 6%;
    height: 80px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    transform: translateZ(0);
    will-change: transform;
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
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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
    font-family: 'Outfit', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 1.45rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    line-height: 1;
    margin: 0;
    padding: 0;
    color: #2563eb;
}

.brand-text span {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
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
    color: #64748b;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none;
    position: relative;
    padding: 6px 2px;
}

.nav-link-item:hover,
.nav-link-item.active {
    color: #2563eb;
}

.nav-link-item.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2.5px;
    background: #2563eb;
    border-radius: 2px;
}

.nav-cta-btn.active::after {
    display: none !important;
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

.nav-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cart-pill-btn {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 9px 18px;
    border-radius: 9999px;
    color: #0f172a;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    animation: gentleFloat 3s ease-in-out infinite;
}

.cart-pill-btn:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #2563eb;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.16);
    animation-play-state: paused;
}

.cart-badge {
    background: #2563eb;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 800;
    min-width: 22px;
    height: 22px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
    transition: transform 0.2s ease, background 0.2s ease;
}

@keyframes gentleFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
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
</style>

<!-- Top Navigation Bar (The Original SeaCV Navbar) -->
<nav class="navbar" id="mainNavbar">
    <a href="index.php" class="brand-logo" aria-label="Beranda SeaCV">
        <img src="logo.png" alt="SeaCV Logo" class="brand-logo-img" />
        <div class="brand-text">
            <h1>SEACV</h1>
            <span>Professional Hub</span>
        </div>
    </a>

    <div class="nav-links">
        <a href="<?= $homePrefix ?>#katalog-layanan" class="nav-link-item nav-cta-btn" data-section="katalog-layanan">Mulai Beli!</a>
        <a href="<?= $homePrefix ?>#keunggulan" class="nav-link-item" data-section="keunggulan">Keunggulan</a>
        <a href="<?= $homePrefix ?>#cara-pemesanan" class="nav-link-item" data-section="cara-pemesanan">Cara Pemesanan</a>
        <a href="<?= $homePrefix ?>#testimoni" class="nav-link-item" data-section="testimoni">Testimoni</a>
        <a href="artikel.php" class="nav-link-item <?= $isArticlePage ? 'active' : '' ?>">Berita &amp; Artikel</a>
    </div>

    <div class="nav-actions">
        <button type="button" class="cart-pill-btn" onclick="typeof toggleCartDrawer === 'function' ? toggleCartDrawer() : (window.location.href='index.php?open_cart=1#katalog-layanan')">
            <span class="cart-label-text">Pesanan Saya</span>
            <span class="cart-badge" id="cartBadgeCount">0</span>
        </button>
    </div>
</nav>

<script>
(function() {
    function syncGlobalNavbarCartBadge() {
        try {
            const raw = localStorage.getItem('seacv_cart');
            if (raw) {
                const parsed = JSON.parse(raw);
                const badge = document.getElementById('cartBadgeCount');
                if (badge && Array.isArray(parsed)) {
                    badge.textContent = parsed.length;
                }
            }
        } catch (e) {}
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncGlobalNavbarCartBadge);
    } else {
        syncGlobalNavbarCartBadge();
    }
})();
</script>
