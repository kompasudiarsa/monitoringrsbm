<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


use App\Http\Controllers\BpjsAntreanMonitorController;
use App\Services\SimrsRiwayatKontrolService;
use App\Http\Controllers\JadwalDokterController;
use App\Http\Controllers\RegistrasiPasienController;
use App\Http\Controllers\BpjsBridgeMonitoringController;
use App\Http\Controllers\SirsOnlineController;

Route::prefix('sirs-online')
    ->name('sirs.')
    ->group(function () {

        Route::get(
            '/',
            [SirsOnlineController::class, 'menu']
        )->name('menu');

        /*
        |--------------------------------------------------------------------------
        | RL 3.1 - Indikator Pelayanan
        |--------------------------------------------------------------------------
        */

        // Jhon | 2026-11-25 19:53 WITA | UBAH: RL3.1 memakai API laporan baru, URL lama tetap tersedia.
        Route::get(
            '/rl-3-1',
            [\App\Http\Controllers\SirsReportController::class, 'show']
        )->defaults('report', 'rl3-1')->name('rl3-1');


        // Jhon | 2026-11-25 19:51 WITA | TAMBAH: Route laporan sesuai katalog menu SIRS.
        Route::get('/rl-3-2', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-2')->name('rl3-2');
        Route::get('/rl-3-3', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-3')->name('rl3-3');
        Route::get('/rl-3-4', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-4')->name('rl3-4');
        Route::get('/rl-3-5', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-5')->name('rl3-5');
        Route::get('/rl-3-6', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-6')->name('rl3-6');
        Route::get('/rl-3-7', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-7')->name('rl3-7');
        Route::get('/rl-3-8', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-8')->name('rl3-8');
        Route::get('/rl-3-9', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-9')->name('rl3-9');
        Route::get('/rl-3-10', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-10')->name('rl3-10');
        Route::get('/rl-3-12', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-12')->name('rl3-12');
        Route::get('/rl-3-14', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-14')->name('rl3-14');
        Route::get('/rl-4-1', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl4-1')->name('rl4-1');
        Route::get('/rl-4-2', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl4-2')->name('rl4-2');
        Route::get('/rl-4-3', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl4-3')->name('rl4-3');
        Route::get('/rl-5-1', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl5-1')->name('rl5-1');
        Route::get('/rl-5-2', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl5-2')->name('rl5-2');
        Route::get('/rl-5-3', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl5-3')->name('rl5-3');
        Route::get('/rl-3-11', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-11')->name('rl3-11');
        Route::get('/rl-3-13', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-13')->name('rl3-13');
        Route::get('/rl-3-15', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-15')->name('rl3-15');
        Route::get('/rl-3-16', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-16')->name('rl3-16');
        Route::get('/rl-3-17', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-17')->name('rl3-17');
        Route::get('/rl-3-18', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-18')->name('rl3-18');
        Route::get('/rl-3-19', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-19')->name('rl3-19');
        Route::get('/rl-3-11-detail', [\App\Http\Controllers\SirsReportController::class, 'show'])->defaults('report', 'rl3-11-detail')->name('rl3-11-detail');
    });
Route::prefix('bpjs-monitoring')
    ->name('bpjs-monitoring.')
    ->group(function () {
        Route::get(
            '/',
            [BpjsBridgeMonitoringController::class, 'index']
        )->name('index');

        Route::post(
            '/check-now',
            [BpjsBridgeMonitoringController::class, 'checkNow']
        )->name('check-now');
    });
    
Route::get(
    '/registrasi-pasien',
    [RegistrasiPasienController::class, 'index']
)->name('registrasi-pasien.index');
Route::post(
    '/registrasi-pasien/check-sep',
    [RegistrasiPasienController::class, 'checkSep']
)->name('registrasi-pasien.check-sep');

Route::get(
    '/bpjs-antrean/cek-jadwal-dokter',
    [JadwalDokterController::class, 'index']
)->name('bpjs-antrean.cek-jadwal-dokter');

Route::get(
    '/bpjs-antrean/cek-jadwal-dokter/dokter-search',
    [JadwalDokterController::class, 'ajaxDokter']
)->name('bpjs-antrean.cek-jadwal-dokter.ajax-dokter');
Route::get('/', [BpjsAntreanMonitorController::class, 'menu'])->name('menu');
Route::get('/tes-api-simrs-kontrol', function (SimrsRiwayatKontrolService $simrs) {
    return response()->json(
        $simrs->getRiwayatKontrol('2026-06-25', '2026-06-25', 'tglreservasi')
    );
});
Route::get('/', function () {
    return view('menu');
})->name('menu');

Route::get('/satusehat', function () {
    return view('satusehat.menu');
})->name('satusehat.menu');

Route::get('/sirs-online', function () {
    return view('sirs.menu');
})->name('sirs.menu');

Route::prefix('bpjs-antrean')->name('bpjs-antrean.')->group(function () {
    Route::get('/', [BpjsAntreanMonitorController::class, 'menu'])->name('menu');

    Route::get('/dashboard', [BpjsAntreanMonitorController::class, 'dashboard'])->name('dashboard');
    Route::get('/aktif', [BpjsAntreanMonitorController::class, 'aktif'])->name('aktif');
    Route::get('/surat-kontrol', [BpjsAntreanMonitorController::class, 'suratKontrol'])->name('surat-kontrol');

    Route::get('/cek-surat-kontrol', [BpjsAntreanMonitorController::class, 'cekSuratKontrol'])
        ->name('cek-surat-kontrol');

    Route::get('/cek-surat-kontrol/ajax/no-kartu', [BpjsAntreanMonitorController::class, 'ajaxCekSuratKontrolByNoKartu'])
        ->name('cek-surat-kontrol.ajax.no-kartu');

    Route::get('/cek-surat-kontrol/ajax/no-surat', [BpjsAntreanMonitorController::class, 'ajaxCekSuratKontrolByNoSurat'])
        ->name('cek-surat-kontrol.ajax.no-surat');

    Route::get('/detail-kodebooking', [BpjsAntreanMonitorController::class, 'detailForm'])->name('detail-form');
    Route::get('/kodebooking/{kodebooking}', [BpjsAntreanMonitorController::class, 'detail'])->name('detail');

    Route::get('/log', [BpjsAntreanMonitorController::class, 'logs'])->name('logs');
    Route::get('/selisih', [BpjsAntreanMonitorController::class, 'selisih'])->name('selisih');
    Route::get('/dashboard-waktu-tunggu', [BpjsAntreanMonitorController::class, 'dashboardWaktuTunggu'])
        ->name('dashboard-waktu-tunggu');
    Route::get('/batal-antrean', [BpjsAntreanMonitorController::class, 'batalForm'])
        ->name('batal-form');

    Route::post('/batal-antrean', [BpjsAntreanMonitorController::class, 'batalAntreanManual'])
        ->name('batal-manual');
    Route::get('/batal-jadwal', [BpjsAntreanMonitorController::class, 'batalByJadwalForm'])
        ->name('batal-jadwal.form');

    Route::post('/batal-jadwal', [BpjsAntreanMonitorController::class, 'batalByJadwalSubmit'])
        ->name('batal-jadwal.submit');
    Route::get('/ajax/poli', [BpjsAntreanMonitorController::class, 'ajaxPoliBpjs'])
        ->name('ajax-poli');
    Route::get('/ajax/dokter', [BpjsAntreanMonitorController::class, 'ajaxDokterBpjs'])
        ->name('ajax-dokter');
    Route::get('/batal-by-tanggal-dokter-poli', [
        BpjsAntreanMonitorController::class,
        'batalByTanggalDokterPoli'
    ])->name('batal-by-tanggal-dokter-poli');
    Route::get('batal-jadwal', [
        BpjsAntreanMonitorController::class,
        'formBatalJadwal'
    ])->name('batal-jadwal.form');

    Route::get('batal-by-tanggal-dokter-poli', [
        BpjsAntreanMonitorController::class,
        'batalByTanggalDokterPoli'
    ])->name('batal-by-tanggal-dokter-poli');

    Route::post('batal-jadwal/submit', [
        BpjsAntreanMonitorController::class,
        'submitBatalJadwal'
    ])->name('batal-jadwal.submit');
    Route::get('/cek-riwayat-kontrol', [
        BpjsAntreanMonitorController::class,
        'cekRiwayatKontrolBpjs'
    ])->name('cek-riwayat-kontrol');
});

// Jhon | 2026-11-23 20:45 WITA | TAMBAH: route login publik dibatasi percobaan, logout hanya POST.
Route::get('/login', [\App\Http\Controllers\MonitoringAuthController::class, 'form'])->name('login');
Route::post('/login', [\App\Http\Controllers\MonitoringAuthController::class, 'login'])->middleware('throttle:5,1')->name('monitoring.login');
Route::post('/logout', [\App\Http\Controllers\MonitoringAuthController::class, 'logout'])->name('monitoring.logout');

// Jhon | 2026-11-23 20:48 WITA | TAMBAH: aktivitas terproteksi sesi dan CSRF.
Route::post('/session/activity', [\App\Http\Controllers\MonitoringAuthController::class, 'activity'])->name('monitoring.activity');

// Jhon | 2026-11-24 12:57 WITA | TAMBAH: endpoint baca antrean dari klik status, mengikuti middleware sesi web.
Route::get('/bpjs-antrean/riwayat-antrean-detail', [BpjsAntreanMonitorController::class, 'riwayatAntreanDetail'])->middleware('throttle:30,1')->name('bpjs-antrean.riwayat-antrean-detail');

// Jhon | 2026-11-24 14:04 WITA | TAMBAH: signed lookup diteruskan server, kredensial tidak dikirim ke browser.
Route::post('/bpjs-antrean/riwayat-antrean-log', [BpjsAntreanMonitorController::class, 'riwayatAntreanLog'])->middleware('throttle:30,1')->name('bpjs-antrean.riwayat-antrean-log');

// Jhon | 2026-11-24 14:15 WITA | TAMBAH: pencarian log surat kontrol baca-saja melalui POST.
Route::post('/bpjs-antrean/surat-kontrol-log', [BpjsAntreanMonitorController::class, 'suratKontrolLog'])->middleware('throttle:30,1')->name('bpjs-antrean.surat-kontrol-log');

// Jhon | 2026-11-27 10:33 WITA | TAMBAH: detail SEP baca-saja melalui POST, tetap memakai sesi, CSRF, dan throttle.
Route::post('/bpjs-antrean/surat-kontrol-sep', [BpjsAntreanMonitorController::class, 'suratKontrolSep'])->middleware('throttle:30,1')->name('bpjs-antrean.surat-kontrol-sep');

// Jhon | 2026-11-27 10:50 WITA | TAMBAH: lookup rujukan tetap terproteksi sesi/CSRF dan throttle.
Route::post('/bpjs-antrean/surat-kontrol-rujukan', [BpjsAntreanMonitorController::class, 'suratKontrolRujukan'])->middleware('throttle:30,1')->name('bpjs-antrean.surat-kontrol-rujukan');

// Jhon | 2026-11-27 11:06 WITA | TAMBAH: simpan verifikasi melalui middleware web sesi/CSRF.
Route::post('/bpjs-antrean/surat-kontrol-verifikasi', [\App\Http\Controllers\SuratKontrolVerificationController::class, 'save'])->middleware('throttle:30,1')->name('bpjs-antrean.surat-kontrol-verifikasi');
