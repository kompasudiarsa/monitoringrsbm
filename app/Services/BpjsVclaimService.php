<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use LZCompressor\LZString;
use Carbon\Carbon;

class BpjsVclaimService
{
    protected string $baseUrl;
    protected string $consId;
    protected string $secretKey;
    protected string $userKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.bpjs_vclaim.base_url'), '/');
        $this->consId = config('services.bpjs_vclaim.cons_id');
        $this->secretKey = config('services.bpjs_vclaim.secret_key');
        $this->userKey = config('services.bpjs_vclaim.user_key');
    }

    public function listSuratKontrolPerHari(string $tanggal, int $filter = 2): array
    {
        /*
            filter:
            1 = berdasarkan tanggal entri
            2 = berdasarkan tanggal rencana kontrol

            Untuk monitoring per hari kontrol pasien, gunakan filter 2.
        */

        return $this->get("/RencanaKontrol/ListRencanaKontrol/tglAwal/{$tanggal}/tglAkhir/{$tanggal}/filter/{$filter}");
    }

    protected function get(string $endpoint): array
    {
        return $this->send('GET', $endpoint);
    }

    protected function send(string $method, string $endpoint, array $payload = []): array
    {
        $timestamp = (string) time();
        $url = $this->baseUrl . $endpoint;
        $startedAt = microtime(true);

        try {
            $response = Http::timeout(25)
                ->retry(2, 500)
                ->withHeaders($this->headers($timestamp))
                ->get($url);

            $raw = $response->body();
            $json = $response->json();

            if (!is_array($json)) {
                $json = [
                    'metadata' => [
                        'code' => $response->status(),
                        'message' => 'Response BPJS VClaim bukan JSON valid',
                    ],
                    'response' => $raw,
                ];
            }

            $decoded = $this->decodeBpjsResponse($json, $timestamp);

            $this->writeLog(
                $method,
                $endpoint,
                $payload,
                $response->status(),
                $raw,
                $decoded,
                $startedAt
            );

            return $decoded;
        } catch (\Throwable $e) {
            $error = [
                'metadata' => [
                    'code' => 500,
                    'message' => $e->getMessage(),
                ],
                'response' => null,
            ];

            $this->writeLog(
                $method,
                $endpoint,
                $payload,
                500,
                $e->getMessage(),
                $error,
                $startedAt
            );

            return $error;
        }
    }

    protected function headers(string $timestamp): array
    {
        return [
            'x-cons-id' => $this->consId,
            'x-timestamp' => $timestamp,
            'x-signature' => $this->signature($timestamp),
            'user_key' => $this->userKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function signature(string $timestamp): string
    {
        $data = $this->consId . '&' . $timestamp;

        return base64_encode(
            hash_hmac('sha256', $data, $this->secretKey, true)
        );
    }

    protected function decodeBpjsResponse(array $json, string $timestamp): array
    {
        if (
            !isset($json['response']) ||
            !is_string($json['response']) ||
            trim($json['response']) === ''
        ) {
            return $json;
        }

        try {
            $decrypted = $this->decrypt($json['response'], $timestamp);
            $decodedJson = json_decode($decrypted, true);

            $json['response'] = $decodedJson ?? $decrypted;

            return $json;
        } catch (\Throwable $e) {
            $json['decrypt_error'] = $e->getMessage();

            return $json;
        }
    }

    protected function decrypt(string $encryptedResponse, string $timestamp): string
    {
        $key = $this->consId . $this->secretKey . $timestamp;

        $keyHash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $decrypted = openssl_decrypt(
            base64_decode($encryptedResponse),
            'AES-256-CBC',
            $keyHash,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new \Exception('Gagal decrypt response BPJS VClaim');
        }

        $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);

        return $decompressed ?: $decrypted;
    }

    protected function writeLog(
        string $method,
        string $endpoint,
        array $payload,
        ?int $statusCode,
        string $raw,
        array $decoded,
        float $startedAt
    ): void {
        try {
            DB::table('bpjs_antrean_logs')->insert([
                'method' => $method,
                'endpoint' => '[VCLAIM] ' . $endpoint,
                'request_payload' => json_encode($payload),
                'status_code' => $statusCode,
                'metadata_code' => data_get($decoded, 'metaData.code', data_get($decoded, 'metadata.code')),
                'metadata_message' => data_get($decoded, 'metaData.message', data_get($decoded, 'metadata.message')),
                'raw_response' => $raw,
                'decoded_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            //
        }
    }
    public function listSuratKontrolRange(string $tglAwal, string $tglAkhir, int $filter = 2): array
    {
        /*
        filter:
        1 = tanggal entri
        2 = tanggal rencana kontrol
    */

        return $this->get("/RencanaKontrol/ListRencanaKontrol/tglAwal/{$tglAwal}/tglAkhir/{$tglAkhir}/filter/{$filter}");
    }
    public function getSuratKontrolByNoSurat(string $noSuratKontrol): array
    {
        return $this->get("/RencanaKontrol/noSuratKontrol/{$noSuratKontrol}");
    }

    public function getSuratKontrolByNoKartu(
        string $noKartu,
        string $bulan,
        string $tahun,
        int $filter = 2
    ): array {
        /*
        filter:
        1 = tanggal entri
        2 = tanggal rencana kontrol
    */

        return $this->get("/RencanaKontrol/ListRencanaKontrol/Bulan/{$bulan}/Tahun/{$tahun}/Nokartu/{$noKartu}/filter/{$filter}");
    }
    /**
     * Cari rujukan FKTP/PCare berdasarkan nomor kartu BPJS.
     */
    public function getRujukanFktpByNoKartu(
        string $noKartu
    ): array {
        $noKartu = $this->normalisasiNoKartu($noKartu);

        if ($noKartu === '') {
            return $this->emptyResponse(
                400,
                'Nomor kartu BPJS tidak boleh kosong.'
            );
        }

        return $this->get(
            '/Rujukan/Peserta/' . rawurlencode($noKartu)
        );
    }

    /**
     * Cari rujukan rumah sakit berdasarkan nomor kartu BPJS.
     */
    public function getRujukanRsByNoKartu(
        string $noKartu
    ): array {
        $noKartu = $this->normalisasiNoKartu($noKartu);

        if ($noKartu === '') {
            return $this->emptyResponse(
                400,
                'Nomor kartu BPJS tidak boleh kosong.'
            );
        }

        return $this->get(
            '/Rujukan/RS/Peserta/' . rawurlencode($noKartu)
        );
    }

    /**
     * Cari SEP berdasarkan nomor rujukan.
     */
    public function getSepByNoRujukan(
        string $noRujukan
    ): array {
        $noRujukan = trim($noRujukan);

        if ($noRujukan === '') {
            return $this->emptyResponse(
                400,
                'Nomor rujukan tidak boleh kosong.'
            );
        }

        return $this->get(
            '/SEP/Rujukan/' . rawurlencode($noRujukan)
        );
    }

    /**
     * Alur:
     *
     * 1. Cari rujukan FKTP berdasarkan nomor kartu.
     * 2. Cari rujukan RS berdasarkan nomor kartu.
     * 3. Ambil seluruh nomor rujukan yang ditemukan.
     * 4. Cari SEP berdasarkan masing-masing nomor rujukan.
     * 5. Ambil SEP yang tanggal SEP-nya sama dengan tanggal pelayanan.
     */
    public function cariSepByNoKartuTanggal(
        string $noKartu,
        string $tanggalPelayanan
    ): array {
        $noKartu = $this->normalisasiNoKartu(
            $noKartu
        );

        if ($noKartu === '') {
            return [
                'success' => false,
                'ditemukan' => false,
                'status' => 'TIDAK_ADA_KARTU',
                'no_kartu' => null,
                'tanggal_pelayanan' => null,
                'jumlah_rujukan' => 0,
                'jumlah_sep' => 0,
                'sep' => null,
                'sep_list' => [],
                'rujukan_list' => [],
                'errors' => [],
                'message' => 'Nomor kartu BPJS kosong.',
            ];
        }

        try {
            $tanggalPelayanan = Carbon::parse(
                $tanggalPelayanan
            )->format('Y-m-d');
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'ditemukan' => false,
                'status' => 'TANGGAL_TIDAK_VALID',
                'no_kartu' => $noKartu,
                'tanggal_pelayanan' => null,
                'jumlah_rujukan' => 0,
                'jumlah_sep' => 0,
                'sep' => null,
                'sep_list' => [],
                'rujukan_list' => [],
                'errors' => [$e->getMessage()],
                'message' => 'Tanggal pelayanan tidak valid.',
            ];
        }

        $rujukanList = [];
        $sepList = [];
        $errors = [];

        /*
     * Cari rujukan dari FKTP dan rumah sakit.
     */
        $responsesRujukan = [
            'FKTP' => $this->getRujukanFktpByNoKartu(
                $noKartu
            ),

            'RS' => $this->getRujukanRsByNoKartu(
                $noKartu
            ),
        ];

        foreach (
            $responsesRujukan as $asalRujukan => $response
        ) {
            $metadataCode = $this->getMetadataCode(
                $response
            );

            $metadataMessage = $this->getMetadataMessage(
                $response
            );

            if ($metadataCode !== '200') {
                /*
             * Tidak ditemukan rujukan pada salah satu sumber
             * tidak langsung menghentikan proses.
             */
                $errors[] = sprintf(
                    'Rujukan %s: [%s] %s',
                    $asalRujukan,
                    $metadataCode ?: '-',
                    $metadataMessage ?: 'Tidak ditemukan'
                );

                continue;
            }

            $items = $this->extractRujukanItems(
                $response
            );

            foreach ($items as $item) {
                $noRujukan = trim(
                    (string) (
                        data_get($item, 'noKunjungan')
                        ?? data_get($item, 'noRujukan')
                        ?? data_get($item, 'nomorRujukan')
                        ?? ''
                    )
                );

                if ($noRujukan === '') {
                    continue;
                }

                $rujukanList[] = [
                    'no_rujukan' => $noRujukan,

                    'tanggal_rujukan' =>
                    data_get($item, 'tglKunjungan')
                        ?? data_get($item, 'tglRujukan'),

                    'asal_rujukan' => $asalRujukan,

                    'poli_rujukan' =>
                    data_get($item, 'poliRujukan.nama')
                        ?? data_get($item, 'poliRujukan.nmPoli')
                        ?? data_get($item, 'poli.nama'),

                    'diagnosa' =>
                    data_get($item, 'diagnosa.nama')
                        ?? data_get($item, 'diagnosa.nmDiagnosa'),

                    'raw' => $item,
                ];
            }
        }

        /*
     * Hilangkan nomor rujukan duplikat.
     */
        $rujukanList = collect($rujukanList)
            ->unique(function ($item) {
                return strtoupper(
                    trim(
                        (string) data_get(
                            $item,
                            'no_rujukan'
                        )
                    )
                );
            })
            ->values()
            ->all();

        if (empty($rujukanList)) {
            return [
                'success' => true,
                'ditemukan' => false,
                'status' => 'RUJUKAN_TIDAK_DITEMUKAN',
                'no_kartu' => $noKartu,
                'tanggal_pelayanan' => $tanggalPelayanan,
                'jumlah_rujukan' => 0,
                'jumlah_sep' => 0,
                'sep' => null,
                'sep_list' => [],
                'rujukan_list' => [],
                'errors' => $errors,
                'message' => 'Rujukan peserta tidak ditemukan.',
            ];
        }

        /*
     * Cari SEP untuk setiap nomor rujukan.
     */
        foreach ($rujukanList as $rujukan) {
            $noRujukan = data_get(
                $rujukan,
                'no_rujukan'
            );

            $responseSep = $this->getSepByNoRujukan(
                $noRujukan
            );

            $metadataCode = $this->getMetadataCode(
                $responseSep
            );

            $metadataMessage = $this->getMetadataMessage(
                $responseSep
            );

            if ($metadataCode !== '200') {
                $errors[] = sprintf(
                    'SEP rujukan %s: [%s] %s',
                    $noRujukan,
                    $metadataCode ?: '-',
                    $metadataMessage ?: 'Tidak ditemukan'
                );

                continue;
            }

            $itemsSep = $this->extractSepItems(
                $responseSep
            );

            foreach ($itemsSep as $sep) {
                $tanggalSep = $this->normalisasiTanggal(
                    data_get($sep, 'tglSep')
                        ?? data_get($sep, 'tglSEP')
                        ?? data_get($sep, 'tglsep')
                );

                /*
             * Bagian paling penting:
             * tanggal SEP harus sama dengan tanggal pelayanan.
             */
                if ($tanggalSep !== $tanggalPelayanan) {
                    continue;
                }

                $noSep = trim(
                    (string) (
                        data_get($sep, 'noSep')
                        ?? data_get($sep, 'noSEP')
                        ?? data_get($sep, 'nosep')
                        ?? ''
                    )
                );

                if ($noSep === '') {
                    continue;
                }

                /*
             * Validasi nomor kartu jika response SEP
             * menyediakan field peserta.noKartu.
             */
                $noKartuSep = $this->normalisasiNoKartu(
                    (string) (
                        data_get(
                            $sep,
                            'peserta.noKartu'
                        )
                        ?? data_get($sep, 'noKartu')
                        ?? ''
                    )
                );

                if (
                    $noKartuSep !== '' &&
                    $noKartuSep !== $noKartu
                ) {
                    continue;
                }

                $sepList[] = [
                    'no_sep' => $noSep,
                    'tgl_sep' => $tanggalSep,
                    'no_kartu' => $noKartuSep ?: $noKartu,
                    'no_rujukan' => $noRujukan,
                    'asal_rujukan' => data_get(
                        $rujukan,
                        'asal_rujukan'
                    ),

                    'jenis_pelayanan' =>
                    data_get($sep, 'jnsPelayanan')
                        ?? data_get($sep, 'jenisPelayanan'),

                    'poli' =>
                    data_get($sep, 'poli')
                        ?? data_get($sep, 'poliTujuan.nama')
                        ?? data_get($sep, 'poliTujuan'),

                    'diagnosa' =>
                    data_get($sep, 'diagnosa')
                        ?? data_get($sep, 'diagnosa.nama'),

                    'raw' => $sep,
                ];
            }
        }

        /*
     * Hilangkan SEP duplikat.
     */
        $sepList = collect($sepList)
            ->unique(function ($item) {
                return strtoupper(
                    trim(
                        (string) data_get(
                            $item,
                            'no_sep'
                        )
                    )
                );
            })
            ->values()
            ->all();

        if (empty($sepList)) {
            return [
                'success' => true,
                'ditemukan' => false,
                'status' => 'SEP_TIDAK_DITEMUKAN',
                'no_kartu' => $noKartu,
                'tanggal_pelayanan' => $tanggalPelayanan,
                'jumlah_rujukan' => count($rujukanList),
                'jumlah_sep' => 0,
                'sep' => null,
                'sep_list' => [],
                'rujukan_list' => $rujukanList,
                'errors' => $errors,
                'message' => sprintf(
                    'Tidak ditemukan SEP pada tanggal %s.',
                    $tanggalPelayanan
                ),
            ];
        }

        return [
            'success' => true,
            'ditemukan' => true,
            'status' => 'SEP_DITEMUKAN',
            'no_kartu' => $noKartu,
            'tanggal_pelayanan' => $tanggalPelayanan,
            'jumlah_rujukan' => count($rujukanList),
            'jumlah_sep' => count($sepList),

            /*
         * Digunakan sebagai SEP utama.
         */
            'sep' => $sepList[0],

            /*
         * Tetap disediakan apabila pada tanggal yang sama
         * terdapat lebih dari satu SEP.
         */
            'sep_list' => $sepList,

            'rujukan_list' => $rujukanList,
            'errors' => $errors,

            'message' => count($sepList) > 1
                ? sprintf(
                    'Ditemukan %d SEP pada tanggal %s.',
                    count($sepList),
                    $tanggalPelayanan
                )
                : 'SEP ditemukan.',
        ];
    }

    /**
     * Mengambil daftar rujukan dari berbagai kemungkinan
     * struktur response BPJS.
     */
    protected function extractRujukanItems(
        array $response
    ): array {
        $payload = data_get(
            $response,
            'response.rujukan'
        );

        if ($payload === null) {
            $payload = data_get(
                $response,
                'response.list'
            );
        }

        if ($payload === null) {
            $payload = data_get(
                $response,
                'response'
            );
        }

        return $this->normalisasiMenjadiList(
            $payload
        );
    }

    /**
     * Mengambil daftar SEP dari berbagai kemungkinan
     * struktur response BPJS.
     */
    protected function extractSepItems(
        array $response
    ): array {
        $payload = data_get(
            $response,
            'response.list'
        );

        if ($payload === null) {
            $payload = data_get(
                $response,
                'response.sep'
            );
        }

        if ($payload === null) {
            $payload = data_get(
                $response,
                'response'
            );
        }

        return $this->normalisasiMenjadiList(
            $payload
        );
    }

    /**
     * Mengubah object tunggal atau array menjadi array list.
     */
    protected function normalisasiMenjadiList(
        $payload
    ): array {
        if (is_object($payload)) {
            $payload = json_decode(
                json_encode($payload),
                true
            );
        }

        if (!is_array($payload) || empty($payload)) {
            return [];
        }

        /*
     * Array numerik berarti sudah berupa list.
     */
        if (
            array_keys($payload) ===
            range(0, count($payload) - 1)
        ) {
            return $payload;
        }

        /*
     * Array associative berarti satu object.
     */
        return [$payload];
    }

    protected function getMetadataCode(
        array $response
    ): string {
        return (string) (
            data_get($response, 'metaData.code')
            ?? data_get($response, 'metadata.code')
            ?? ''
        );
    }

    protected function getMetadataMessage(
        array $response
    ): string {
        return (string) (
            data_get($response, 'metaData.message')
            ?? data_get($response, 'metadata.message')
            ?? ''
        );
    }

    protected function normalisasiNoKartu(
        string $noKartu
    ): string {
        return preg_replace(
            '/[^0-9]/',
            '',
            trim($noKartu)
        ) ?? '';
    }

    protected function normalisasiTanggal(
        $tanggal
    ): ?string {
        if (blank($tanggal)) {
            return null;
        }

        try {
            return Carbon::parse($tanggal)
                ->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function emptyResponse(
        int $code,
        string $message
    ): array {
        return [
            'metadata' => [
                'code' => $code,
                'message' => $message,
            ],
            'response' => null,
        ];
    }
    /**
 * Mengambil daftar SEP yang diterbitkan berdasarkan
 * tanggal dan jenis pelayanan.
 *
 * Jenis pelayanan:
 * 1 = Rawat Inap
 * 2 = Rawat Jalan
 */
public function monitoringKunjunganTanggal(
    string $tanggal,
    int $jnsPelayanan = 2
): array {
    try {
        $tanggal = Carbon::parse($tanggal)
            ->format('Y-m-d');
    } catch (\Throwable $e) {
        return $this->emptyResponse(
            400,
            'Tanggal monitoring tidak valid.'
        );
    }

    if (!in_array($jnsPelayanan, [1, 2], true)) {
        return $this->emptyResponse(
            400,
            'Jenis pelayanan hanya boleh 1 atau 2.'
        );
    }

    return $this->get(
        "/Monitoring/Kunjungan/Tanggal/"
        . "{$tanggal}/JnsPelayanan/{$jnsPelayanan}"
    );
}
/**
 * Membandingkan SEP yang tersimpan di SIMRS dengan
 * SEP yang diterbitkan BPJS.
 *
 * Pencarian utama:
 * Monitoring/Kunjungan/Tanggal/{tanggal}/JnsPelayanan/{jenis}
 *
 * Apabila tidak ditemukan pada monitoring kunjungan,
 * pencarian dapat dilanjutkan melalui:
 * nomor kartu → rujukan → SEP.
 */
public function bandingkanSepRegistrasi(
    string $noKartu,
    string $tanggalRegistrasi,
    ?string $noSepSimrs = null,
    int $jnsPelayanan = 2
): array {
    $noKartu = $this->normalisasiNoKartu(
        $noKartu
    );

    $noSepSimrs = trim(
        (string) $noSepSimrs
    );

    if ($noKartu === '') {
        return [
            'success' => false,
            'status' => 'TIDAK_ADA_KARTU',
            'cocok' => false,
            'no_sep_simrs' => $noSepSimrs ?: null,
            'no_sep_bpjs' => null,
            'sep_bpjs' => null,
            'sep_bpjs_list' => [],
            'sumber_bpjs' => null,
            'message' => 'Nomor kartu BPJS kosong.',
        ];
    }

    try {
        $tanggalRegistrasi = Carbon::parse(
            $tanggalRegistrasi
        )->format('Y-m-d');
    } catch (\Throwable $e) {
        return [
            'success' => false,
            'status' => 'TANGGAL_TIDAK_VALID',
            'cocok' => false,
            'no_sep_simrs' => $noSepSimrs ?: null,
            'no_sep_bpjs' => null,
            'sep_bpjs' => null,
            'sep_bpjs_list' => [],
            'sumber_bpjs' => null,
            'message' => 'Tanggal registrasi tidak valid.',
        ];
    }

    /*
     * Pencarian pertama melalui Monitoring Kunjungan.
     */
    $monitoring = $this->monitoringKunjunganTanggal(
        $tanggalRegistrasi,
        $jnsPelayanan
    );

    $metadataCode = $this->getMetadataCode(
        $monitoring
    );

    $metadataMessage = $this->getMetadataMessage(
        $monitoring
    );

    $sepList = collect();
    $errors = [];

    if ($metadataCode === '200') {
        $sepList = collect(
            $this->extractMonitoringKunjunganItems(
                $monitoring
            )
        )
            ->map(function ($sep) {
                return [
                    'no_sep' => trim(
                        (string) (
                            data_get($sep, 'noSep')
                            ?? data_get($sep, 'noSEP')
                            ?? data_get($sep, 'nosep')
                            ?? ''
                        )
                    ),

                    'tgl_sep' => $this->normalisasiTanggal(
                        data_get($sep, 'tglSep')
                            ?? data_get($sep, 'tglSEP')
                            ?? data_get($sep, 'tglsep')
                    ),

                    'no_kartu' => $this->normalisasiNoKartu(
                        (string) (
                            data_get($sep, 'noKartu')
                            ?? data_get(
                                $sep,
                                'peserta.noKartu'
                            )
                            ?? ''
                        )
                    ),

                    'jns_pelayanan' => (int) (
                        data_get($sep, 'jnsPelayanan')
                        ?? data_get(
                            $sep,
                            'jenisPelayanan'
                        )
                        ?? 0
                    ),

                    'poli' =>
                        data_get($sep, 'poli')
                        ?? data_get(
                            $sep,
                            'poliTujuan'
                        )
                        ?? data_get(
                            $sep,
                            'poliTujuan.nama'
                        ),

                    'diagnosa' =>
                        data_get($sep, 'diagnosa')
                        ?? data_get(
                            $sep,
                            'diagnosa.nama'
                        ),

                    'no_rujukan' =>
                        data_get($sep, 'noRujukan')
                        ?? data_get(
                            $sep,
                            'noRujukanAsal'
                        )
                        ?? data_get(
                            $sep,
                            'rujukan.noRujukan'
                        ),

                    'tgl_pulang_sep' =>
                        data_get($sep, 'tglPlgSep')
                        ?? data_get(
                            $sep,
                            'tglPulang'
                        ),

                    'raw' => $sep,
                ];
            })
            ->filter(function ($sep) use (
                $noKartu,
                $tanggalRegistrasi,
                $jnsPelayanan
            ) {
                /*
                 * Nomor kartu harus sama.
                 */
                if (
                    data_get($sep, 'no_kartu') !==
                    $noKartu
                ) {
                    return false;
                }

                /*
                 * Tanggal SEP harus sama dengan
                 * tanggal registrasi.
                 */
                if (
                    data_get($sep, 'tgl_sep') !==
                    $tanggalRegistrasi
                ) {
                    return false;
                }

                /*
                 * Jika response menyediakan jenis pelayanan,
                 * pastikan sesuai.
                 */
                $jenisSep = (int) data_get(
                    $sep,
                    'jns_pelayanan',
                    0
                );

                if (
                    $jenisSep > 0 &&
                    $jenisSep !== $jnsPelayanan
                ) {
                    return false;
                }

                return filled(
                    data_get($sep, 'no_sep')
                );
            })
            ->unique(function ($sep) {
                return $this->normalisasiNoSep(
                    data_get($sep, 'no_sep')
                );
            })
            ->values();
    } else {
        $errors[] = sprintf(
            'Monitoring kunjungan: [%s] %s',
            $metadataCode ?: '-',
            $metadataMessage ?: 'Gagal mengambil data'
        );
    }

    $sumberBpjs = 'MONITORING_KUNJUNGAN';

    /*
     * Fallback ke pencarian nomor kartu → rujukan → SEP.
     *
     * Bagian ini boleh dihapus apabila hanya ingin menggunakan
     * endpoint Monitoring Kunjungan.
     */
    if (
        $sepList->isEmpty() &&
        method_exists(
            $this,
            'cariSepByNoKartuTanggal'
        )
    ) {
        $hasilRujukan = $this
            ->cariSepByNoKartuTanggal(
                $noKartu,
                $tanggalRegistrasi
            );

        $fallbackSepList = data_get(
            $hasilRujukan,
            'sep_list',
            []
        );

        if (
            empty($fallbackSepList) &&
            filled(data_get($hasilRujukan, 'sep.no_sep'))
        ) {
            $fallbackSepList = [
                data_get($hasilRujukan, 'sep'),
            ];
        }

        $sepList = collect($fallbackSepList)
            ->map(function ($sep) use ($noKartu) {
                return [
                    'no_sep' => trim(
                        (string) (
                            data_get($sep, 'no_sep')
                            ?? data_get($sep, 'noSep')
                            ?? ''
                        )
                    ),

                    'tgl_sep' => $this->normalisasiTanggal(
                        data_get($sep, 'tgl_sep')
                            ?? data_get($sep, 'tglSep')
                    ),

                    'no_kartu' =>
                        data_get($sep, 'no_kartu')
                        ?: $noKartu,

                    'jns_pelayanan' =>
                        data_get(
                            $sep,
                            'jenis_pelayanan'
                        ),

                    'no_rujukan' =>
                        data_get($sep, 'no_rujukan'),

                    'asal_rujukan' =>
                        data_get(
                            $sep,
                            'asal_rujukan'
                        ),

                    'poli' => data_get($sep, 'poli'),

                    'diagnosa' => data_get(
                        $sep,
                        'diagnosa'
                    ),

                    'raw' => data_get(
                        $sep,
                        'raw',
                        $sep
                    ),
                ];
            })
            ->filter(function ($sep) {
                return filled(
                    data_get($sep, 'no_sep')
                );
            })
            ->unique(function ($sep) {
                return $this->normalisasiNoSep(
                    data_get($sep, 'no_sep')
                );
            })
            ->values();

        if ($sepList->isNotEmpty()) {
            $sumberBpjs = 'RUJUKAN_SEP';
        }

        $fallbackErrors = data_get(
            $hasilRujukan,
            'errors',
            []
        );

        if (is_array($fallbackErrors)) {
            $errors = array_merge(
                $errors,
                $fallbackErrors
            );
        }
    }

    /*
     * Apabila terdapat lebih dari satu SEP, cari yang sama
     * dengan SEP SIMRS terlebih dahulu.
     */
    $sepYangCocok = null;

    if ($noSepSimrs !== '') {
        $sepYangCocok = $sepList->first(
            function ($sep) use ($noSepSimrs) {
                return $this->normalisasiNoSep(
                    data_get($sep, 'no_sep')
                ) === $this->normalisasiNoSep(
                    $noSepSimrs
                );
            }
        );
    }

    $sepTerpilih = $sepYangCocok
        ?? $sepList->first();

    $noSepBpjs = trim(
        (string) data_get(
            $sepTerpilih,
            'no_sep',
            ''
        )
    );

    /*
     * Tentukan status perbandingan.
     */
    if (
        $noSepSimrs !== '' &&
        $sepYangCocok !== null
    ) {
        $status = 'COCOK';
        $message =
            'SEP SIMRS sama dengan SEP yang diterbitkan BPJS.';
        $cocok = true;
    } elseif (
        $noSepSimrs !== '' &&
        $noSepBpjs !== ''
    ) {
        $status = 'BERBEDA';
        $message =
            'SEP SIMRS berbeda dengan SEP yang diterbitkan BPJS.';
        $cocok = false;
    } elseif (
        $noSepSimrs === '' &&
        $noSepBpjs !== ''
    ) {
        $status = 'BELUM_TERSIMPAN_SIMRS';
        $message =
            'SEP sudah diterbitkan BPJS tetapi belum tersimpan di SIMRS.';
        $cocok = false;
    } elseif (
        $noSepSimrs !== '' &&
        $noSepBpjs === ''
    ) {
        $status = 'TIDAK_DITEMUKAN_BPJS';
        $message =
            'SEP tersedia di SIMRS tetapi tidak ditemukan '
            . 'pada data BPJS tanggal tersebut.';
        $cocok = false;
    } else {
        $status = 'BELUM_ADA_SEP';
        $message =
            'Belum ditemukan SEP di SIMRS maupun BPJS.';
        $cocok = false;
    }

    return [
        'success' => true,
        'status' => $status,
        'cocok' => $cocok,

        'no_kartu' => $noKartu,
        'tanggal_registrasi' => $tanggalRegistrasi,
        'jns_pelayanan' => $jnsPelayanan,

        'no_sep_simrs' =>
            $noSepSimrs !== ''
                ? $noSepSimrs
                : null,

        'no_sep_bpjs' =>
            $noSepBpjs !== ''
                ? $noSepBpjs
                : null,

        'sep_bpjs' => $sepTerpilih,

        'sep_bpjs_list' => $sepList->all(),

        'jumlah_sep_bpjs' => $sepList->count(),

        'sumber_bpjs' => $sepList->isNotEmpty()
            ? $sumberBpjs
            : null,

        'errors' => array_values(
            array_filter($errors)
        ),

        'message' => $message,
    ];
}
protected function extractMonitoringKunjunganItems(
    array $response
): array {
    $payload = data_get(
        $response,
        'response.sep'
    );

    if ($payload === null) {
        $payload = data_get(
            $response,
            'response.list'
        );
    }

    if ($payload === null) {
        $payload = data_get(
            $response,
            'response'
        );
    }

    return $this->normalisasiMenjadiList(
        $payload
    );
}

protected function normalisasiNoSep(
    ?string $noSep
): string {
    return preg_replace(
        '/\s+/',
        '',
        strtoupper(trim((string) $noSep))
    ) ?? '';
}
}
