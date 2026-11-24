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

        Route::get(
            '/rl-3-1',
            [SirsOnlineController::class, 'rl31']
        )->name('rl3-1');

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
