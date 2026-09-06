<?php
// database/migrations/generate_article_assets.php

$dir = __DIR__ . '/../../assets/images/articles';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

$articles = [
    'loker-tren-2026.svg' => [
        'bg1' => '#070d1e',
        'bg2' => '#0f2757',
        'accent' => '#38bdf8',
        'tag' => 'INFO LOKER INDONESIA',
        'title' => 'Tren Rekrutmen Kerja 2026',
        'sub' => 'Peluang Karir Terbanyak di BUMN, Swasta &amp; Startup',
        'icon_type' => 'trend'
    ],
    'cv-ats-vs-kreatif.svg' => [
        'bg1' => '#090d1f',
        'bg2' => '#1e1b4b',
        'accent' => '#818cf8',
        'tag' => 'PANDUAN LENGKAP CV',
        'title' => 'CV ATS vs CV Kreatif',
        'sub' => 'Kapan Waktu Tepat Menggunakan Masing-Masing Format?',
        'icon_type' => 'cv'
    ],
    'rahasia-skrining-hrd.svg' => [
        'bg1' => '#04161b',
        'bg2' => '#0f3c44',
        'accent' => '#2dd4bf',
        'tag' => 'RAHASIA REKRUTER',
        'title' => '6 Detik Pertama Skrining HRD',
        'sub' => 'Penyebab Berkas Langsung Tereliminasi &amp; Solusinya',
        'icon_type' => 'hrd'
    ],
    'syarat-berkas-lamaran.svg' => [
        'bg1' => '#181308',
        'bg2' => '#3d2b0e',
        'accent' => '#fbbf24',
        'tag' => 'CHECKLIST DOKUMEN',
        'title' => 'Syarat Berkas Lamaran Kerja',
        'sub' => 'Daftar Dokumen Wajib &amp; Susunan Standar Rekruter',
        'icon_type' => 'docs'
    ],
    'tips-wawancara-kerja.svg' => [
        'bg1' => '#0b0f24',
        'bg2' => '#1e295d',
        'accent' => '#60a5fa',
        'tag' => 'SUKSES INTERVIEW',
        'title' => 'Trik Wawancara &amp; Negosiasi Gaji',
        'sub' => 'Jawaban Tepat untuk Pertanyaan Menjebak Recruiter',
        'icon_type' => 'interview'
    ],
    'surat-lamaran-efektif.svg' => [
        'bg1' => '#1a0d18',
        'bg2' => '#4c1d3d',
        'accent' => '#f472b6',
        'tag' => 'COVER LETTER PUEBI',
        'title' => 'Surat Lamaran Kerja Yang Menjual',
        'sub' => 'Struktur 4 Paragraf Emas Menarik Hati Perusahaan',
        'icon_type' => 'letter'
    ]
];

foreach ($articles as $filename => $item) {
    $tag = $item['tag'];
    $title = $item['title'];
    $sub = $item['sub'];
    $accent = $item['accent'];
    $bg1 = $item['bg1'];
    $bg2 = $item['bg2'];

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad_{$filename}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$bg1}" />
      <stop offset="60%" stop-color="{$bg2}" />
      <stop offset="100%" stop-color="{$bg1}" />
    </linearGradient>
    <filter id="glow_{$filename}" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="8" result="blur" />
      <feComposite in="SourceGraphic" in2="blur" operator="over" />
    </filter>
    <pattern id="grid_{$filename}" width="40" height="40" patternUnits="userSpaceOnUse">
      <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="1" />
    </pattern>
  </defs>

  <!-- Background -->
  <rect width="800" height="450" fill="url(#bgGrad_{$filename})" />
  <rect width="800" height="450" fill="url(#grid_{$filename})" />

  <!-- Ambient Glow Circles -->
  <circle cx="700" cy="100" r="180" fill="{$accent}" opacity="0.12" filter="url(#glow_{$filename})" />
  <circle cx="100" cy="380" r="160" fill="{$accent}" opacity="0.08" filter="url(#glow_{$filename})" />

  <!-- Illustration Graphic on the Right -->
  <g transform="translate(520, 100)">
    <!-- Main Decorative Card / Backdrop -->
    <rect x="0" y="20" width="220" height="260" rx="18" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" />
    <rect x="15" y="0" width="220" height="260" rx="18" fill="rgba(255,255,255,0.09)" stroke="{$accent}" stroke-width="1.5" stroke-opacity="0.4" />
    
    <!-- Graphic Elements inside Card -->
    <circle cx="65" cy="50" r="22" fill="{$accent}" opacity="0.25" />
    <circle cx="65" cy="50" r="10" fill="{$accent}" />
    
    <rect x="100" y="42" width="105" height="8" rx="4" fill="#ffffff" opacity="0.85" />
    <rect x="100" y="56" width="70" height="6" rx="3" fill="#94a3b8" opacity="0.6" />
    
    <line x1="35" y1="88" x2="215" y2="88" stroke="rgba(255,255,255,0.1)" stroke-width="1.5" />
    
    <!-- Skeleton Lines / Bars -->
    <rect x="35" y="105" width="160" height="7" rx="3.5" fill="#ffffff" opacity="0.4" />
    <rect x="35" y="122" width="180" height="7" rx="3.5" fill="#ffffff" opacity="0.25" />
    <rect x="35" y="139" width="130" height="7" rx="3.5" fill="#ffffff" opacity="0.25" />

    <rect x="35" y="165" width="80" height="26" rx="8" fill="{$accent}" opacity="0.2" stroke="{$accent}" stroke-width="1" />
    <text x="75" y="182" fill="{$accent}" font-family="system-ui, sans-serif" font-size="11" font-weight="bold" text-anchor="middle">VERIFIED</text>

    <!-- Floating Mini Badge -->
    <g transform="translate(-35, 170)">
      <rect width="110" height="52" rx="14" fill="#0b132b" stroke="{$accent}" stroke-width="1.5" filter="url(#glow_{$filename})" />
      <circle cx="28" cy="26" r="14" fill="{$accent}" opacity="0.2" />
      <path d="M 23 26 L 27 30 L 33 22" fill="none" stroke="{$accent}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
      <text x="50" y="24" fill="#ffffff" font-family="system-ui, sans-serif" font-size="11" font-weight="700">SeaCV</text>
      <text x="50" y="37" fill="#94a3b8" font-family="system-ui, sans-serif" font-size="9.5">Career Hub</text>
    </g>
  </g>

  <!-- Left Content Info -->
  <g transform="translate(60, 105)">
    <!-- Pill Category Badge -->
    <rect x="0" y="0" width="190" height="34" rx="17" fill="rgba(255,255,255,0.08)" stroke="{$accent}" stroke-width="1.2" />
    <circle cx="18" cy="17" r="4.5" fill="{$accent}" />
    <text x="32" y="22" fill="{$accent}" font-family="system-ui, sans-serif" font-size="11.5" font-weight="800" letter-spacing="0.08em">{$tag}</text>

    <!-- Main Title -->
    <text x="0" y="82" fill="#ffffff" font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="28" font-weight="800" letter-spacing="-0.02em">
      {$title}
    </text>

    <!-- Subtitle -->
    <text x="0" y="125" fill="#cbd5e1" font-family="system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="15" font-weight="500">
      {$sub}
    </text>

    <!-- Meta Read Time & Author -->
    <g transform="translate(0, 175)">
      <!-- Clock Icon -->
      <circle cx="10" cy="10" r="9" fill="none" stroke="#94a3b8" stroke-width="1.8" />
      <polyline points="10 5 10 10 13 12" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" />
      <text x="28" y="14" fill="#94a3b8" font-family="system-ui, sans-serif" font-size="13" font-weight="500">4 Menit Baca</text>

      <!-- Dot Separator -->
      <circle cx="120" cy="10" r="2.5" fill="#64748b" />

      <!-- Verified Shield -->
      <path d="M 136 4 L 146 1 L 156 4 L 156 10 C 156 16 146 19 146 19 C 146 19 136 16 136 10 Z" fill="none" stroke="{$accent}" stroke-width="1.8" />
      <text x="166" y="14" fill="#cbd5e1" font-family="system-ui, sans-serif" font-size="13" font-weight="600">Tim HR SeaCV</text>
    </g>
  </g>

  <!-- Bottom Accent Stripe -->
  <rect x="0" y="445" width="800" height="5" fill="{$accent}" />
</svg>
SVG;

    file_put_contents($dir . '/' . $filename, $svg);
    echo "Generated: {$filename}\n";
}

echo "ALL ASSETS GENERATED OK!\n";
