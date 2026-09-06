<?php
// public/api_upgrade.php
require_once "auth.php";

header('Content-Type: application/json');

$versionFile = __DIR__ . '/version.json';

function getVersionConfig($file) {
    if (!file_exists($file)) {
        $default = [
            'version' => '1.0.0',
            'github_repo' => '',
            'github_branch' => 'main',
            'github_token' => '',
            'current_commit' => 'initial',
            'current_commit_msg' => 'Versi Awal SeaCV',
            'last_checked' => '',
            'last_updated' => date('Y-m-d H:i:s')
        ];
        file_put_contents($file, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    if (!is_array($data)) {
        return [];
    }
    return $data;
}

function saveVersionConfig($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function githubRequest($url, $token = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_USERAGENT, 'SeaCV-Updater/1.0');
    
    $headers = [
        'Accept: application/vnd.github.v3+json',
        'User-Agent: SeaCV-Updater/1.0',
        'Cache-Control: no-cache',
        'Pragma: no-cache'
    ];
    if (!empty($token)) {
        $headers[] = 'Authorization: Bearer ' . trim($token);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => $response,
        'error' => $curlError
    ];
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    @unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        @rmdir($dir);
    }
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$config = getVersionConfig($versionFile);

switch ($action) {
    case 'get_info':
        echo json_encode([
            'status' => 'success',
            'data' => [
                'version' => $config['version'] ?? '1.0.0',
                'github_repo' => $config['github_repo'] ?? '',
                'github_branch' => $config['github_branch'] ?? 'main',
                'has_token' => !empty($config['github_token']),
                'current_commit' => $config['current_commit'] ?? 'initial',
                'current_commit_msg' => $config['current_commit_msg'] ?? 'Versi Awal',
                'last_checked' => $config['last_checked'] ?? 'Belum pernah',
                'last_updated' => $config['last_updated'] ?? 'Belum pernah',
                'php_version' => PHP_VERSION,
                'zip_supported' => class_exists('ZipArchive'),
                'curl_supported' => function_exists('curl_init')
            ]
        ]);
        break;

    case 'save_config':
        $repo = trim($_POST['repo'] ?? '');
        $branch = trim($_POST['branch'] ?? 'main');
        $token = trim($_POST['token'] ?? '');
        
        // Clean repo format: remove https://github.com/ if user pasted full url
        $repo = preg_replace('#^https?://github\.com/#i', '', $repo);
        $repo = trim($repo, '/');
        
        $config['github_repo'] = $repo;
        $config['github_branch'] = !empty($branch) ? $branch : 'main';
        if ($token !== '***KEEP***') {
            $config['github_token'] = $token;
        }
        
        saveVersionConfig($versionFile, $config);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Pengaturan repositori GitHub berhasil disimpan.',
            'repo' => $config['github_repo'],
            'branch' => $config['github_branch']
        ]);
        break;

    case 'check':
        $logs = [];
        $time = date('H:i:s');
        
        $repo = trim($config['github_repo'] ?? '');
        $branch = trim($config['github_branch'] ?? 'main');
        $token = trim($config['github_token'] ?? '');
        
        if (empty($repo)) {
            $logs[] = ["time" => $time, "type" => "WARN", "msg" => "Repositori GitHub belum dikonfigurasi. Masukkan 'username/repo' di form pengaturan."];
            echo json_encode([
                'status' => 'error',
                'message' => 'Repositori belum diatur.',
                'logs' => $logs
            ]);
            exit;
        }

        $logs[] = ["time" => $time, "type" => "INFO", "msg" => "Menghubungkan ke GitHub API..."];
        $logs[] = ["time" => $time, "type" => "GIT", "msg" => "Target: github.com/{$repo} (Branch: {$branch})"];

        $apiUrl = "https://api.github.com/repos/{$repo}/commits/{$branch}";
        $res = githubRequest($apiUrl, $token);

        if (!empty($res['error'])) {
            $logs[] = ["time" => date('H:i:s'), "type" => "ERROR", "msg" => "Koneksi cURL gagal: " . $res['error']];
            echo json_encode(['status' => 'error', 'message' => 'Koneksi internet/API gagal.', 'logs' => $logs]);
            exit;
        }

        if ($res['code'] === 404) {
            $logs[] = ["time" => date('H:i:s'), "type" => "ERROR", "msg" => "Repositori atau branch tidak ditemukan (HTTP 404). Pastikan nama 'username/repo' dan branch '{$branch}' sudah benar, atau masukkan GitHub Personal Access Token jika repositori bersifat Private."];
            echo json_encode(['status' => 'error', 'message' => 'Repositori tidak ditemukan atau bersifat private.', 'logs' => $logs]);
            exit;
        }

        if ($res['code'] === 401 || $res['code'] === 403) {
            $logs[] = ["time" => date('H:i:s'), "type" => "ERROR", "msg" => "Autentikasi gagal atau limit API tercapai (HTTP {$res['code']}). Masukkan GitHub Personal Access Token."];
            echo json_encode(['status' => 'error', 'message' => 'Autentikasi GitHub gagal atau terkena rate limit.', 'logs' => $logs]);
            exit;
        }

        if ($res['code'] !== 200) {
            $logs[] = ["time" => date('H:i:s'), "type" => "ERROR", "msg" => "GitHub API mengembalikan status code {$res['code']}."];
            echo json_encode(['status' => 'error', 'message' => 'Status error: ' . $res['code'], 'logs' => $logs]);
            exit;
        }

        $commitData = json_decode($res['body'], true);
        if (!isset($commitData['sha'])) {
            $logs[] = ["time" => date('H:i:s'), "type" => "ERROR", "msg" => "Respon GitHub API tidak valid."];
            echo json_encode(['status' => 'error', 'message' => 'Respon GitHub tidak valid.', 'logs' => $logs]);
            exit;
        }

        $remoteSha = substr($commitData['sha'], 0, 7);
        $fullSha = $commitData['sha'];
        $commitMsg = trim(explode("\n", $commitData['commit']['message'] ?? '')[0]);
        $commitAuthor = $commitData['commit']['author']['name'] ?? 'Unknown';
        $commitDate = date('d M Y H:i', strtotime($commitData['commit']['author']['date'] ?? 'now'));
        
        $localCommit = $config['current_commit'] ?? 'initial';
        $hasUpdate = ($localCommit !== $fullSha && $localCommit !== $remoteSha);

        $config['last_checked'] = date('Y-m-d H:i:s');
        saveVersionConfig($versionFile, $config);

        $logs[] = ["time" => date('H:i:s'), "type" => "SUCCESS", "msg" => "Terhubung ke GitHub! Commit terbaru: #{$remoteSha} oleh {$commitAuthor}"];
        $logs[] = ["time" => date('H:i:s'), "type" => "INFO", "msg" => "Pesan: \"{$commitMsg}\" ({$commitDate})"];

        if ($hasUpdate) {
            $logs[] = ["time" => date('H:i:s'), "type" => "UPDATE", "msg" => "★ Pembaruan baru TERSEDIA! Klik tombol 'Mulai Upgrade Sekarang' untuk memperbarui."];
        } else {
            $logs[] = ["time" => date('H:i:s'), "type" => "SUCCESS", "msg" => "Sistem Anda sudah menggunakan versi paling mutakhir (Up to Date)."];
        }

        echo json_encode([
            'status' => 'success',
            'has_update' => $hasUpdate,
            'remote_sha' => $remoteSha,
            'full_sha' => $fullSha,
            'commit_msg' => $commitMsg,
            'commit_author' => $commitAuthor,
            'commit_date' => $commitDate,
            'local_commit' => $localCommit,
            'logs' => $logs
        ]);
        break;

    case 'apply':
        $logs = [];
        $t = function($type, $msg) use (&$logs) {
            $logs[] = ["time" => date('H:i:s'), "type" => $type, "msg" => $msg];
        };

        $repo = trim($config['github_repo'] ?? '');
        $branch = trim($config['github_branch'] ?? 'main');
        $token = trim($config['github_token'] ?? '');

        if (empty($repo)) {
            $t('ERROR', 'Repositori belum diatur.');
            echo json_encode(['status' => 'error', 'message' => 'Repositori belum diatur.', 'logs' => $logs]);
            exit;
        }

        if (!class_exists('ZipArchive') && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $t('ERROR', 'Ekstensi PHP ZipArchive tidak aktif pada server ini.');
            echo json_encode(['status' => 'error', 'message' => 'ZipArchive tidak tersedia.', 'logs' => $logs]);
            exit;
        }

        $t('INFO', '=== MEMULAI PROSES PEMBARUAN SISTEM SEACV ===');
        $t('PROTECT', 'Safety Shield aktif: Memproteksi file config.php & folder uploads/ agar tidak tertimpa...');

        // Step 1: Download zipball from GitHub
        $zipUrl = "https://api.github.com/repos/{$repo}/zipball/{$branch}";
        $t('DOWNLOAD', "Mengunduh arsip paket dari GitHub (Branch: {$branch})...");
        
        $tempZip = __DIR__ . '/temp_update_' . uniqid() . '.zip';
        $tempExtractDir = __DIR__ . '/temp_extract_' . uniqid();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $zipUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SeaCV-Updater/1.0');
        
        $headers = [
            'Accept: application/vnd.github.v3+json',
            'User-Agent: SeaCV-Updater/1.0'
        ];
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . trim($token);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $zipContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if (!empty($curlErr) || $httpCode !== 200 || empty($zipContent)) {
            $t('ERROR', "Gagal mengunduh arsip zip dari GitHub (HTTP Code: {$httpCode}, Err: {$curlErr})");
            echo json_encode(['status' => 'error', 'message' => 'Download arsip gagal.', 'logs' => $logs]);
            exit;
        }

        file_put_contents($tempZip, $zipContent);
        $zipSize = round(filesize($tempZip) / 1024, 1);
        $t('DOWNLOAD', "Arsip berhasil diunduh ({$zipSize} KB).");

        // Step 2: Extract zipball
        $t('EXTRACT', 'Membuka dan mengekstrak arsip...');
        if (!is_dir($tempExtractDir)) {
            mkdir($tempExtractDir, 0777, true);
        }

        $extractedOk = false;
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($tempZip) === true) {
                $zip->extractTo($tempExtractDir);
                $zip->close();
                $extractedOk = true;
            }
        } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            @exec("powershell -Command \"Expand-Archive -Path '{$tempZip}' -DestinationPath '{$tempExtractDir}' -Force\"");
            $extractedOk = true;
        }

        @unlink($tempZip);

        if (!$extractedOk) {
            $t('ERROR', 'Gagal mengekstrak file arsip zip.');
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuka zip.', 'logs' => $logs]);
            exit;
        }

        // Find the extracted root folder (GitHub packs everything inside owner-repo-hash/)
        $subdirs = glob($tempExtractDir . '/*', GLOB_ONLYDIR);
        $sourceRoot = (!empty($subdirs) && is_dir($subdirs[0])) ? $subdirs[0] : $tempExtractDir;

        // Step 3: Copy files to project root while strictly PROTECTING critical files
        $t('UPDATE', 'Menyinkronkan file sistem baru...');

        $protectedFiles = [
            'config.php',        // Database credentials must NEVER be overwritten
            'version.json',      // Current repository config
            '.env',
            '.git'
        ];
        $protectedFolders = [
            'uploads',           // User-uploaded templates & images must NEVER be wiped
            '.git'
        ];

        $updatedCount = 0;
        $skippedCount = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceRoot, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($sourceRoot) + 1);
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Check if relative path starts with any protected folder
            $isProtected = false;
            foreach ($protectedFolders as $pf) {
                if ($relativePath === $pf || strpos($relativePath, $pf . '/') === 0) {
                    $isProtected = true;
                    break;
                }
            }

            // Check if file is in protected files list
            if (in_array($relativePath, $protectedFiles)) {
                $isProtected = true;
            }

            if ($isProtected) {
                $skippedCount++;
                continue;
            }

            $targetPath = __DIR__ . '/' . $relativePath;

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    @mkdir($targetPath, 0777, true);
                }
            } else {
                $targetDir = dirname($targetPath);
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0777, true);
                }
                if (@copy($item->getPathname(), $targetPath)) {
                    $updatedCount++;
                }
            }
        }

        // Clean up temporary extracted folder
        rrmdir($tempExtractDir);

        $t('EXTRACT', "Sinkronisasi selesai: {$updatedCount} file diperbarui, {$skippedCount} file/media data diproteksi.");

        // Step 4: Fetch remote commit SHA to update version.json
        $apiCommit = githubRequest("https://api.github.com/repos/{$repo}/commits/{$branch}", $token);
        if ($apiCommit['code'] === 200) {
            $cd = json_decode($apiCommit['body'], true);
            if (!empty($cd['sha'])) {
                $config['current_commit'] = substr($cd['sha'], 0, 7);
                $config['current_commit_msg'] = trim(explode("\n", $cd['commit']['message'] ?? '')[0]);
            }
        }
        $config['last_updated'] = date('Y-m-d H:i:s');
        saveVersionConfig($versionFile, $config);

        // Step 5: Clear OPcache if supported
        if (function_exists('opcache_reset')) {
            @opcache_reset();
            $t('CACHE', 'OPcache runtime berhasil di-reset.');
        }

        $t('SUCCESS', '★ SELAMAT! SISTEM SEACV BERHASIL DIPERBARUI KE VERSI TERBARU DARI GITHUB.');
        $t('INFO', 'Anda dapat me-refresh halaman dashboard untuk menikmati fitur-fitur terbaru.');

        echo json_encode([
            'status' => 'success',
            'message' => 'Pembaruan berhasil diterapkan.',
            'updated_count' => $updatedCount,
            'new_commit' => $config['current_commit'],
            'logs' => $logs
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali.']);
        break;
}
