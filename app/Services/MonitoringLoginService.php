<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
// Jhon | 2026-11-23 20:45 WITA | TAMBAH: autentikasi SIMRS server-side; jangan simpan password, cookie, atau respons lengkap.
class MonitoringLoginService
{
    public function authenticate(string $username, string $password): ?array
    {
        $response = Http::withOptions(['connect_timeout' => 5, 'allow_redirects' => false])
            ->timeout(15)->acceptJson()->post(config('services.monitoring_auth.url'), [
                'namaUser' => $username, 'kataSandi' => $password, 'token' => '', 'tokenCapcay' => null,
            ]);
        if (!$response->successful()) return null;
        $json = $response->json();
        if (!is_array($json) || (string) data_get($json, 'metaData.code') !== '200'
            || !is_string(data_get($json, 'response.token')) || trim(data_get($json, 'response.token')) === ''
            || !data_get($json, 'response.data.id')) return null;
        $expires = now()->addHours(8)->timestamp;
        $upstreamExpiry = data_get($json, 'response.expired');
        if ($upstreamExpiry) {
            try {
                $upstreamExpiry = is_numeric($upstreamExpiry) ? (int) $upstreamExpiry : Carbon::parse($upstreamExpiry)->timestamp;
                $expires = min($expires, $upstreamExpiry);
            } catch (\Throwable $e) { return null; }
        }
        if ($expires <= time()) return null;
        return ['id' => data_get($json, 'response.data.id'),
            'name' => (string) data_get($json, 'response.data.namaUser', $username), 'expires_at' => $expires];
    }
}
