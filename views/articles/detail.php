<?php
// views/articles/detail.php - Halaman Baca Artikel Karir SeaCV

$pageTitle = $article['title'] . ' | SeaCV';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($article['summary']) ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --secondary: #0ea5e9;
            --accent-cyan: #38bdf8;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 18px;
            --radius-pill: 9999px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
            --shadow-lg: 0 20px 35px -8px rgba(15, 23, 42, 0.12);
            --transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            --font-heading: 'Plus Jakarta Sans', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.7;
            overflow-x: hidden;
            padding-bottom: 70px;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Detail Container */
        .detail-wrapper {
            max-width: 860px;
            margin: 32px auto 60px;
            padding: 0 20px;
        }

        /* Breadcrumbs */
        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.86rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .breadcrumbs a {
            color: var(--primary);
            font-weight: 600;
        }

        .breadcrumbs a:hover {
            text-decoration: underline;
        }

        /* Article Header Card */
        .article-main-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 40px;
        }

        .detail-header {
            margin-bottom: 28px;
        }

        .detail-category-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid #bfdbfe;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: var(--radius-pill);
            letter-spacing: 0.04em;
            margin-bottom: 14px;
        }

        .detail-title {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.3;
            color: var(--text-main);
            margin-bottom: 18px;
            letter-spacing: -0.4px;
        }

        .detail-meta-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.85rem;
            color: var(--text-muted);
            padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .detail-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Featured Image */
        .detail-featured-wrap {
            width: 100%;
            aspect-ratio: 16 / 9;
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-bottom: 34px;
            background: #0f172a;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
        }

        .detail-featured-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Article Typography */
        .article-content {
            font-size: 1.05rem;
            color: #334155;
            line-height: 1.85;
        }

        .article-content .lead-paragraph {
            font-size: 1.15rem;
            font-weight: 500;
            color: #1e293b;
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .article-content h3 {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 34px 0 14px;
            padding-left: 14px;
            border-left: 4px solid var(--primary);
            line-height: 1.3;
        }

        .article-content p {
            margin-bottom: 18px;
        }

        .article-content ul,
        .article-content ol {
            margin: 14px 0 24px 24px;
        }

        .article-content li {
            margin-bottom: 10px;
        }

        .article-callout {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            padding: 18px 22px;
            margin: 26px 0;
            font-size: 0.98rem;
            color: #0369a1;
        }

        .article-callout strong {
            color: #0284c7;
        }

        /* Share Bar */
        .share-bar-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .share-label {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .share-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-share {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            font-size: 0.84rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-main);
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-share:hover {
            transform: translateY(-1px);
        }

        .btn-share.wa:hover {
            background: #25D366;
            color: #ffffff;
            border-color: #25D366;
        }

        .btn-share.copy:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* Related Articles Section */
        .related-section {
            margin-top: 50px;
        }

        .related-header {
            margin-bottom: 22px;
        }

        .related-header h3 {
            font-family: var(--font-heading);
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .related-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .related-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .related-thumb {
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #0f172a;
            object-fit: cover;
        }

        .related-card-body {
            padding: 14px 16px 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .related-cat {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .related-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Banner CTA */
        .article-cta-box {
            position: relative;
            background: linear-gradient(135deg, #070d1e 0%, #0d1b3e 50%, #0f2757 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: var(--radius-lg);
            padding: 34px 38px;
            margin-bottom: 40px;
            box-shadow: 0 16px 36px -4px rgba(7, 13, 30, 0.4);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            color: #ffffff;
        }

        .article-cta-box::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('batik-megamendung.jpg');
            background-size: cover;
            background-position: center 25%;
            opacity: 0.32;
            pointer-events: none;
        }

        .article-cta-content {
            position: relative;
            z-index: 1;
        }

        .article-cta-content h3 {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .article-cta-content p {
            font-size: 0.92rem;
            color: #cbd5e1;
        }

        .article-cta-btn {
            position: relative;
            z-index: 1;
            white-space: nowrap;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: var(--radius-pill);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .article-cta-btn:hover {
            transform: translateY(-2px);
        }

        /* Footer */
        .site-footer {
            border-top: 1px solid var(--border-color);
            background: #ffffff;
            padding: 32px 6% 40px;
            text-align: center;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .site-footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 12px;
            flex-wrap: wrap;
            font-weight: 600;
        }

        .site-footer-links a:hover {
            color: var(--primary);
        }

        /* Mobile Bottom Bar (5 Items) */
        .mobile-bottom-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1200;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.07);
            padding: 6px 4px calc(6px + env(safe-area-inset-bottom));
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            body { padding-bottom: calc(65px + env(safe-area-inset-bottom, 0px)); }

            .detail-wrapper {
                margin: 16px auto 36px;
                padding: 0 14px;
            }
            .article-main-card {
                padding: 18px 14px;
                border-radius: 14px;
            }
            .breadcrumbs {
                font-size: 0.78rem;
                margin-bottom: 12px;
                flex-wrap: wrap;
                gap: 4px;
            }
            .detail-header {
                margin-bottom: 16px;
            }
            .detail-badge {
                font-size: 0.68rem;
                padding: 3px 10px;
                margin-bottom: 8px;
            }
            .detail-title {
                font-size: 1.3rem;
                line-height: 1.3;
                margin-bottom: 10px;
            }
            .detail-meta {
                flex-wrap: wrap;
                gap: 8px;
                font-size: 0.76rem;
            }
            .detail-featured-img {
                border-radius: 10px;
                margin-bottom: 18px;
            }
            .article-content {
                font-size: 0.94rem;
                line-height: 1.7;
            }
            .article-content h2 {
                font-size: 1.18rem;
                margin: 22px 0 8px;
            }
            .article-content h3 {
                font-size: 1.05rem;
                margin: 18px 0 6px;
            }
            .article-content p {
                margin-bottom: 14px;
            }
            .callout-box {
                padding: 14px;
                border-radius: 10px;
                margin: 18px 0;
            }
            .checklist-box {
                padding: 14px;
                border-radius: 10px;
                margin: 18px 0;
            }
            .checklist-item {
                font-size: 0.88rem;
                gap: 8px;
            }
            .share-bar-wrap {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 16px 0 0;
            }
            .share-buttons {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
            .btn-share {
                justify-content: center;
                width: 100%;
                padding: 8px 12px;
                font-size: 0.8rem;
            }
            .related-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            .article-cta-box {
                flex-direction: column;
                text-align: center;
                padding: 28px 20px 24px;
                border-radius: 18px;
                margin-bottom: 85px;
                box-shadow: 0 12px 30px -4px rgba(7, 13, 30, 0.45);
            }
            .article-cta-box::before {
                background-position: center 15%;
            }
            .article-cta-content h3 {
                font-size: 1.2rem;
                margin-bottom: 6px;
            }
            .article-cta-content p {
                font-size: 0.82rem;
                margin-bottom: 16px;
            }
            .article-cta-btn {
                width: 100%;
                justify-content: center;
                padding: 11px 18px;
                font-size: 0.88rem;
            }

            .mobile-bottom-bar {
                display: flex;
                align-items: center;
                justify-content: space-around;
            }

            .bottom-bar-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;
                flex: 1;
                min-width: 0;
                padding: 4px 2px;
                color: #64748b;
                text-align: center;
            }

            .bottom-bar-item .bottom-bar-icon {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .bottom-bar-item svg {
                width: 19px;
                height: 19px;
            }

            .bottom-bar-item span {
                font-size: 0.65rem;
                font-weight: 600;
                line-height: 1.1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }

            .bottom-bar-item.active {
                color: var(--primary);
            }

            .bottom-bar-item.active span {
                font-weight: 700;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar (Shared navbar.php Component) -->
    <?php require __DIR__ . '/../../navbar.php'; ?>

    <!-- Detail Wrapper -->
    <div class="detail-wrapper">
        <!-- Breadcrumbs -->
        <nav class="breadcrumbs">
            <a href="index.php">Beranda</a>
            <span>›</span>
            <a href="artikel.php">Berita &amp; Artikel</a>
            <span>›</span>
            <span><?= htmlspecialchars($article['category']) ?></span>
        </nav>

        <!-- Main Article Card -->
        <article class="article-main-card">
            <header class="detail-header">
                <span class="detail-category-pill"><?= htmlspecialchars($article['category']) ?></span>
                <h1 class="detail-title"><?= htmlspecialchars($article['title']) ?></h1>
                
                <div class="detail-meta-bar">
                    <span class="detail-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?= htmlspecialchars($article['read_time']) ?>
                    </span>
                    <span>•</span>
                    <span class="detail-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <?= date('d M Y', strtotime($article['created_at'])) ?>
                    </span>
                    <span>•</span>
                    <span class="detail-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Oleh <?= htmlspecialchars($article['author']) ?>
                    </span>
                </div>
            </header>

            <!-- Featured Image -->
            <div class="detail-featured-wrap">
                <img src="<?= htmlspecialchars(asset($article['image'])) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="detail-featured-img" />
            </div>

            <!-- Content -->
            <div class="article-content">
                <?= $article['content'] ?>
            </div>

            <!-- Share Buttons -->
            <div class="share-bar-wrap">
                <span class="share-label">Bagikan Wawasan Ini:</span>
                <div class="share-buttons">
                    <?php 
                        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                        $uri = $_SERVER['REQUEST_URI'] ?? ('/artikel.php?slug=' . urlencode($article['slug']));
                        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                        $currentUrl = "{$scheme}://{$host}{$uri}";
                        $waText = urlencode($article['title'] . " - Baca selengkapnya di SeaCV: " . $currentUrl);
                    ?>
                    <a href="https://api.whatsapp.com/send?text=<?= $waText ?>" target="_blank" rel="noopener" class="btn-share wa">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <span>WhatsApp</span>
                    </a>
                    <button type="button" class="btn-share copy" onclick="copyArticleLink()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        <span id="copyBtnText">Salin Tautan</span>
                    </button>
                </div>
            </div>
        </article>

        <!-- Related Articles -->
        <?php if (!empty($relatedArticles)): ?>
            <section class="related-section">
                <div class="related-header">
                    <h3>Artikel Menarik Lainnya</h3>
                </div>
                <div class="related-grid">
                    <?php foreach ($relatedArticles as $rel): ?>
                        <a href="artikel.php?slug=<?= urlencode($rel['slug']) ?>" class="related-card">
                            <img src="<?= htmlspecialchars(asset($rel['image'])) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="related-thumb" loading="lazy" />
                            <div class="related-card-body">
                                <span class="related-cat"><?= htmlspecialchars($rel['category']) ?></span>
                                <h4 class="related-title"><?= htmlspecialchars($rel['title']) ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Integrated Call to Action -->
        <section class="article-cta-box">
            <div class="article-cta-content">
                <h3>Ingin CV Anda Direview &amp; Dibuatkan Oleh Profesional?</h3>
                <p>SeaCV siap membantu menyusun CV ATS &amp; CV Kreatif memikat dengan tata bahasa PUEBI standar rekruter HRD. Pembayaran di akhir setelah draft selesai!</p>
            </div>
            <a href="index.php#katalog-layanan" class="article-cta-btn">
                <span>Pilih Desain CV Anda</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </section>
    </div>



    <!-- Mobile App-Style Bottom Navigation Bar (5 Items) -->
    <nav class="mobile-bottom-bar" id="mobileBottomBar" aria-label="Navigasi Menu Mobile">
        <a href="index.php#katalog-layanan" class="bottom-bar-item">
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
        <a href="index.php#keunggulan" class="bottom-bar-item">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
            <span>Keunggulan</span>
        </a>
        <a href="index.php#cara-pemesanan" class="bottom-bar-item">
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
        <a href="index.php#testimoni" class="bottom-bar-item">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
            </div>
            <span>Testimoni</span>
        </a>
        <a href="artikel.php" class="bottom-bar-item active">
            <div class="bottom-bar-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                    <path d="M6 6h10"></path>
                    <path d="M6 10h10"></path>
                    <path d="M6 14h6"></path>
                </svg>
            </div>
            <span>Artikel</span>
        </a>
    </nav>

    <script>
        function copyArticleLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const btn = document.getElementById('copyBtnText');
                if (btn) {
                    btn.textContent = 'Tersalin!';
                    setTimeout(() => { btn.textContent = 'Salin Tautan'; }, 2000);
                }
            }).catch(() => {
                prompt('Salin link artikel ini:', url);
            });
        }
    </script>
</body>
</html>
