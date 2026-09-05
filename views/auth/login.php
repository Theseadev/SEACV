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
