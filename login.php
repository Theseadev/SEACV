<?php
// login.php has been disabled and hidden for security.
http_response_code(404);
header("HTTP/1.1 404 Not Found");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Hayolo Ngapain Haha</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; text-align: center; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background: #0f172a; color: #f8fafc; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 24px; padding: 44px 28px; max-width: 420px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6); }
        .emoji-img { width: 140px; height: 140px; object-fit: contain; margin-bottom: 16px; filter: drop-shadow(0 12px 18px rgba(0,0,0,0.45)); animation: bounce 1.5s infinite; }
        .emoji { font-size: 80px; margin-bottom: 14px; animation: bounce 1.5s infinite; }
        @keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .tag { display: inline-block; font-size: 12px; font-weight: 800; letter-spacing: 0.08em; color: #ef4444; background: rgba(239,68,68,0.15); padding: 5px 14px; border-radius: 99px; margin-bottom: 14px; }
        h1 { font-size: 26px; font-weight: 800; margin-bottom: 8px; }
        p { font-size: 15px; color: #94a3b8; margin-bottom: 28px; }
        a { display: inline-block; padding: 13px 28px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; border-radius: 12px; box-shadow: 0 8px 16px rgba(37,99,235,0.4); transition: transform 0.2s; }
        a:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="card">
        <img src="assets/images/laugh_transparent.png" alt="Emoji Ketawa" class="emoji-img" onerror="this.outerHTML='<div class=\'emoji\'>🤣</div>'">
        <div><span class="tag">404 NOT FOUND</span></div>
        <h1>hayolo ngapain haha</h1>
        <p>Gak ada apa-apa di sini wkwk 😜</p>
        <a href="index.php">&larr; Balik ke Beranda</a>
    </div>
</body>
</html>