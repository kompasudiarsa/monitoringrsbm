<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class SirsOnlineController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Menu
    |--------------------------------------------------------------------------
    */

    public function menu()
    {
        return view('sirs.menu');
    }


    /*
    |--------------------------------------------------------------------------
    | RL 3.1
    |--------------------------------------------------------------------------
    */

public function rl31(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Jenis Periode
    |--------------------------------------------------------------------------
    |
    | Default = tanggal
    | Default tanggal = hari ini
    |
    */

    $jenisPeriode = $request->get(
        'jenis_periode',
        'tanggal'
    );

    if (!in_array(
        $jenisPeriode,
        ['tanggal', 'bulan'],
        true
    )) {
        $jenisPeriode = 'tanggal';
    }


    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    */

    $periode = null;

    $tglAwal = now()->format('Y-m-d');
    $tglAkhir = now()->format('Y-m-d');


    /*
    |--------------------------------------------------------------------------
    | Filter Berdasarkan Bulan
    |--------------------------------------------------------------------------
    */

    if ($jenisPeriode === 'bulan') {

        $periode = $request->get(
            'periode',
            now()->format('Y-m')
        );

        try {

            $bulan = Carbon::createFromFormat(
                'Y-m',
                $periode
            );

            $tglAwal = $bulan
                ->copy()
                ->startOfMonth()
                ->format('Y-m-d');

            $tglAkhir = $bulan
                ->copy()
                ->endOfMonth()
                ->format('Y-m-d');

        } catch (\Throwable $e) {

            $periode = now()->format('Y-m');

            $tglAwal = now()
                ->startOfMonth()
                ->format('Y-m-d');

            $tglAkhir = now()
                ->endOfMonth()
                ->format('Y-m-d');
        }

    } else {

        /*
        |--------------------------------------------------------------------------
        | Filter Berdasarkan Tanggal
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'tgl_awal' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'tgl_akhir' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:tgl_awal',
            ],
        ]);


        $tglAwal = $request->get(
            'tgl_awal',
            now()->format('Y-m-d')
        );

        $tglAkhir = $request->get(
            'tgl_akhir',
            $tglAwal
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Request API
    |--------------------------------------------------------------------------
    */

    $result = $this->signedGet(
        '/api/service/getborlostoi',
        [
            'tglAwal' => $tglAwal,
            'tglAkhir' => $tglAkhir,
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | Data API
    |--------------------------------------------------------------------------
    |
    | Format:
    |
    | data => [
    |     [
    |         ...
    |     ]
    | ]
    |
    */

    $data = $result['success']
        ? data_get(
            $result,
            'data.0'
        )
        : null;


    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    return view(
        'sirs.rl-3-1',
        [
            'jenisPeriode' =>
                $jenisPeriode,

            'periode' =>
                $periode,

            'tglAwal' =>
                $tglAwal,

            'tglAkhir' =>
                $tglAkhir,

            'data' =>
                $data,

            'error' =>
                $result['success']
                    ? null
                    : $result['message'],

            'apiStatus' =>
                $result['status'],
        ]
    );
}


    /*
    |--------------------------------------------------------------------------
    | Generic Signed GET
    |--------------------------------------------------------------------------
    |
    | Fungsi ini dipakai oleh SEMUA endpoint SIRS.
    |
    | Contoh:
    |
    | $this->signedGet(
    |     '/api/service/getborlostoi',
    |     [...]
    | );
    |
    | $this->signedGet(
    |     '/api/service/getrawatinap',
    |     [...]
    | );
    |
    */

   private function signedGet(
    string $endpoint,
    array $query = []
): array {

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi API
    |--------------------------------------------------------------------------
    */

    $baseUrl = rtrim(
        (string) config('services.api_simrs.base_url'),
        '/'
    );

    $token = trim(
        (string) config('services.api_simrs.token')
    );

    $secret = trim(
        (string) config('services.api_simrs.secret')
    );

    $timeout = (int) config(
        'services.api_simrs.timeout',
        30
    );


    /*
    |--------------------------------------------------------------------------
    | Validasi konfigurasi
    |--------------------------------------------------------------------------
    */

    if (
        $baseUrl === '' ||
        $token === '' ||
        $secret === ''
    ) {
        return [
            'success' => false,
            'status' => 500,
            'message' =>
                'Konfigurasi API belum lengkap.',
            'data' => null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    */

    if (
        substr($endpoint, 0, 1) !== '/'
    ) {
        $endpoint = '/' . $endpoint;
    }


    /*
    |--------------------------------------------------------------------------
    | Query Parameter
    |--------------------------------------------------------------------------
    */

    ksort($query);

    $queryString = http_build_query(
        $query,
        '',
        '&',
        PHP_QUERY_RFC3986
    );


    /*
    |--------------------------------------------------------------------------
    | Canonical URI
    |--------------------------------------------------------------------------
    */

    $canonicalUri = $endpoint;

    if ($queryString !== '') {
        $canonicalUri .=
            '?' .
            $queryString;
    }


    /*
    |--------------------------------------------------------------------------
    | Signature
    |--------------------------------------------------------------------------
    */

    $timestamp = (string) time();

    /*
     * Request GET tidak memiliki body.
     */
    $body = '';

    $bodyHash = hash(
        'sha256',
        $body
    );

    $payload = implode(
        "\n",
        [
            'GET',
            $canonicalUri,
            $timestamp,
            $bodyHash,
        ]
    );

    $signature = hash_hmac(
        'sha256',
        $payload,
        $secret
    );


    /*
    |--------------------------------------------------------------------------
    | Request API
    |--------------------------------------------------------------------------
    */

    try {

        $response = \Illuminate\Support\Facades\Http::timeout(
            $timeout
        )
            ->acceptJson()
            ->withHeaders([
                'X-Token' =>
                    $token,

                'X-Timestamp' =>
                    $timestamp,

                'X-Signature' =>
                    $signature,
            ])
            ->get(
                $baseUrl .
                $canonicalUri
            );


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $json = $response->json();


        if (! $response->successful()) {

            $message = data_get(
                $json,
                'metadata.message'
            );

            if (!$message) {
                $message = data_get(
                    $json,
                    'metaData.message'
                );
            }

            return [
                'success' => false,

                'status' =>
                    $response->status(),

                'message' =>
                    $message
                    ?: 'API gagal diakses.',

                'data' =>
                    $json,
            ];
        }


        return [
            'success' => true,

            'status' =>
                $response->status(),

            'message' =>
                data_get(
                    $json,
                    'metaData.message',
                    'OK'
                ),

            'data' =>
                $json,
        ];

    } catch (\Throwable $e) {

        return [
            'success' => false,

            'status' => 500,

            'message' =>
                'Gagal terhubung ke API: ' .
                $e->getMessage(),

            'data' => null,
        ];
    }
}
}