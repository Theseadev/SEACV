<?php
// login.php has been disabled and hidden for security.
// Admin login is moved to a secret path.
http_response_code(404);
header("HTTP/1.1 404 Not Found");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; text-align: center; padding: 60px 20px; background: #f8fafc; color: #0f172a; }
        h1 { font-size: 48px; margin-bottom: 8px; color: #334155; }
        p { font-size: 16px; color: #64748b; max-width: 480px; margin: 0 auto 24px; }
        a { color: #2563eb; text-decoration: none; font-weight: 600; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>404</h1>
    <p>Halaman yang Anda tuju tidak ditemukan atau sudah dipindahkan.</p>
    <p><a href="index.php">&larr; Kembali ke Beranda</a></p>
</body>
</html>