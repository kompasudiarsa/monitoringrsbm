<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
// Jhon | 2026-11-25 19:51 WITA | TAMBAH: Transport GET RL bertanda tangan; query canonical sama dengan URL aktual.
class SirsReportClient
{
    public function fetch(string $report, array $query): array
    {
        // Jhon | 2026-11-25 21:03 WITA | UBAH: laporan menuju vhost API-SIMRS, bukan loopback aplikasi monitoring.
        $base = rtrim((string) config('services.api_simrs.reports_base_url', config('services.api_simrs.base_url')), '/');
        $token = (string) config('services.api_simrs.token');
        $secret = (string) config('services.api_simrs.secret');
        if (!$base || !$token || !$secret) throw new \DomainException('Konfigurasi API-SIMRS belum lengkap.');
        // Jhon | 2026-11-25 21:01 WITA | UBAH: normalisasi prefix API dan nonce mencegah replay pada klik ulang dalam detik sama.
        $parts = parse_url($base);
        if (!is_array($parts) || !in_array($parts['scheme'] ?? '', ['http','https'], true)
            || empty($parts['host']) || isset($parts['query']) || isset($parts['fragment']) || isset($parts['user']) || isset($parts['pass'])) {
            throw new \DomainException('Alamat API_BASE_URL tidak valid; gunakan URL server tanpa query atau kredensial.');
        }
        $base = preg_replace('~/(?:api/service|api|service)$~i', '', $base);
        $query['_request_id'] = bin2hex(random_bytes(12));
        ksort($query);
        $url = $base.'/api/service/sirs/rl/'.$report.'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        $uri = parse_url($url, PHP_URL_PATH).'?'.parse_url($url, PHP_URL_QUERY);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', implode("\n", ['GET', $uri, $timestamp, hash('sha256', '')]), $secret);
        try {
            $response = Http::withOptions(['connect_timeout'=>5, 'allow_redirects'=>false])->timeout(60)
                ->withHeaders(['Accept'=>'application/json','X-Token'=>$token,'X-Timestamp'=>$timestamp,'X-Signature'=>$signature])->get($url);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \DomainException('Koneksi API-SIMRS gagal atau melewati batas waktu. Silakan coba kembali.');
        }
        if (in_array($response->status(), [401,403], true)) throw new \DomainException('Autentikasi API-SIMRS ditolak. Periksa konfigurasi signature dan waktu server.');
        if ($response->status() === 409) throw new \DomainException('API menolak request duplikat. Muat ulang laporan untuk membuat request baru.');
        if (in_array($response->status(), [301,302,303,307,308], true)) throw new \DomainException('Alamat API mengalihkan request. Gunakan alamat API langsung, bukan halaman login atau URL redirect.');
        if ($response->status() === 422) throw new \DomainException('API menolak parameter laporan (HTTP 422). Pastikan versi API laporan dan monitoring sama.');
        if ($response->status() === 404) throw new \DomainException('API laporan belum tersedia pada server tujuan.');
        if ($response->status() === 429) throw new \DomainException('Batas permintaan tercapai. Tunggu sebentar lalu coba kembali.');
        // Jhon | 2026-11-25 21:04 WITA | UBAH: tampilkan kategori kegagalan aman dari API, bukan exception/SQL mentah.
        if ($response->status() === 503) {
            $details = $response->json();
            $code = is_array($details) ? ($details['error_code'] ?? '') : '';
            $messages = [
                'RL_SETTING_MISSING'=>'Setting RL profil 1 belum tersedia',
                'RL_TABLE_MISSING'=>'Tabel laporan tidak ditemukan pada database tujuan',
                'RL_COLUMN_MISSING'=>'Kolom laporan tidak sesuai dengan database tujuan',
                'RL_VALUE_INVALID'=>'Nilai setting atau data tidak sesuai tipe kolom laporan',
                'RL_QUERY_TYPE'=>'Tipe data atau fungsi query laporan tidak sesuai',
                'RL_DB_CONNECTION'=>'Koneksi database API-SIMRS gagal',
                'RL_DB_AUTH'=>'Autentikasi database API-SIMRS ditolak',
                'RL_DB_DRIVER'=>'Driver PostgreSQL pada PHP API-SIMRS belum tersedia',
                // Jhon | 2026-11-25 21:06 WITA | TAMBAH: diagnosis query tambahan tanpa pesan exception mentah.
                'RL_AMBIGUOUS_COLUMN'=>'Referensi kolom query laporan ambigu',
                'RL_GROUPING_ERROR'=>'Pengelompokan query laporan tidak sesuai',
                'RL_DB_PERMISSION'=>'Akun database tidak memiliki izin membaca tabel laporan',
                'RL_DATABASE_MISSING'=>'Database tujuan API-SIMRS tidak ditemukan',
                'RL_QUERY_FAILED'=>'Query laporan gagal pada database API-SIMRS',
                'RL_CALCULATION_FAILED'=>'Proses perhitungan laporan gagal pada API-SIMRS',
                // Jhon | 2026-11-26 12:58 WITA | TAMBAH: mapping ambigu dibedakan dari runtime/query gagal.
                'RL_MAPPING_AMBIGUOUS'=>'Satu ruangan memiliki lebih dari satu mapping layanan khusus RL3.2; mapping harus diselesaikan tanpa memilih layanan secara acak',
            ];
            $message = $messages[$code] ?? 'API terhubung, tetapi perhitungan laporan gagal';
            if ($code === 'RL_SETTING_MISSING' && is_string($details['setting'] ?? null)
                && preg_match('/^[A-Za-z0-9_]{1,100}$/', $details['setting'])) $message .= ': '.$details['setting'];
            // Jhon | 2026-11-26 13:00 WITA | TAMBAH: tampilkan ruangan dan pilihan mapping konflik; Blade tetap melakukan escaping.
            if ($code === 'RL_MAPPING_AMBIGUOUS' && is_array($details['mapping_conflict'] ?? null)) {
                $conflict = $details['mapping_conflict'];
                $name = is_string($conflict['name'] ?? null) ? mb_substr($conflict['name'],0,120) : '';
                $services = array_slice(array_filter($conflict['services'] ?? [], 'is_string'),0,20);
                $message .= '. Ruangan: '.$name.' (ID '.(int)($conflict['id'] ?? 0).'); layanan: '.implode(', ', $services);
            }
            $state = is_array($details) ? ($details['sqlstate'] ?? null) : null;
            if (is_string($state) && preg_match('/^[A-Z0-9]{5}$/', $state)) $message .= ' [SQLSTATE '.$state.']';
            // Jhon | 2026-11-25 21:07 WITA | TAMBAH: penanda versi membedakan cache/kode lama dengan diagnosis aktif.
            $reasonLabels = ['binding-array'=>'parameter array pada query','binding-object'=>'parameter objek pada query',
                'driver-missing'=>'driver database tidak tersedia','db-password'=>'autentikasi database gagal',
                'db-access'=>'akses database ditolak','db-connection'=>'koneksi database gagal'];
            if (isset($reasonLabels[$details['reason'] ?? ''])) $message .= ' — '.$reasonLabels[$details['reason']];
            $version = $details['diagnostic_version'] ?? '';
            $revision = in_array($version,['RL-D3','RL-D4'],true) ? 'API '.substr($version,3) : 'API tidak menyertakan penanda diagnosis';
            $origin = $details['origin'] ?? '';
            if (is_string($origin) && preg_match('/^RL32:[A-Za-z0-9_]{1,80}$/',$origin)) $revision .= '; '.$origin;
            $type = $details['exception_type'] ?? '';
            if (is_string($type) && preg_match('/^[A-Za-z]{1,50}$/',$type)) $revision .= '; '.$type;
            $message .= ' [Client D4; '.$revision.']';
            throw new \DomainException($message.' (HTTP 503'.(isset($messages[$code]) ? '; '.$code : '').').');
        }
        if (!$response->successful()) throw new \DomainException('API laporan gagal memproses permintaan (HTTP '.$response->status().').');
        $json = $response->json();
        if (!is_array($json) || !array_key_exists('response', $json) || (int) data_get($json,'metaData.code') !== 200 || !is_array($json['response'])) {
            throw new \DomainException('Format hasil API laporan tidak sesuai.');
        }
        return $json['response'];
    }
}
