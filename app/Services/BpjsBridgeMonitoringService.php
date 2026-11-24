<?php

namespace App\Services;

use App\Models\BpjsBridgeMonitoring;
use Closure;
use Throwable;

class BpjsBridgeMonitoringService
{
    protected BpjsAntreanService $antrean;
    protected BpjsVclaimService $vclaim;

    public function __construct(
        BpjsAntreanService $antrean,
        BpjsVclaimService $vclaim
    ) {
        $this->antrean = $antrean;
        $this->vclaim = $vclaim;
    }

    public function checkAll(): array
    {
        $tanggal = now()->format('Y-m-d');
        $waktu = now()->format('H:i:s');

        return [
            $this->runCheck(
                'Antrean BPJS - Pendaftaran Tanggal',
                '/antrean/pendaftaran/tanggal/' . $tanggal,
                fn() => $this->antrean->pendaftaranTanggal($tanggal)
            ),

            $this->runCheck(
                'Antrean BPJS - Antrean Aktif',
                '/antrean/pendaftaran/aktif',
                fn() => $this->antrean->antreanAktif()
            ),

            $this->runCheck(
                'Antrean BPJS - Dashboard',
                '/dashboard/waktutunggu/tanggal/' . $tanggal . '/waktu/' . $waktu,
                fn() => $this->antrean->dashboardTanggal(
                    $tanggal,
                    $waktu
                )
            ),

            $this->runCheck(
                'VClaim - Surat Kontrol',
                '/RencanaKontrol/ListRencanaKontrol',
                fn() => $this->vclaim->listSuratKontrolPerHari(
                    $tanggal,
                    2
                )
            ),
        ];
    }

    protected function runCheck(
        string $service,
        string $endpoint,
        Closure $callback
    ): BpjsBridgeMonitoring {
        $startedAt = microtime(true);

        try {
            $result = $callback();

            $responseTime = (int) round(
                (microtime(true) - $startedAt) * 1000
            );

            $bpjsCode = $this->getBpjsCode($result);
            $message = $this->getBpjsMessage($result);
            $payloadCount = $this->getPayloadCount($result);

            $status = $this->determineStatus(
                $service,
                $bpjsCode,
                $message,
                $responseTime,
                $result
            );

            return BpjsBridgeMonitoring::create([
                'service' => $service,
                'endpoint' => $endpoint,
                'status' => $status,
                'bpjs_code' => $bpjsCode,
                'message' => $message,
                'response_time_ms' => $responseTime,
                'payload_count' => $payloadCount,
                'error_detail' => null,
                'checked_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $responseTime = (int) round(
                (microtime(true) - $startedAt) * 1000
            );

            report($exception);

            return BpjsBridgeMonitoring::create([
                'service' => $service,
                'endpoint' => $endpoint,
                'status' => 'DOWN',
                'bpjs_code' => null,
                'message' => $exception->getMessage(),
                'response_time_ms' => $responseTime,
                'payload_count' => null,
                'error_detail' => $exception->getTraceAsString(),
                'checked_at' => now(),
            ]);
        }
    }
    protected function determineStatus(
        string $service,
        ?string $bpjsCode,
        string $message,
        int $responseTime,
        $result
    ): string {
        $messageLower = strtolower($message);

        /*
     * Tidak ada respons sama sekali.
     */
        if ($result === null) {
            return 'DOWN';
        }

        /*
     * Gangguan koneksi, autentikasi, signature, atau timeout.
     * Kondisi ini benar-benar menunjukkan bridging bermasalah.
     */
        $downIndicators = [
            'connection refused',
            'connection timed out',
            'timed out',
            'could not resolve host',
            'unable to connect',
            'unauthorized',
            'invalid signature',
            'signature tidak valid',
            'consumer id',
            'service unavailable',
            'gateway timeout',
        ];

        foreach ($downIndicators as $indicator) {
            if (str_contains($messageLower, $indicator)) {
                return 'DOWN';
            }
        }

        /*
     * Kode autentikasi atau gateway.
     */
        if (in_array((string) $bpjsCode, [
            '401',
            '403',
            '502',
            '503',
            '504',
        ], true)) {
            return 'DOWN';
        }

        /*
     * Respons diterima, tetapi endpoint BPJS mengalami masalah
     * saat memproses query atau data.
     */
        $degradedIndicators = [
            'internal error executing query',
            'no results returned',
            'internal server error',
            'data tidak ditemukan',
            'hasil tidak ditemukan',
            'query error',
        ];

        foreach ($degradedIndicators as $indicator) {
            if (str_contains($messageLower, $indicator)) {
                return 'DEGRADED';
            }
        }

        /*
     * Respons sukses.
     */
        if (
            $bpjsCode === null ||
            in_array((string) $bpjsCode, ['200', '201'], true)
        ) {
            if (
                $responseTime >=
                config('services.bpjs_monitoring.warning_ms', 5000)
            ) {
                return 'DEGRADED';
            }

            return 'UP';
        }

        /*
     * Kode selain sukses, tetapi server BPJS masih memberikan respons.
     */
        return 'DEGRADED';
    }
    protected function getBpjsCode($result): ?string
    {
        $code = data_get($result, 'metadata.code')
            ?? data_get($result, 'metaData.code')
            ?? data_get($result, 'meta_data.code')
            ?? data_get($result, 'code');

        return $code !== null ? (string) $code : null;
    }

    protected function getBpjsMessage($result): string
    {
        return (string) (
            data_get($result, 'metadata.message')
            ?? data_get($result, 'metaData.message')
            ?? data_get($result, 'meta_data.message')
            ?? data_get($result, 'message')
            ?? 'Respons API diterima'
        );
    }

    protected function getPayloadCount($result): ?int
    {
        $payload = data_get($result, 'response.list')
            ?? data_get($result, 'response')
            ?? data_get($result, 'data')
            ?? data_get($result, 'list');

        if (is_countable($payload)) {
            return count($payload);
        }

        return null;
    }
}
