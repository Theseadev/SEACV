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
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 20px; padding: 40px 24px; max-width: 400px; width: 100%; box-shadow: 0 20px 30px rgba(0,0,0,0.5); }
        .emoji-img { width: 96px; height: 96px; object-fit: contain; margin-bottom: 12px; animation: bounce 1.5s infinite; }
        .emoji { font-size: 72px; margin-bottom: 12px; animation: bounce 1.5s infinite; }
        @keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .tag { display: inline-block; font-size: 12px; font-weight: 700; color: #ef4444; background: rgba(239,68,68,0.15); padding: 4px 12px; border-radius: 99px; margin-bottom: 12px; }
        h1 { font-size: 24px; margin-bottom: 8px; }
        p { font-size: 14px; color: #94a3b8; margin-bottom: 24px; }
        a { display: inline-block; padding: 12px 24px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <img src="http://www.clipartbest.com/cliparts/yik/g85/yikg85brT.jpeg" alt="Emoji Ketawa" class="emoji-img" onerror="this.outerHTML='<div class=\'emoji\'>🤣</div>'">
        <div><span class="tag">404 NOT FOUND</span></div>
        <h1>hayolo ngapain haha</h1>
        <p>Gak ada apa-apa di sini wkwk 😜</p>
        <a href="index.php">&larr; Balik ke Beranda</a>
    </div>
</body>
</html>