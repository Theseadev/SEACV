<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SeaCV Executive Portal</title>
    <link rel="icon" type="image/png" href="<?= asset('logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('admin.css') ?>">
    <style>
        :root {
            --adm-bg: #f8fafc;
            --adm-surface: #ffffff;
            --adm-border: #e2e8f0;
            --adm-text-main: #0f172a;
            --adm-text-sub: #475569;
            --adm-text-muted: #94a3b8;
            --adm-primary: #2563eb;
            --adm-primary-hover: #1d4ed8;
            --adm-danger: #dc2626;
            --adm-rose-light: #fff1f2;
            --adm-rose-border: #fecdd3;
            --adm-rose: #e11d48;
            --adm-radius-sm: 6px;
            --adm-radius-md: 10px;
            --adm-radius-lg: 16px;
            --adm-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --adm-font-display: 'Outfit', sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body.admin-body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: var(--adm-font);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .adm-login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: radial-gradient(circle at 50% 10%, #eff6ff 0%, #f8fafc 60%, #f1f5f9 100%);
        }

        .adm-login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--adm-radius-lg);
            padding: 36px 30px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            animation: cardAppear 0.3s ease-out;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .adm-login-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .adm-login-logo {
            height: 48px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            margin-bottom: 14px;
            transition: transform 0.2s ease;
        }

        .adm-login-logo:hover {
            transform: scale(1.04);
        }

        .adm-login-title {
            font-family: var(--adm-font-display);
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }

        .adm-login-sub {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.45;
        }

        .adm-form-group {
            margin-bottom: 18px;
        }

        .adm-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 7px;
        }

        .adm-input {
            width: 100%;
            padding: 11px 14px;
            font-size: 14px;
            font-family: inherit;
            color: #0f172a;
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: var(--adm-radius-md);
            outline: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .adm-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            background-color: #ffffff;
        }

        .adm-input::placeholder {
            color: #94a3b8;
        }

        .adm-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 18px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: inherit;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: var(--adm-radius-md);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            transition: all 0.2s ease;
        }

        .adm-btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        .adm-btn-primary:active {
            transform: translateY(0);
        }

        .adm-login-footer {
            text-align: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
        }

        .adm-login-footer a {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .adm-login-footer a:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .adm-login-card {
                padding: 26px 20px;
                border-radius: 14px;
            }
            .adm-login-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="adm-login-wrapper">
        <div class="adm-login-card">
            <div class="adm-login-header">
                <a href="<?= url('/') ?>">
                    <img src="<?= asset('logo.png') ?>" alt="SeaCV Logo" class="adm-login-logo">
                </a>
                <h1 class="adm-login-title">Portal Admin</h1>
                <p class="adm-login-sub">Masuk untuk mengelola katalog & produk SeaCV</p>
            </div>
            
            <?php if(!empty($error)): ?>
                <div style="padding: 12px 16px; border-radius: var(--adm-radius-md); background: var(--adm-rose-light); border: 1px solid var(--adm-rose-border); color: var(--adm-rose); font-size: 13px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= url('/login') ?>" method="post">
                <div class="adm-form-group">
                    <label class="adm-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="adm-input" 
                           placeholder="Masukkan username admin" 
                           value="<?= htmlspecialchars($username ?? '') ?>" 
                           autocomplete="username" required autofocus>
                </div>    
                <div class="adm-form-group" style="margin-bottom: 24px;">
                    <label class="adm-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="adm-input" 
                           placeholder="••••••••" 
                           autocomplete="current-password" required>
                </div>
                <button type="submit" class="adm-btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>
            
            <div class="adm-login-footer">
                <a href="<?= url('/') ?>">&larr; Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>
</html>
