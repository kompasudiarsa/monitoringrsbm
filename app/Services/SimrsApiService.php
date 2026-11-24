<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SimrsApiService
{
    protected string $baseUrl;
    protected ?string $token;
    protected ?string $cookie;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.simrs_service.url'), '/');
        $this->token = config('services.simrs_service.token');
        $this->cookie = config('services.simrs_service.cookie');
    }




    public function listRuanganRawatJalan(string $query = ''): array
    {
        try {
            $headers = [
                'Accept' => 'application/json',
            ];

            if (!empty($this->token)) {
                $headers['token'] = $this->token;
            }

            if (!empty($this->cookie)) {
                $headers['Cookie'] = $this->cookie;
            }

            $response = Http::timeout(25)
                ->retry(2, 500)
                ->withHeaders($headers)
                ->get($this->baseUrl . '/service/registrasi/list-ruangan-rawat-jalan', [
                    'query' => $query,
                ]);

            $json = $response->json();

            if (!is_array($json)) {
                return [
                    'metaData' => [
                        'code' => $response->status(),
                        'message' => 'Response SIMRS bukan JSON valid',
                    ],
                    'response' => [
                        'ruangan_RJ' => [],
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
                    'ruangan_RJ' => [],
                ],
            ];
        }
    }
    public function poliBpjsOptions(string $query = ''): array
    {
        $response = $this->listRuanganRawatJalan($query);

        $list = data_get($response, 'response.ruangan_RJ', []);

        if (!is_array($list)) {
            $list = [];
        }

        return collect($list)
            ->filter(function ($item) {
                return is_array($item)
                    && data_get($item, 'statusenabled') === true;
            })
            ->map(function ($item) {
                $kodeSpesialisBpjs = data_get($item, 'kdspesialisbpjs')
                    ?: data_get($item, 'kdsubspesialisbpjs');

                return [
                    'id' => data_get($item, 'id'),
                    'nama' => data_get($item, 'namaruangan')
                        ?: data_get($item, 'nama')
                        ?: '-',

                    // ini yang dikirim ke BPJS
                    'kodepoli' => $kodeSpesialisBpjs,

                    'kdruangan' => data_get($item, 'kdruangan'),
                    'namaexternal' => data_get($item, 'namaexternal'),
                    'kdspesialisbpjs' => data_get($item, 'kdspesialisbpjs'),
                    'kdsubspesialisbpjs' => data_get($item, 'kdsubspesialisbpjs'),
                ];
            })
            ->sortBy('nama')
            ->values()
            ->toArray();
    }
    public function listDokterPaging(string $name = '', int $limit = 1000): array
    {
        try {
            $headers = [
                'Accept' => 'application/json',
            ];

            if (!empty($this->token)) {
                $headers['token'] = $this->token;
            }

            if (!empty($this->cookie)) {
                $headers['Cookie'] = $this->cookie;
            }

            $response = Http::timeout(25)
                ->retry(2, 500)
                ->withHeaders($headers)
                ->get($this->baseUrl . '/service/registrasi/dokter-paging', [
                    'name' => $name,
                    'limit' => $limit,
                ]);

            $json = $response->json();

            if (!is_array($json)) {
                return [
                    'metaData' => [
                        'code' => $response->status(),
                        'message' => 'Response SIMRS dokter bukan JSON valid',
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
            ];
        }
    }
    public function dokterBpjsOptions(string $query = ''): array
    {
        $response = $this->listDokterPaging($query, 1000);

        $items = [];

        $walk = function ($node) use (&$walk, &$items) {
            if (!is_array($node)) {
                return;
            }

            // Ambil array yang terlihat seperti data dokter
            if (
                array_key_exists('namalengkap', $node)
                || array_key_exists('namadokter', $node)
                || array_key_exists('namaDokter', $node)
                || array_key_exists('nama', $node)
                || array_key_exists('kddokterbpjs', $node)
                || array_key_exists('kodebpjs', $node)
            ) {
                $items[] = $node;
            }

            foreach ($node as $child) {
                if (is_array($child)) {
                    $walk($child);
                }
            }
        };

        $walk($response);

        return collect($items)
            ->filter(fn($item) => is_array($item))
            ->map(function ($item) {
                $kodeDokterBpjs =
                    data_get($item, 'kodedokterbpjs')
                    ?: data_get($item, 'kode_dokter_bpjs')
                    ?: data_get($item, 'kddokterbpjs')
                    ?: data_get($item, 'kd_dokter_bpjs')
                    ?: data_get($item, 'kodebpjs')
                    ?: data_get($item, 'kode_bpjs')
                    ?: data_get($item, 'kodebpjsdokter')
                    ?: data_get($item, 'kode_dokter')
                    ?: data_get($item, 'kddokter')
                    ?: data_get($item, 'idbpjs')
                    ?: data_get($item, 'id_bpjs');

                $namaDokter =
                    data_get($item, 'namalengkap')
                    ?: data_get($item, 'namaLengkap')
                    ?: data_get($item, 'namadokter')
                    ?: data_get($item, 'nama_dokter')
                    ?: data_get($item, 'namaDokter')
                    ?: data_get($item, 'nama')
                    ?: data_get($item, 'name');

                return [
                    'id' => data_get($item, 'id')
                        ?: data_get($item, 'idpegawai')
                        ?: data_get($item, 'objectpegawaifk'),

                    // yang ditampilkan
                    'nama' => $namaDokter ?: '-',

                    // yang dikirim ke BPJS
                    'kodedokter' => $kodeDokterBpjs,

                    // debug sementara
                    'kodeexternal' => data_get($item, 'kodeexternal'),
                    'kdinternal' => data_get($item, 'kdinternal'),
                    'raw' => $item,
                ];
            })
            ->filter(fn($item) => !empty($item['nama']) && $item['nama'] !== '-')
            ->unique(fn($item) => ($item['id'] ?? '') . '-' . $item['nama'])
            ->sortBy('nama')
            ->values()
            ->toArray();
    }
}
