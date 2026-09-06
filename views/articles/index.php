<?php
// views/articles/index.php - Halaman Berita & Artikel Karir SeaCV

$pageTitle = 'Berita, Info Lowongan & Edukasi Karir Indonesia | SeaCV';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Informasi lowongan pekerjaan, panduan CV ATS dan CV Kreatif, rahasia HRD, syarat berkas lamaran, dan tips karir terpercaya di Indonesia.">
    
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
            line-height: 1.6;
            overflow-x: hidden;
            padding-bottom: 70px;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Hero Banner Section (Batik Megamendung Theme) */
        .article-hero {
            position: relative;
            background: linear-gradient(135deg, #070d1e 0%, #0d1b3e 50%, #0f2757 100%);
            border-bottom: 1px solid rgba(56, 189, 248, 0.25);
            padding: 54px 6% 64px;
            overflow: hidden;
            color: #ffffff;
            text-align: center;
        }

        .article-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('batik-megamendung.jpg');
            background-size: cover;
            background-position: center 40%;
            background-repeat: no-repeat;
            opacity: 0.38;
            pointer-events: none;
            z-index: 0;
            filter: contrast(1.15) saturate(1.25);
        }

        .article-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(7, 13, 30, 0.92) 0%, rgba(13, 27, 62, 0.65) 50%, rgba(7, 13, 30, 0.88) 100%),
                        linear-gradient(180deg, rgba(7, 13, 30, 0.3) 0%, rgba(7, 13, 30, 0.85) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .article-hero-content {
            position: relative;
            z-index: 1;
            max-width: 820px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.35);
            padding: 6px 16px;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--accent-cyan);
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            backdrop-filter: blur(8px);
        }

        .article-hero h2 {
            font-family: var(--font-heading);
            font-size: 2.3rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
            text-shadow: 0 3px 12px rgba(0,0,0,0.4);
        }

        .article-hero p {
            font-size: 1.05rem;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        /* Search Bar */
        .search-bar-wrap {
            max-width: 580px;
            margin: 0 auto;
            position: relative;
        }

        .search-form {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: var(--radius-pill);
            padding: 6px 8px 6px 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            transition: var(--transition);
        }

        .search-form:focus-within {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--accent-cyan);
            box-shadow: 0 8px 28px rgba(56, 189, 248, 0.3);
        }

        .search-form input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #ffffff;
            font-size: 0.95rem;
            font-family: var(--font-body);
        }

        .search-form input::placeholder {
            color: #94a3b8;
        }

        .search-form button {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            color: #ffffff;
            padding: 10px 22px;
            border-radius: var(--radius-pill);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .search-form button:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.5);
        }

        /* Filter Pills Section (Desktop / Laptop View: Floating white card) */
        .filter-section {
            max-width: 1280px;
            margin: -24px auto 36px;
            padding: 0 6%;
            position: relative;
            z-index: 10;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 14px 20px;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .filter-scroll-wrapper {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .filter-pills-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            background: #f1f5f9;
            border: 1px solid transparent;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .filter-pill:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        .filter-pill.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        /* Di laptop/PC, ikon di-hide agar persis seperti tampilan sebelumnya */
        .filter-pill-icon {
            display: none;
        }

        .filter-stats-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .filter-stats-text strong {
            color: var(--primary);
        }

        .desktop-reset-link {
            color: #ef4444;
            text-decoration: none;
            font-size: 0.82rem;
            margin-left: 4px;
            font-weight: 600;
            transition: var(--transition);
        }

        .desktop-reset-link:hover {
            text-decoration: underline;
        }

        /* Di laptop/PC, bar judul section tambahan disembunyikan agar tampilan bersih seperti sebelumnya */
        .articles-section-bar {
            display: none;
        }

        /* Main Content Container */
        .main-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 6%;
        }

        /* Article Cards Grid */
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-bottom: 56px;
        }

        .article-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: #cbd5e1;
        }

        .card-thumb-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #0f172a;
            overflow: hidden;
        }

        .card-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .article-card:hover .card-thumb-img {
            transform: scale(1.04);
        }

        .card-category-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--accent-cyan);
            font-size: 0.74rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            letter-spacing: 0.04em;
        }

        .card-body {
            padding: 22px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .card-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .card-title {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.35;
            margin-bottom: 10px;
            transition: color 0.2s;
        }

        .article-card:hover .card-title {
            color: var(--primary);
        }

        .card-excerpt {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }

        .card-author {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .card-author-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .card-read-more {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: transform 0.2s ease;
        }

        .article-card:hover .card-read-more {
            transform: translateX(4px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 56px 24px;
            margin-bottom: 48px;
            box-shadow: var(--shadow-sm);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 14px;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 22px;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Banner CTA Box */
        .article-cta-box {
            position: relative;
            background: linear-gradient(135deg, #070d1e 0%, #0d1b3e 50%, #0f2757 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: var(--radius-lg);
            padding: 38px 42px;
            margin-bottom: 60px;
            box-shadow: 0 16px 36px -4px rgba(7, 13, 30, 0.4);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            color: #ffffff;
        }

        .article-cta-box::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('batik-megamendung.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.35;
            pointer-events: none;
            filter: contrast(1.15) saturate(1.25);
        }

        .article-cta-content {
            position: relative;
            z-index: 1;
            max-width: 680px;
        }

        .article-cta-content h3 {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .article-cta-content p {
            font-size: 0.95rem;
            color: #cbd5e1;
            line-height: 1.5;
        }

        .article-cta-btn {
            position: relative;
            z-index: 1;
            white-space: nowrap;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 13px 28px;
            border-radius: var(--radius-pill);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.45);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .article-cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(37, 99, 235, 0.6);
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

        @media (max-width: 1024px) {
            .articles-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 22px;
            }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            body { padding-bottom: calc(65px + env(safe-area-inset-bottom, 0px)); }

            /* Compact & Sleek Hero Section on Mobile */
            .article-hero {
                padding: 24px 14px 26px;
                border-bottom: 1px solid rgba(56, 189, 248, 0.15);
            }
            .hero-badge {
                font-size: 0.68rem;
                padding: 4px 12px;
                margin-bottom: 10px;
                letter-spacing: 0.04em;
                gap: 5px;
            }
            .article-hero h2 {
                font-size: 1.3rem;
                line-height: 1.28;
                margin-bottom: 8px;
                letter-spacing: -0.3px;
            }
            .article-hero p {
                font-size: 0.84rem;
                line-height: 1.5;
                color: #cbd5e1;
                margin-bottom: 16px;
            }
            .search-bar-wrap {
                max-width: 100%;
            }
            .search-form {
                padding: 4px 4px 4px 14px;
                border-radius: 24px;
                height: 44px;
            }
            .search-form input {
                font-size: 0.84rem;
                min-width: 0;
            }
            .search-form button {
                padding: 8px 14px;
                font-size: 0.8rem;
                gap: 4px;
                border-radius: 20px;
                flex-shrink: 0;
            }

            /* Responsive Modern Edge-to-Edge Filter Chips on Mobile */
            .filter-section {
                margin: 10px 0 14px;
                padding: 0 14px;
            }
            .filter-card {
                background: transparent;
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                display: block;
                border-radius: 0;
            }
            .filter-stats-text {
                display: none;
            }
            .filter-scroll-wrapper {
                margin: 0 -14px;
                padding: 4px 14px 8px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            .filter-scroll-wrapper::-webkit-scrollbar {
                display: none;
            }
            .filter-pills-wrap {
                display: flex;
                gap: 8px;
                flex-wrap: nowrap;
            }
            .filter-pill {
                padding: 7px 15px;
                font-size: 0.8rem;
                gap: 6px;
                white-space: nowrap;
                flex-shrink: 0;
                border-radius: 20px;
                background: #ffffff;
                border: 1px solid var(--border-color);
                box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
                color: #475569;
            }
            .filter-pill:hover {
                background: var(--primary-light);
                color: var(--primary);
            }
            .filter-pill.active {
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: #ffffff;
                border-color: transparent;
                box-shadow: 0 3px 10px rgba(37, 99, 235, 0.35);
            }
            .filter-pill-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .filter-pill-icon svg {
                width: 13px;
                height: 13px;
                stroke: currentColor;
            }
            .filter-pill.active .filter-pill-icon svg {
                stroke: #ffffff;
            }

            /* Section Title Bar on Mobile (Tampil di atas artikel grid khusus mobile) */
            .articles-section-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
                padding-bottom: 10px;
                border-bottom: 1px solid var(--border-color);
            }
            .section-title-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .section-title {
                font-family: var(--font-heading);
                font-size: 1.1rem;
                font-weight: 700;
                color: var(--text-main);
                margin: 0;
            }
            .section-count-badge {
                font-size: 0.72rem;
                padding: 2px 8px;
                border-radius: 20px;
                background: #eff6ff;
                color: var(--primary);
                font-weight: 600;
                border: 1px solid #bfdbfe;
            }
            .btn-reset-filter {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 0.74rem;
                padding: 3px 9px;
                border-radius: 20px;
                background: #fef2f2;
                color: #dc2626;
                border: 1px solid #fecaca;
                text-decoration: none;
                font-weight: 600;
            }
            .btn-reset-filter:hover {
                background: #fee2e2;
                color: #dc2626;
            }

            /* Article Grid & Cards */
            .main-container {
                padding: 0 14px 20px;
            }
            .articles-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 30px;
            }
            .article-card {
                border-radius: 14px;
            }
            .card-thumb-wrap {
                aspect-ratio: 16 / 9;
            }
            .card-body {
                padding: 16px;
            }
            .card-category-badge {
                top: 10px;
                left: 10px;
                font-size: 0.68rem;
                padding: 3px 9px;
            }
            .card-title {
                font-size: 1.05rem;
                line-height: 1.35;
                margin-bottom: 6px;
            }
            .card-excerpt {
                font-size: 0.82rem;
                line-height: 1.5;
                margin-bottom: 12px;
                -webkit-line-clamp: 2;
            }
            .card-footer {
                padding-top: 10px;
            }
            .card-author {
                font-size: 0.78rem;
            }
            .card-author-avatar {
                width: 22px;
                height: 22px;
                font-size: 0.65rem;
            }
            .card-read-more {
                font-size: 0.8rem;
            }

            /* CTA Box on Mobile */
            .article-cta-box {
                flex-direction: column;
                text-align: center;
                padding: 22px 16px;
                border-radius: 14px;
                margin-bottom: 30px;
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

            /* Mobile Bottom Bar (5 Tabs) */
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
                transition: var(--transition);
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

    <!-- Hero Header -->
    <header class="article-hero">
        <div class="article-hero-content">
            <div class="hero-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>PUSAT EDUKASI KARIR &amp; LOKER</span>
            </div>
            <h2>Berita Karir, Info Loker &amp; Panduan Sukses Kerja</h2>
            <p>Wawasan lowongan kerja Indonesia, rahasia HRD, panduan CV ATS vs Kreatif, dan syarat berkas lamaran</p>

            <!-- Search Bar Form -->
            <div class="search-bar-wrap">
                <form action="artikel.php" method="get" class="search-form">
                    <?php if (!empty($selectedCategory) && $selectedCategory !== 'Semua'): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                    <?php endif; ?>
                    <input type="text" name="search" placeholder="Cari topik loker, tips CV..." value="<?= htmlspecialchars($searchQuery) ?>" />
                    <button type="submit">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>Cari</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Filter Pills Section (Desktop: Floating Card | Mobile: Edge-to-Edge Swipeable) -->
    <section class="filter-section" aria-label="Filter Kategori Artikel">
        <div class="filter-card">
            <div class="filter-scroll-wrapper">
                <div class="filter-pills-wrap">
                    <?php foreach ($categories as $cat): ?>
                        <?php 
                            $isActive = ($selectedCategory === $cat);
                            $urlQuery = 'artikel.php?category=' . urlencode($cat);
                            if (!empty($searchQuery)) {
                                $urlQuery .= '&search=' . urlencode($searchQuery);
                            }
                        ?>
                        <a href="<?= $urlQuery ?>" class="filter-pill <?= $isActive ? 'active' : '' ?>">
                            <span class="filter-pill-icon">
                                <?php if ($cat === 'Semua'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
                                <?php elseif ($cat === 'Info Loker'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                <?php elseif ($cat === 'Tips CV'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <?php elseif ($cat === 'Info HRD'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <?php elseif ($cat === 'Syarat Berkas'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                <?php elseif ($cat === 'Tips Interview'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                                <?php elseif ($cat === 'Surat Lamaran'): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <?php endif; ?>
                            </span>
                            <span><?= htmlspecialchars($cat) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="filter-stats-text">
                Menampilkan <strong><?= count($articles) ?></strong> Pembahasan
                <?php if ((!empty($selectedCategory) && $selectedCategory !== 'Semua') || !empty($searchQuery)): ?>
                    • <a href="artikel.php" class="desktop-reset-link" title="Reset filter & pencarian">Reset Filter</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <main class="main-container">
        <!-- Section Bar (Heading & Count) -->
        <div class="articles-section-bar">
            <div class="section-title-wrap">
                <h3 class="section-title">
                    <?= ($selectedCategory !== 'Semua') ? htmlspecialchars($selectedCategory) : 'Semua Pembahasan Karir' ?>
                </h3>
                <span class="section-count-badge"><?= count($articles) ?> Artikel</span>
            </div>
            <?php if ((!empty($selectedCategory) && $selectedCategory !== 'Semua') || !empty($searchQuery)): ?>
                <a href="artikel.php" class="btn-reset-filter" title="Reset filter & pencarian">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Reset Filter</span>
                </a>
            <?php endif; ?>
        </div>
        <?php if (empty($articles)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <h3>Artikel Tidak Ditemukan</h3>
                <p>Maaf, tidak ada pembahasan yang cocok dengan kata kunci "<strong><?= htmlspecialchars($searchQuery) ?></strong>". Coba kata kunci lain atau reset filter.</p>
                <a href="artikel.php" class="article-cta-btn" style="display:inline-flex;">Reset Pencarian</a>
            </div>
        <?php else: ?>
            <!-- Articles Grid -->
            <div class="articles-grid">
                <?php foreach ($articles as $art): ?>
                    <article class="article-card">
                        <div class="card-thumb-wrap">
                            <img src="<?= htmlspecialchars(asset($art['image'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" class="card-thumb-img" loading="lazy" />
                            <span class="card-category-badge"><?= htmlspecialchars($art['category']) ?></span>
                        </div>
                        <div class="card-body">
                            <div class="card-meta">
                                <span class="card-meta-item">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?= htmlspecialchars($art['read_time']) ?>
                                </span>
                                <span>•</span>
                                <span class="card-meta-item">
                                    <?= date('d M Y', strtotime($art['created_at'])) ?>
                                </span>
                            </div>

                            <a href="artikel.php?slug=<?= urlencode($art['slug']) ?>">
                                <h3 class="card-title"><?= htmlspecialchars($art['title']) ?></h3>
                            </a>

                            <p class="card-excerpt"><?= htmlspecialchars($art['summary']) ?></p>

                            <div class="card-footer">
                                <div class="card-author">
                                    <div class="card-author-avatar">S</div>
                                    <span><?= htmlspecialchars($art['author']) ?></span>
                                </div>
                                <a href="artikel.php?slug=<?= urlencode($art['slug']) ?>" class="card-read-more">
                                    <span>Baca</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Integrated Call-to-Action Banner -->
        <section class="article-cta-box">
            <div class="article-cta-content">
                <h3>Sudah Siap Memikat HRD dengan CV Profesional?</h3>
                <p>Jangan biarkan lamaran Anda terabaikan karena format CV yang salah. Pilih ratusan template CV ATS &amp; Kreatif siap pakai di SeaCV, dikerjakan kilat oleh tim berpengalaman dengan sistem pembayaran di akhir!</p>
            </div>
            <a href="index.php#katalog-layanan" class="article-cta-btn">
                <span>Pilih Template Sekarang</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer-links">
            <a href="index.php">Beranda</a>
            <a href="index.php#katalog-layanan">Katalog Template</a>
            <a href="index.php#keunggulan">Keunggulan</a>
            <a href="index.php#cara-pemesanan">Cara Pemesanan</a>
            <a href="artikel.php">Berita &amp; Artikel</a>
        </div>
        <p>&copy; <?= date('Y') ?> SeaCV Professional Hub. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

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

</body>
</html>
