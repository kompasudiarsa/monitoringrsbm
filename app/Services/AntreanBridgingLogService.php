<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
// Jhon | 2026-11-24 14:04 WITA | TAMBAH: signed POST lookup log memakai konfigurasi API existing, body signature sama dengan bytes request.
class AntreanBridgingLogService
{
    // Jhon | 2026-11-24 14:15 WITA | UBAH: pakai transport signature yang sama untuk dua endpoint tetap.
    public function lookup(string $booking): array
    {
        return $this->sendLookup('antrean', 'kodebooking', $booking);
    }
    public function suratKontrol(string $sep): array
    {
        return $this->sendLookup('surat-kontrol', 'noSEP', $sep);
    }
    private function sendLookup(string $kind, string $field, string $value): array
    {
        $base = rtrim((string) config('services.api_simrs.base_url'), '/');
        $token = (string) config('services.api_simrs.token');
        $secret = (string) config('services.api_simrs.secret');
        // Jhon | 2026-11-24 14:06 WITA | UBAH: diagnosis hanya nama konfigurasi, tidak pernah nilai kredensial.
        if (!$base) throw new \DomainException('Alamat API log belum dikonfigurasi (API_BASE_URL).');
        if (!$token) throw new \DomainException('Token API log belum dikonfigurasi (API_TOKEN).');
        if (!$secret) throw new \DomainException('Secret penandatangan API log belum dikonfigurasi (API_SECRET).');
        $url = $base . '/api/service/bpjs-log/' . $kind;
        $uri = parse_url($url, PHP_URL_PATH);
        $body = json_encode([$field => $value, 'limit' => 20], JSON_UNESCAPED_SLASHES);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', implode("\n", ['POST', $uri, $timestamp, hash('sha256', $body)]), $secret);
        // Jhon | 2026-11-24 14:06 WITA | UBAH: bedakan kegagalan transport dari penolakan autentikasi.
        try {
        $response = Http::withOptions(['connect_timeout' => 5, 'allow_redirects' => false])->timeout(20)
            ->withHeaders(['Accept' => 'application/json', 'X-Token' => $token, 'X-Timestamp' => $timestamp, 'X-Signature' => $signature])
            ->withBody($body, 'application/json')->post($url);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \DomainException('Koneksi API log gagal/timeout. Periksa alamat API_BASE_URL dari server PHP; 127.0.0.1 menunjuk server PHP pemanggil.');
        }
        // Jhon | 2026-11-24 14:06 WITA | TAMBAH: status HTTP aman untuk diagnosis tanpa respons mentah upstream.
        if ($response->status() === 401 || $response->status() === 403) throw new \DomainException('API log menolak autentikasi (HTTP ' . $response->status() . '). Periksa pasangan API_TOKEN/API_SECRET dan waktu server.');
        if ($response->status() === 404) throw new \DomainException('Endpoint log tidak ditemukan (HTTP 404). Periksa API_BASE_URL dan path /api/service/bpjs-log/antrean.');
        if ($response->status() === 429) throw new \DomainException('Batas request API log tercapai. Tunggu sebentar lalu coba lagi.');
        if (!$response->successful()) throw new \DomainException('API log mengembalikan HTTP ' . $response->status() . '. Layanan belum dapat memproses pencarian.');
        $json = $response->json();
        if (!$response->successful() || !is_array($json) || !isset($json['data']) || !is_array($json['data'])) throw new \DomainException('Format respons API log tidak sesuai JSON data. Periksa alamat endpoint dan redirect login pada API.');
        $rows = [];
        foreach ($json['data'] as $row) {
            // Jhon | 2026-11-27 07:31 WITA | UBAH: teruskan nama penginput scalar; jangan parse nama sebagai JSON atau mengambil akun sesi.
            if (is_array($row)) $rows[] = ['user' => isset($row['user']) && is_string($row['user']) ? trim($row['user']) : null, 'data' => $this->clean($row['data'] ?? null), 'response' => $this->clean($row['response'] ?? null)];
        }
        return ['data' => $rows, 'truncated' => (bool) data_get($json, 'meta.truncated', false)];
    }
    private function clean($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            // Log malformed tidak ditampilkan mentah karena bisa memuat header atau rahasia yang gagal diparse.
            if (json_last_error() !== JSON_ERROR_NONE) return '[Isi bukan JSON valid; tidak ditampilkan]';
            return $this->clean($decoded);
        }
        if (!is_array($value)) return $value;
        foreach ($value as $key => $item) {
            if (preg_match('/token|secret|signature|password|sandi|cookie|authorization|header/i', (string) $key)) $value[$key] = '[disembunyikan]';
            elseif (is_array($item)) $value[$key] = $this->clean($item);
        }
        return $value;
    }
}
