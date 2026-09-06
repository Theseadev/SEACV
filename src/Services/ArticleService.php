<?php
namespace App\Services;

use PDO;
use Exception;

class ArticleService {
    /**
     * In-memory default / seed dataset
     */
    public static function getDefaultArticles(): array {
        return [
            [
                'id' => 1,
                'slug' => 'tren-rekrutmen-kerja-indonesia-2026',
                'title' => 'Tren Rekrutmen Kerja Indonesia 2026: Sektor Industri yang Paling Banyak Buka Lowongan',
                'category' => 'Info Loker',
                'summary' => 'Ulasan lengkap peta pasar tenaga kerja Indonesia tahun 2026. Ketahui sektor industri yang sedang gencar membuka lowongan dan cara memenangkan persaingan kerja.',
                'image' => 'assets/images/articles/loker-tren-2026.svg',
                'author' => 'Tim HR SeaCV',
                'read_time' => '5 menit baca',
                'views' => 1240,
                'created_at' => '2026-09-01 10:00:00',
                'content' => '
                    <p class="lead-paragraph">Memasuki pertengahan tahun 2026, dinamika pasar tenaga kerja di Indonesia mengalami pergeseran signifikan. Munculnya gelombang digitalisasi tingkat lanjut, ekspansi BUMN, serta maraknya industri energi baru dan FMCG membuka ribuan peluang karir bagi para pencari kerja.</p>

                    <h3>1. Sektor Industri Paling Banyak Membuka Lowongan</h3>
                    <p>Berdasarkan data asosiasi ketenagakerjaan dan tren portal rekrutmen nasional, berikut adalah sektor dengan penyerapan tenaga kerja tertinggi:</p>
                    <ul>
                        <li><strong>Teknologi Informasi &amp; AI Integration:</strong> Kebutuhan developer, data analyst, dan spesialis otomasi operasional meningkat hingga 35%.</li>
                        <li><strong>FMCG &amp; Rantai Pasok (Supply Chain):</strong> Posisi sales specialist, warehouse supervisor, dan digital marketing terus menjadi primadona.</li>
                        <li><strong>BUMN &amp; Sektor Publik:</strong> Rekrutmen Bersama BUMN kembali membuka puluhan ribu formasi untuk lulusan Diploma, S1, hingga berpengalaman.</li>
                        <li><strong>Perbankan &amp; FinTech:</strong> Kebutuhan relationship manager, compliance officer, dan analis kredit tetap stabil tinggi.</li>
                    </ul>

                    <div class="article-callout">
                        <strong>Tips Penting Rekruter:</strong> Jangan hanya melamar di satu portal saja. Gunakan kombinasi LinkedIn, platform karir resmi perusahaan, serta jejaring profesional untuk memperluas peluang dipanggil interview.
                    </div>

                    <h3>2. Standar Baru Kualifikasi Pelamar Kerja</h3>
                    <p>Perusahaan Indonesia saat ini tidak lagi hanya menilai akreditasi kampus atau IPK semata. Tiga hal utama yang paling dicari adalah:</p>
                    <ol>
                        <li><strong>Kesesuaian Skill Teknis (Hard Skills):</strong> Sertifikasi kompetensi atau portofolio nyata yang bisa langsung dibuktikan.</li>
                        <li><strong>Soft Skills &amp; Adaptabilitas:</strong> Kemampuan komunikasi asertif, pemecahan masalah (problem solving), dan kemampuan bekerja dalam tim kolaboratif.</li>
                        <li><strong>Kerapian Berkas Administrasi:</strong> Dokumen lamaran yang rapi, profesional, dan bebas dari kesalahan ketik (typo).</li>
                    </ol>

                    <h3>3. Waspada Modus Penipuan Lowongan Kerja</h3>
                    <p>Di tengah tingginya persaingan, waspadai lowongan kerja palsu yang mengatasnamakan perusahaan besar. Ciri-ciri lowongan palsu antara lain:</p>
                    <ul>
                        <li>Menggunakan email gratisan seperti <em>@gmail.com</em> atau <em>@yahoo.com</em> untuk perusahaan ternama.</li>
                        <li>Mengharuskan pelamar membeli tiket pesawat atau memesan hotel melalui agen travel tertentu dengan janji <em>reimbursement</em>.</li>
                        <li>Proses seleksi tanpa wawancara langsung namun tiba-tiba dinyatakan diterima.</li>
                    </ul>
                    <p>Ingat, perusahaan resmi dan profesional tidak pernah memungut biaya sepeser pun dalam proses seleksi penerimaan karyawan.</p>
                '
            ],
            [
                'id' => 2,
                'slug' => 'panduan-cv-ats-vs-kreatif',
                'title' => 'Panduan Lengkap Memilih Antara CV ATS Friendly dan CV Kreatif: Kapan Harus Dipakai?',
                'category' => 'Tips CV',
                'summary' => 'Masih bingung kapan harus memakai CV ATS dan kapan CV Desain Kreatif? Simak panduan komprehensif agar tidak salah format saat mengirimkan lamaran.',
                'image' => 'assets/images/articles/cv-ats-vs-kreatif.svg',
                'author' => 'Tim HR SeaCV',
                'read_time' => '4 menit baca',
                'views' => 2180,
                'created_at' => '2026-09-02 11:30:00',
                'content' => '
                    <p class="lead-paragraph">Salah satu pertanyaan paling populer yang diajukan para pencari kerja adalah: <em>"Lebih bagus CV ATS Friendly atau CV Kreatif yang berwarna?"</em> Jawabannya sangat bergantung pada jenis industri, ukuran perusahaan, dan cara Anda mengirimkan lamaran.</p>

                    <h3>1. Mengenal CV ATS Friendly</h3>
                    <p><strong>Applicant Tracking System (ATS)</strong> adalah perangkat lunak komputer yang digunakan perusahaan untuk menyaring, memindai, dan menyortir ribuan berkas lamaran secara otomatis sebelum berkas tersebut dibaca manusia.</p>
                    <ul>
                        <li><strong>Ciri Utama:</strong> Format tata letak 1 kolom sederhana, jenis font standar (Arial, Calibri, Helvetica, Georgia), tanpa icon grafis rumit, tanpa tabel bertingkat, dan kaya akan kata kunci (keywords) yang relevan dengan kualifikasi lowongan.</li>
                        <li><strong>Kapan Wajib Pakai CV ATS?</strong>
                            <ul>
                                <li>Saat melamar ke perusahaan multinasional (MNC), korporasi besar (Unilever, Astra, BCA, Telkom), BUMN, atau instansi pemerintah.</li>
                                <li>Saat melamar lewat portal web karir seperti JobStreet, LinkedIn Easy Apply, Workday, atau sistem portal internal perusahaan.</li>
                            </ul>
                        </li>
                    </ul>

                    <div class="article-callout">
                        <strong>Kelebihan CV ATS:</strong> Memastikan seluruh teks pengalaman, keahlian, dan riwayat pendidikan Anda terbaca 100% oleh sistem robot tanpa risiko parsing error.
                    </div>

                    <h3>2. Mengenal CV Kreatif Berdesain</h3>
                    <p><strong>CV Kreatif</strong> adalah dokumen lamaran kerja yang dirancang dengan tata letak visual modern, kombinasi warna elegan, aksen pembagian kolom, serta hierarki visual yang kuat.</p>
                    <ul>
                        <li><strong>Ciri Utama:</strong> Penampilan estetik, elegan, memiliki ruang untuk foto profesional, dan sangat nyaman dipandang mata manusia.</li>
                        <li><strong>Kapan Tepat Menggunakan CV Kreatif?</strong>
                            <ul>
                                <li>Melamar ke industri kreatif: Advertising agency, media, startup, fashion, F&amp;B, perhotelan, atau event organizer.</li>
                                <li>Posisi seperti UI/UX Designer, Graphic Designer, Social Media Specialist, Content Creator, Copywriter, atau Marketing.</li>
                                <li>Melamar secara langsung via WhatsApp HRD, email personal manajer rekruter, atau <em>walk-in interview</em>.</li>
                            </ul>
                        </li>
                    </ul>

                    <h3>3. Kesimpulan: Strategi Terbaik</h3>
                    <p>Saran terbaik dari praktisi HR SeaCV adalah memiliki <strong>keduanya</strong>. Gunakan format ATS untuk portal online berskala masif, dan siapkan versi CV Kreatif estetik untuk pengiriman direct message atau wawancara tatap muka.</p>
                '
            ],
            [
                'id' => 3,
                'slug' => 'rahasia-6-detik-skrining-cv-oleh-hrd',
                'title' => 'Rahasia 6 Detik Skrining CV oleh HRD: Hal Sepele yang Paling Sering Bikin Langsung Gugur',
                'category' => 'Info HRD',
                'summary' => 'Riset membuktikan rata-rata rekruter hanya meluangkan 6 sampai 8 detik untuk menilai sebuah CV. Inilah hal krusial yang dicari HRD dan cara lolos seleksi awal.',
                'image' => 'assets/images/articles/rahasia-skrining-hrd.svg',
                'author' => 'Tim HR SeaCV',
                'read_time' => '4 menit baca',
                'views' => 3420,
                'created_at' => '2026-09-03 09:15:00',
                'content' => '
                    <p class="lead-paragraph">Dengan rata-rata 300 hingga 1.000 lamaran yang masuk untuk satu posisi yang dibuka, tim HRD tidak mungkin membaca setiap baris kata di CV Anda secara teliti di tahap awal. Mereka melakukan <em>skimming scan</em> dalam hitungan 6–8 detik pertama.</p>

                    <h3>1. Tiga Titik Pandang Pertama Rekruter</h3>
                    <p>Berdasarkan studi pelacakan gerak mata (eye-tracking), tatapan HRD akan tertuju secara berurutan pada:</p>
                    <ol>
                        <li><strong>Nama &amp; Ringkasan Profil (Headline):</strong> Apakah deskripsi diri Anda langsung relevan dengan posisi yang dilamar?</li>
                        <li><strong>Jabatan Pekerjaan Terakhir:</strong> Apakah Anda memiliki pengalaman sejenis atau keterampilan yang transferable?</li>
                        <li><strong>Perusahaan &amp; Masa Kerja:</strong> Apakah riwayat kerja stabil atau terdapat pola berpindah terlalu cepat (kurang dari 2 bulan tanpa alasan jelas)?</li>
                    </ol>

                    <div class="article-callout">
                        <strong>Formula Emas Bullet Point:</strong> Jangan tulis deskripsi tugas seperti job desk biasa (contoh: <em>"Mengelola sosial media"</em>). Tuliskan dengan formula <strong>Action + Context + Result</strong> (contoh: <em>"Mengembangkan strategi konten Instagram dan TikTok, menaikkan engagement rate sebesar 45% dalam 3 bulan"</em>).
                    </div>

                    <h3>2. Kesalahan Fatal yang Bikin CV Langsung Dibuang</h3>
                    <ul>
                        <li><strong>Alamat Email Tidak Profesional:</strong> Hindari email lama seperti <em>chibi_boyz99@gmail.com</em>. Buat email khusus lamaran dengan nama lengkap Anda.</li>
                        <li><strong>Typo &amp; Tata Bahasa Berantakan:</strong> Typo pada nama posisi yang Anda lamar adalah tanda kurang teliti dan kurangnya perhatian terhadap detail.</li>
                        <li><strong>Desain Terlalu Penuh &amp; Spasi Rapat:</strong> Jangan gunakan font berukuran 8pt demi memuat 4 halaman. Gunakan ukuran minimal 10–11pt dengan ruang putih (white space) yang seimbang.</li>
                        <li><strong>Tidak Menyertakan Kontak yang Bisa Dihubungi:</strong> Pastikan nomor WhatsApp aktif dan link profil LinkedIn dapat diklik secara langsung.</li>
                    </ul>

                    <h3>3. Kunci Kemenangan di Skrining Awal</h3>
                    <p>Jadikan bagian atas (top 1/3) halaman pertama CV Anda sebagai etalase prestasi terbaik Anda. Saat rekruter tertarik di detik pertama, CV Anda otomatis masuk ke tumpukan <em>"Shortlisted Candidates"</em>.</p>
                '
            ],
            [
                'id' => 4,
                'slug' => 'checklist-syarat-berkas-lamaran-kerja-lengkap',
                'title' => 'Checklist Lengkap Syarat Berkas Lamaran Kerja 2026: Jangan Sampai Ada yang Tertinggal',
                'category' => 'Syarat Berkas',
                'summary' => 'Daftar dokumen wajib saat melamar kerja di perusahaan swasta maupun BUMN. Lengkap dengan urutan penyusunan dokumen PDF agar berkas Anda rapi dan resmi.',
                'image' => 'assets/images/articles/syarat-berkas-lamaran.svg',
                'author' => 'Tim HR SeaCV',
                'read_time' => '5 menit baca',
                'views' => 1890,
                'created_at' => '2026-09-04 14:00:00',
                'content' => '
                    <p class="lead-paragraph">Salah satu alasan paling konyol pelamar gugur di tahap administrasi bukan karena kurang pintar, melainkan karena berkas yang dikirim tidak lengkap atau urutannya tidak beraturan. Simak checklist berkas lamaran kerja terstandar berikut ini.</p>

                    <h3>1. Susunan Dokumen Lamaran (Format Merge PDF)</h3>
                    <p>Jika perusahaan meminta berkas disatukan dalam satu file PDF, susunlah dengan urutan hierarki standar HRD berikut:</p>
                    <ol>
                        <li><strong>Surat Lamaran Kerja (Cover Letter):</strong> Halaman pertama sebagai pengantar resmi kepada pimpinan atau HRD.</li>
                        <li><strong>Curriculum Vitae (CV) Terbaru:</strong> Halaman kedua (maksimal 1–2 halaman).</li>
                        <li><strong>Salinan Ijazah Terakhir:</strong> Legalisir atau scan asli berkualitas jernih.</li>
                        <li><strong>Transkrip Nilai Akademik:</strong> Memperlihatkan daftar mata kuliah dan IPK akhir.</li>
                        <li><strong>Salinan Identitas Diri (KTP &amp; NPWP):</strong> KTP wajib, sedangkan NPWP dilampirkan jika diminta.</li>
                        <li><strong>Sertifikat Kompetensi &amp; Pelatihan:</strong> Sertifikat kursus, sertifikasi keahlian, atau piagam penghargaan yang relevan.</li>
                        <li><strong>Portofolio Hasil Karya:</strong> Wajib untuk posisi desainer, copywriter, programmer, dan arsitek.</li>
                        <li><strong>Surat Keterangan Catatan Kepolisian (SKCK):</strong> Jika dipersyaratkan oleh perusahaan atau BUMN.</li>
                    </ol>

                    <div class="article-callout">
                        <strong>Tips Standar Penamaan File:</strong> Jangan beri nama file hanya <em>"Berkas.pdf"</em> atau <em>"Dokumen_Lamaran.pdf"</em>. Gunakan format: <strong>[Nama Lengkap] - [Posisi yang Dilamar] - Berkas Lamaran.pdf</strong>.
                    </div>

                    <h3>2. Batasan Ukuran File (File Size Optimization)</h3>
                    <p>Banyak server email perusahaan menolak lampiran file di atas 5 MB. Pastikan ukuran file PDF gabungan Anda berada di rentang <strong>1 MB hingga maksimal 2.5 MB</strong> dengan kompresi yang tetap menjaga keterbacaan teks.</p>

                    <h3>3. Pas Foto: Jangan Gunakan Foto Selfie!</h3>
                    <p>Gunakan pas foto formal berlatar belakang polos (merah atau biru) dengan pakaian formal kemeja/blazer. Pastikan pencahayaan cukup dan ekspresi wajah tersenyum ramah serta percaya diri.</p>
                '
            ],
            [
                'id' => 5,
                'slug' => 'strategi-sukses-wawancara-kerja-dan-negosiasi-gaji',
                'title' => 'Strategi Sukses Wawancara Kerja & Trik Negosiasi Gaji yang Sopan Tapi Menguntungkan',
                'category' => 'Tips Interview',
                'summary' => 'Kuasai teknik menjawab pertanyaan jebakan saat interview HRD dan pelajari cara elegan menegosiasikan paket gaji tanpa khawatir ditolak.',
                'image' => 'assets/images/articles/tips-wawancara-kerja.svg',
                'author' => 'Tim HR SeaCV',
                'read_time' => '6 menit baca',
                'views' => 2750,
                'created_at' => '2026-09-05 08:45:00',
                'content' => '
                    <p class="lead-paragraph">Lolos seleksi berkas adalah tiket emas untuk melangkah ke tahap wawancara. Di tahap ini, HRD ingin mengonfirmasi apakah kepribadian dan cara berkomunikasi Anda cocok dengan budaya kerja tim mereka.</p>

                    <h3>1. Menjawab "Ceritakan Tentang Diri Anda" dengan Metode Present-Past-Future</h3>
                    <p>Pertanyaan ini bukan undangan untuk membaca ulang isi KTP Anda. Jawablah dengan struktur ringkas 90 detik:</p>
                    <ul>
                        <li><strong>Present (Saat Ini):</strong> Jelaskan peran profesional Anda saat ini dan fokus keahlian yang Anda kuasai.</li>
                        <li><strong>Past (Masa Lalu):</strong> Sebutkan pengalaman kunci atau pencapaian terbesar yang membentuk keahlian Anda sekarang.</li>
                        <li><strong>Future (Masa Depan):</strong> Ungkapkan mengapa posisi di perusahaan ini adalah langkah tepat berikutnya dan nilai tambah apa yang siap Anda berikan.</li>
                    </ul>

                    <div class="article-callout">
                        <strong>Metode STAR:</strong> Untuk pertanyaan berbasis studi kasus perilaku (behavioral question), gunakan format <strong>Situation</strong> (situasi yang dihadapi), <strong>Task</strong> (tugas Anda), <strong>Action</strong> (langkah nyata yang Anda ambil), dan <strong>Result</strong> (hasil terukur yang dicapai).
                    </div>

                    <h3>2. Cara Menjawab "Berapa Ekspektasi Gaji Anda?"</h3>
                    <p>Jangan pernah menjawab <em>"Terserah standar perusahaan saja"</em>, karena hal tersebut menunjukkan kurangnya riset pasar. Ikuti langkah berikut:</p>
                    <ol>
                        <li>Lakukan riset standar gaji posisi tersebut di kota tempat bekerja melalui platform riset gaji nasional.</li>
                        <li>Sebutkan kisaran angka (range) ketimbang satu angka kaku (contoh: <em>"Berdasarkan riset dan nilai tambah pengalaman saya, kisaran yang saya harapkan berada di angka Rp 6.000.000 hingga Rp 7.500.000, namun saya terbuka untuk mendiskusikan keseluruhan paket kompensasi dan benefit yang berlaku di perusahaan."</em>).</li>
                    </ol>

                    <h3>3. Selalu Siapkan Pertanyaan di Akhir Sesi</h3>
                    <p>Ketika rekruter bertanya: <em>"Ada yang ingin Anda tanyakan kepada kami?"</em> Jangan menjawab <em>"Tidak ada"</em>. Ajukan pertanyaan berkualitas seperti: <em>"Apa indikator keberhasilan utama untuk posisi ini dalam 3 bulan pertama?"</em> Hal ini membuktikan antusiasme dan keseriusan Anda.</p>
                '
            ],
            [
                'id' => 6,
                'slug' => 'cara-menulis-surat-lamaran-kerja-efektif-puebi',
                'title' => 'Cara Menulis Surat Lamaran Kerja (Cover Letter) yang Menghipnotis HRD Sesuai Kaidah PUEBI',
                'category' => 'Surat Lamaran',
                'summary' => 'Surat lamaran kerja adalah pengait awal sebelum HRD membuka CV Anda. Simak struktur 4 paragraf efektif yang membuat rekruter terpikat sejak baris pertama.',
                'image' => 'assets/images/articles/surat-lamaran-efektif.svg',
                'author' => 'Tim HR SeaCV',
                'read_time' => '4 menit baca',
                'views' => 1560,
                'created_at' => '2026-09-05 16:20:00',
                'content' => '
                    <p class="lead-paragraph">Banyak pelamar meremehkan surat lamaran kerja (cover letter) dan hanya menyalin template kuno dari internet yang sudah dipakai ribuan orang. Padahal, cover letter yang tepat adalah cara terbaik menceritakan narasi di balik data angka di CV Anda.</p>

                    <h3>1. Struktur 4 Paragraf Emas Cover Letter</h3>
                    <p>Surat lamaran yang efektif tidak perlu bertele-tele. Cukup 1 halaman dengan 4 paragraf padat:</p>
                    <ul>
                        <li><strong>Paragraf 1 (Pembuka &amp; Hook):</strong> Sebutkan dengan jelas posisi yang Anda tuju, dari mana Anda mengetahui lowongan tersebut, dan satu kalimat hook mengapa Anda antusias dengan visi perusahaan.</li>
                        <li><strong>Paragraf 2 (Kecocokan Kualifikasi &amp; Solusi):</strong> Hubungkan 2–3 keahlian terkuat Anda dengan tantangan yang sedang dihadapi posisi tersebut.</li>
                        <li><strong>Paragraf 3 (Pencapaian Berdampak):</strong> Ceritakan satu studi kasus pencapaian nyata yang pernah Anda berikan di organisasi atau tempat kerja sebelumnya.</li>
                        <li><strong>Paragraf 4 (Penutup &amp; Call to Action):</strong> Sampaikan kesediaan untuk berdiskusi lebih lanjut dalam sesi wawancara dan ucapkan terima kasih secara santun.</li>
                    </ul>

                    <div class="article-callout">
                        <strong>Hindari Kalimat Kuno Ini:</strong> <em>"Dengan ini saya yang bertanda tangan di bawah ini bermaksud untuk mengajukan permohonan pekerjaan..."</em> Gantilah dengan kalimat yang lebih modern dan percaya diri.
                    </div>

                    <h3>2. Ketentuan Format Sesuai PUEBI</h3>
                    <p>Pastikan kaidah penulisan bahasa Indonesia baku dipatuhi:</p>
                    <ul>
                        <li>Gunakan kata <em>"Yth. Bapak/Ibu [Nama atau Posisi]"</em> tanpa kata <em>"Kepada"</em> di depannya.</li>
                        <li>Gunakan kata baku: <em>analisis</em> (bukan analisa), <em>kualitas</em> (bukan kwalitas), <em>antre</em> (bukan antri).</li>
                        <li>Penulisan salam pembuka: <em>"Dengan hormat,"</em> diakhiri dengan tanda koma.</li>
                    </ul>

                    <h3>3. Sesuaikan dengan Karakter Perusahaan</h3>
                    <p>Gunakan nada formal namun dinamis. Perusahaan yang melihat Anda meluangkan waktu untuk menulis cover letter khusus bagi mereka akan menghargai inisiatif Anda jauh lebih tinggi.</p>
                '
            ]
        ];
    }

    /**
     * Ensure database table exists and is populated with seed articles
     */
    public static function ensureTableAndSeed(PDO $pdo): void {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS articles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    slug VARCHAR(255) NOT NULL UNIQUE,
                    title VARCHAR(255) NOT NULL,
                    category VARCHAR(100) NOT NULL,
                    summary TEXT NOT NULL,
                    content LONGTEXT NOT NULL,
                    image VARCHAR(255) NOT NULL,
                    author VARCHAR(100) NOT NULL DEFAULT 'Tim HR SeaCV',
                    read_time VARCHAR(50) NOT NULL DEFAULT '4 menit baca',
                    views INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_category (category),
                    INDEX idx_slug (slug)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            // Check if seeded
            $countStmt = $pdo->query("SELECT COUNT(*) FROM articles");
            $count = (int)$countStmt->fetchColumn();

            if ($count === 0) {
                $defaults = self::getDefaultArticles();
                $insertStmt = $pdo->prepare("
                    INSERT INTO articles (slug, title, category, summary, content, image, author, read_time, views, created_at)
                    VALUES (:slug, :title, :category, :summary, :content, :image, :author, :read_time, :views, :created_at)
                ");

                foreach ($defaults as $art) {
                    $insertStmt->execute([
                        ':slug' => $art['slug'],
                        ':title' => $art['title'],
                        ':category' => $art['category'],
                        ':summary' => $art['summary'],
                        ':content' => $art['content'],
                        ':image' => $art['image'],
                        ':author' => $art['author'],
                        ':read_time' => $art['read_time'],
                        ':views' => $art['views'],
                        ':created_at' => $art['created_at'],
                    ]);
                }
            }
        } catch (Exception $e) {
            // Silently handle table creation errors (fallback to memory will take over)
        }
    }

    /**
     * Get all articles with optional category and search filter
     */
    public static function getAll(?string $category = null, ?string $search = null): array {
        $defaults = self::getDefaultArticles();

        try {
            $pdo = Database::getConnection();
            self::ensureTableAndSeed($pdo);

            $sql = "SELECT * FROM articles WHERE 1=1";
            $params = [];

            if (!empty($category) && $category !== 'Semua') {
                $sql .= " AND category = :category";
                $params[':category'] = $category;
            }

            if (!empty($search)) {
                $sql .= " AND (title LIKE :search OR summary LIKE :search2 OR content LIKE :search3)";
                $params[':search'] = "%{$search}%";
                $params[':search2'] = "%{$search}%";
                $params[':search3'] = "%{$search}%";
            }

            $sql .= " ORDER BY id ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $dbArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($dbArticles)) {
                return $dbArticles;
            }
        } catch (Exception $e) {
            // Database failed or offline, fallback to memory
        }

        // Memory Fallback Filter
        $filtered = $defaults;

        if (!empty($category) && $category !== 'Semua') {
            $filtered = array_filter($filtered, function($item) use ($category) {
                return strcasecmp($item['category'], $category) === 0;
            });
        }

        if (!empty($search)) {
            $s = strtolower($search);
            $filtered = array_filter($filtered, function($item) use ($s) {
                return stripos($item['title'], $s) !== false 
                    || stripos($item['summary'], $s) !== false
                    || stripos($item['category'], $s) !== false;
            });
        }

        return array_values($filtered);
    }

    /**
     * Find single article by slug
     */
    public static function getBySlug(string $slug): ?array {
        $slug = trim($slug);

        try {
            $pdo = Database::getConnection();
            self::ensureTableAndSeed($pdo);

            $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = :slug LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                // Increment view counter
                try {
                    $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id")->execute([':id' => $article['id']]);
                } catch (Exception $ign) {}

                return $article;
            }
        } catch (Exception $e) {
            // Fallback to memory
        }

        foreach (self::getDefaultArticles() as $art) {
            if ($art['slug'] === $slug) {
                return $art;
            }
        }

        return null;
    }

    /**
     * Get related articles
     */
    public static function getRelated(string $currentSlug, int $limit = 3): array {
        $all = self::getAll();
        $related = [];

        foreach ($all as $item) {
            if ($item['slug'] !== $currentSlug) {
                $related[] = $item;
                if (count($related) >= $limit) {
                    break;
                }
            }
        }

        return $related;
    }

    /**
     * Get all unique categories
     */
    public static function getCategories(): array {
        return [
            'Semua',
            'Info Loker',
            'Tips CV',
            'Info HRD',
            'Syarat Berkas',
            'Tips Interview',
            'Surat Lamaran'
        ];
    }
}
