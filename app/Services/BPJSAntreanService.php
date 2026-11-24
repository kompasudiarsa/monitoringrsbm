<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use LZCompressor\LZString;


class BpjsAntreanService
{
    protected string $baseUrl;
    protected string $consId;
    protected string $secretKey;
    protected string $userKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.bpjs_antrean.base_url'), '/');
        $this->consId = config('services.bpjs_antrean.cons_id');
        $this->secretKey = config('services.bpjs_antrean.secret_key');
        $this->userKey = config('services.bpjs_antrean.user_key');
    }

    public function antreanTanggal(string $tanggal): array
    {
        return $this->get("/antrean/pendaftaran/tanggal/{$tanggal}");
    }

    public function antreanKodeBooking(string $kodebooking): array
    {
        return $this->get("/antrean/pendaftaran/kodebooking/{$kodebooking}");
    }

    public function antreanAktif(): array
    {
        return $this->get("/antrean/pendaftaran/aktif");
    }
    public function antreanByJadwal(
        string $kodepoli,
        string $kodedokter,
        string $hari,
        string $jampraktek
    ): array {
        return $this->get(
            '/antrean/pendaftaran/kodepoli/' . rawurlencode($kodepoli)
                . '/kodedokter/' . rawurlencode($kodedokter)
                . '/hari/' . rawurlencode($hari)
                . '/jampraktek/' . rawurlencode($jampraktek)
        );
    }
    public function dashboardTanggal(string $tanggal, string $waktu = 'rs'): array
    {
        $waktu = strtolower(trim($waktu));

        if (!in_array($waktu, ['rs', 'server'], true)) {
            $waktu = 'rs';
        }

        return $this->get("/dashboard/waktutunggu/tanggal/{$tanggal}/waktu/{$waktu}");
    }
    public function listTask(string $kodebooking): array
    {
        return $this->post('/antrean/getlisttask', [
            'kodebooking' => $kodebooking,
        ]);
    }
    public function batalAntrean(string $kodebooking, string $keterangan): array
    {
        return $this->post('/antrean/batal', [
            'kodebooking' => $kodebooking,
            'keterangan' => $keterangan,
        ]);
    }

    protected function get(string $endpoint): array
    {
        return $this->send('GET', $endpoint);
    }

    protected function post(string $endpoint, array $payload = []): array
    {
        return $this->send('POST', $endpoint, $payload);
    }


    protected function send(string $method, string $endpoint, array $payload = []): array
    {
        $timestamp = (string) time();
        $url = $this->baseUrl . $endpoint;
        $startedAt = microtime(true);

        try {
            $http = Http::timeout(25)
                ->retry(2, 500)
                ->withHeaders($this->headers($timestamp));

            $response = $method === 'GET'
                ? $http->get($url)
                : $http->post($url, $payload);

            $raw = $response->body();
            $json = $response->json();

            if (!is_array($json)) {
                $json = [
                    'metadata' => [
                        'code' => $response->status(),
                        'message' => 'Response BPJS bukan JSON valid',
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
            throw new \Exception('Gagal decrypt response BPJS');
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
                'endpoint' => $endpoint,
                'request_payload' => json_encode($payload),
                'status_code' => $statusCode,
                'metadata_code' => data_get($decoded, 'metadata.code'),
                'metadata_message' => data_get($decoded, 'metadata.message'),
                'raw_response' => $raw,
                'decoded_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai gagal tulis log membuat aplikasi error.
        }
    }
    public function pendaftaranTanggal(string $tanggal)
    {
        return $this->get("/antrean/pendaftaran/tanggal/{$tanggal}");
    }
public function jadwalDokter(
    string $kodePoli,
    string $tanggal
): array {
    return $this->get(
        '/jadwaldokter/kodepoli/'
        . urlencode(strtoupper($kodePoli))
        . '/tanggal/'
        . urlencode($tanggal)
    );
}
}
