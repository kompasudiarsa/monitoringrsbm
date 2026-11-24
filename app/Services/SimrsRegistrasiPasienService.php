<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SimrsRegistrasiPasienService
{
    public function getListPasien(
        string $dari,
        string $sampai,
        int $page = 1,
        int $limit = 25,
        ?int $ruanganFk = null
    ): array {
        $baseUrl = rtrim(
            (string) config('services.simrs.base_url'),
            '/'
        );

        $token = (string) config('services.simrs.token');

        if ($baseUrl === '') {
            throw new RuntimeException(
                'SIMRS_URL belum diatur pada file .env.'
            );
        }

        if ($token === '') {
            throw new RuntimeException(
                'SIMRS_TOKEN belum diatur pada file .env.'
            );
        }

        $query = [
            'page' => max(1, $page),
            'dari' => $dari,
            'sampai' => $sampai,
            'limit' => $limit,
            'pasien_aktif' => 'true',
        ];

        if (!empty($ruanganFk)) {
            $query['ruanganfk'] = $ruanganFk;
        }

        $response = Http::acceptJson()
            ->withHeaders([
                'token' => $token,
            ])
            ->timeout(90)
            ->get(
                $baseUrl . '/service/registrasi/list-pasien-grid',
                $query
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'API SIMRS gagal diakses. HTTP status: ' .
                $response->status()
            );
        }

        $result = $response->json();

        $metadataCode = (int) data_get(
            $result,
            'metaData.code',
            0
        );

        $metadataMessage = data_get(
            $result,
            'metaData.message',
            'Respons API SIMRS tidak valid.'
        );

        if ($metadataCode !== 200) {
            throw new RuntimeException(
                'API SIMRS: ' . $metadataMessage
            );
        }

        return data_get(
            $result,
            'response.data',
            []
        );
    }
}