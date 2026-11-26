# Integrasi laporan RL monitoring

Implementasi di `/sirs-online`: menu lama memakai named route yang sekarang tersedia untuk RL3.1–RL3.19, RL4.1–RL4.3, RL5.1–RL5.3 (25 laporan utama), ditambah RL3.11 detail (total 26). RL3.1 URL existing tetap `/sirs-online/rl-3-1`; controller lama tidak dihapus. Detail RL3.11 dapat dipilih lewat dropdown dan membutuhkan ID konten dari hasil sumber.

## Alur

1. Login monitoring existing wajib untuk seluruh route web (MonitoringSession di Kernel).
2. Pilih laporan dan bulan, lalu Tampilkan Laporan. Halaman awal tidak memanggil API.
3. Server mengirim GET bertanda tangan ke `/api/service/sirs/rl/{kode}` dengan bulan/tahun; token dan secret tidak dikirim ke browser. Memakai `services.api_simrs` existing, timeout 60 detik, connect timeout 5 detik, redirect nonaktif.
4. Respons `metaData.code=200` dan array `response` wajib. Empty, HTTP gagal, autentikasi gagal, koneksi gagal dan format invalid dipisahkan. Nilai dirender dengan escaping; tidak menghitung total baru di frontend.
5. Tabel mengikuti field hasil API dan menampilkan array bertingkat melalui rincian. Cetak memakai browser/PDF; CSV rincian berbentuk field-path/nilai, dilindungi dari formula CSV. CSV bukan format XLSX resmi frontend SIMRSBM-TM.

## Batas yang masih ada

- API saat ini berperiode satu bulan penuh. Label kelompok tahunan pada menu lama bukan dukungan agregasi setahun: gunakan bulan yang tersedia; hasil tahunan belum diimplementasikan.
- Renderer tabel umum mempertahankan hasil API, belum meniru susunan header bertingkat/format Excel masing-masing Vue.
- Bukan migrasi seluruh 33 kandidat (mapping/edit/versi lama/ekspor khusus belum disalin).
- Belum deploy atau bersihkan cache. Tidak ada panggilan pengiriman eksternal SIRS.

## Verifikasi

Parser PHP pada controller, client, route, tes; sintaks JavaScript dan katalog 26 laporan diperiksa. Runtime PHP tidak tersedia pada container pemeriksa: PHPUnit dan render Blade/HTTP/browser belum dijalankan, angka database belum dibandingkan. Tes tersedia di `tests/Feature/SirsReportIntegrationTest.php`: route tamu ditolak, halaman awal tanpa request, fixture render dan signature canonical, periode invalid, kondisi kosong/gagal serta escaping.

Jalankan di runtime proyek: `php artisan test --filter=SirsReportIntegrationTest`.
UAT setelah kode API dan monitoring tersedia: login → `/sirs-online` → tiap laporan → pilih bulan → Tampilkan; bandingkan kolom/angka sumber pada bulan sama. Uji detail bertingkat, hasil kosong, API mati, print desktop/HP dan CSV. Pastikan tamu diarahkan ke login dan tidak ada token/signature pada HTML. Koneksi API harus diuji dari server PHP monitoring, bukan menganggap 127.0.0.1 menunjuk server lain.

Cadangan sebelum edit: `/tmp/sirs-ui-backup/` pada lingkungan pemeriksa. File pengguna yang sudah ada pada `docs/` dipertahankan.

## Perbaikan origin laporan 2026-11-25

Client RL menggunakan `services.api_simrs.reports_base_url` (`API_REPORT_BASE_URL`), default `http://api-simrs.test`; fitur lain tetap memakai base_url existing. Probe dari container ke 10.60.11.65 dengan Host api-simrs.test: endpoint RL3.2 menghasilkan 401 header autentikasi hilang, sedangkan path tidak dikenal 404. Ini membuktikan route tersedia di vhost tersebut, bukan keberhasilan request signed dari PHP monitoring. Kredensial existing tidak dibaca atau disalin. Jika lingkungan lain, set API_REPORT_BASE_URL ke origin API-SIMRS yang benar. Tes runtime signed dan hasil laporan masih harus dijalankan setelah login.
