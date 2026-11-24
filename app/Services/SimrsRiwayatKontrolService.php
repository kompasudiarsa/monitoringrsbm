<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SimrsRiwayatKontrolService
{
    public function getRiwayatKontrol(
    string $dari,
    string $sampai,
    string $kategori = 'tglreservasi',
    string $isAllPeriode = 'undefined'
): array {
    $url = config('services.simrs_riwayat_kontrol.url');
    $token = config('services.simrs_riwayat_kontrol.token');
    $cookie = config('services.simrs_riwayat_kontrol.cookie');

    try {
        $headers = [
            'Accept' => 'application/json',
        ];

        if (!empty($token)) {
            $headers['Token'] = $token;
        }

        if (!empty($cookie)) {
            $headers['Cookie'] = $cookie;
        }

        $response = Http::timeout(60)
            ->withHeaders($headers)
            ->get($url, [
                'dari' => $dari,
                'sampai' => $sampai,
                'kategori' => $kategori,
                'isAllPeriode' => $isAllPeriode,
            ]);

        $json = $response->json();

        if (!is_array($json)) {
            return [
                'metaData' => [
                    'code' => $response->status(),
                    'message' => 'Response SIMRS bukan JSON valid',
                ],
                'response' => [
                    'riwayat' => [],
                ],
                'raw' => $response->body(),
            ];
        }

        return $json;
    } catch (\Throwable $e) {
        return [
            'metaData' => [
                'code' => 500,
                'message' => $e->getMessage(),
            ],
            'response' => [
                'riwayat' => [],
            ],
        ];
    }
}
}