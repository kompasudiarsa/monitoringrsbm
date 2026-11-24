<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SimrsJadwalDokterService
{
    /**
     * Cari dokter melalui API dokter-paging.
     *
     * Respons dokter tersedia pada response.dokter dan sudah
     * mengandung id, namalengkap, serta kddokterbpjs.
     */
    public function searchDokter(
        string $nama = '',
        int $limit = 30
    ): array {
        return $this->getJson(
            '/registrasi/dokter-paging',
            [
                'name' => trim($nama),
                'limit' => max(
                    1,
                    min($limit, 1000)
                ),
            ],
            'daftar dokter SIMRS'
        );
    }

    /**
     * Ambil jadwal dokter SIMRS berdasarkan tanggal, ID pegawai,
     * dan ID ruangan hasil pencarian poli jika tersedia.
     */
    public function getJadwal(
        string $tanggal,
        int $dokterId,
        ?int $ruanganId = null
    ): array {
        if ($dokterId <= 0) {
            throw new RuntimeException(
                'ID dokter SIMRS tidak valid.'
            );
        }

        $tanggal = Carbon::parse(
            $tanggal
        )->format('Y-m-d');

        return $this->getJson(
            '/sysadmin/master-jadwal-dokter',
            [
                'statusenabled' => 'true',
                'objectpegawaifk' => $dokterId,
                'objectruanganfk' =>
                    $ruanganId ?? '',
                'tglAwal' => $tanggal,
                'tglAkhir' => $tanggal,
            ],
            'jadwal dokter SIMRS'
        );
    }

    private function getJson(
        string $path,
        array $query,
        string $namaProses
    ): array {
        $response = $this->client()->get(
            $this->url($path),
            $query
        );

        return $this->validasiResponse(
            $response,
            $namaProses
        );
    }

    private function validasiResponse(
        Response $response,
        string $namaProses
    ): array {
        if ($response->failed()) {
            throw new RuntimeException(
                'Gagal mengambil '
                    . $namaProses
                    . '. HTTP '
                    . $response->status()
                    . '.'
            );
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException(
                'Respons API '
                    . $namaProses
                    . ' tidak valid.'
            );
        }

        $metaCode = data_get(
            $payload,
            'metaData.code'
        );

        if (
            $metaCode !== null
            && (int) $metaCode !== 200
        ) {
            throw new RuntimeException(
                (string) data_get(
                    $payload,
                    'metaData.message',
                    'API '
                        . $namaProses
                        . ' mengembalikan kegagalan.'
                )
            );
        }

        return $payload;
    }

    private function client(): PendingRequest
    {
        $token = trim(
            (string) config(
                'services.simrs.token'
            )
        );

        if ($token === '') {
            throw new RuntimeException(
                'Konfigurasi SIMRS_TOKEN belum diisi.'
            );
        }

        return Http::acceptJson()
            ->withHeaders([
                'token' => $token,
            ])
            ->timeout(60)
            ->retry(2, 500);
    }

    private function url(string $path): string
    {
        $baseUrl = rtrim(
            (string) config(
                'services.simrs.base_url'
            ),
            '/'
        );

        if ($baseUrl === '') {
            throw new RuntimeException(
                'Konfigurasi SIMRS_URL belum diisi.'
            );
        }

        $path = '/' . ltrim($path, '/');

        /*
         * Mendukung:
         * SIMRS_URL=https://app.balimandarahospital.com
         * SIMRS_URL=https://app.balimandarahospital.com/service
         */
        if (preg_match(
            '#/service$#i',
            $baseUrl
        )) {
            return $baseUrl . $path;
        }

        return $baseUrl
            . '/service'
            . $path;
    }
}