<?php

namespace App\Http\Controllers;

use App\Services\BpjsAntreanService;
use App\Services\SimrsJadwalDokterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class JadwalDokterController extends Controller
{
    public function index(
        Request $request,
        SimrsJadwalDokterService $simrs,
        BpjsAntreanService $bpjs
    ) {
        $tanggal = Carbon::parse(
            $request->get(
                'tanggal',
                now()->format('Y-m-d')
            )
        )->format('Y-m-d');

        /*
     * Nilai dokter adalah ID pegawai SIMRS
     * hasil pilihan dari pencarian API.
     */
        $dokterId = $request->filled('dokter')
            ? (int) $request->get('dokter')
            : null;

        $namaDokter = trim(
            (string) $request->get('nama_dokter', '')
        );

        /*
         * Kode poli berasal dari pencarian API ruangan.
         *
         * Urutan kode:
         * 1. kdsubspesialisbpjs
         * 2. kdspesialisbpjs, jika subspesialis kosong
         */
        $kodePoli = strtoupper(
            trim((string) $request->get('kodepoli', ''))
        );

        $namaPoli = trim(
            (string) $request->get('nama_poli', '')
        );

        $ruanganId = $request->filled('ruangan_id')
            ? (int) $request->get('ruangan_id')
            : null;

        $dokter = null;
        $hasil = collect();
        $error = null;

        if ($dokterId && $kodePoli !== '') {
            try {
                $request->validate([
                    'tanggal' => [
                        'required',
                        'date',
                    ],
                    'dokter' => [
                        'required',
                        'integer',
                        'min:1',
                    ],
                    'nama_dokter' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],
                    'kodepoli' => [
                        'required',
                        'string',
                        'max:10',
                        'regex:/^[A-Za-z0-9]+$/',
                    ],
                    'nama_poli' => [
                        'nullable',
                        'string',
                        'max:255',
                    ],
                    'ruangan_id' => [
                        'nullable',
                        'integer',
                        'min:1',
                    ],
                ]);

                /*
                 * Periksa ulang dokter melalui API dokter-paging.
                 *
                 * API ini langsung menyediakan:
                 * - id
                 * - namalengkap
                 * - kddokterbpjs
                 *
                 * Tidak ada query database dan tidak menggunakan
                 * endpoint mapping dokter terpisah.
                 */
                $dokter = $this->ambilDokterDariApi(
                    $simrs,
                    $dokterId,
                    $namaDokter
                );

                /*
             * Ambil jadwal dokter SIMRS melalui API.
             */
                $responseSimrs = $simrs->getJadwal(
                    $tanggal,
                    $dokterId,
                    $ruanganId
                );

                $jadwalSimrs = $this
                    ->normalisasiListSimrs(
                        $responseSimrs,
                        $tanggal,
                        $dokterId,
                        $ruanganId
                    );

                /*
             * Ambil jadwal dokter BPJS.
             */
                $responseBpjs = $bpjs->jadwalDokter(
                    $kodePoli,
                    $tanggal
                );

                $jadwalBpjs = $this
                    ->normalisasiListBpjs(
                        $responseBpjs
                    );

                $hasil = $this->bandingkanJadwal(
                    $jadwalSimrs,
                    $jadwalBpjs,
                    $dokter,
                    $tanggal,
                    $kodePoli
                );
            } catch (Throwable $e) {
                report($e);
                $error = $e->getMessage();
            }
        }

        $summary = [
            'total' => $hasil->count(),

            'sesuai' => $hasil
                ->where('status', 'SESUAI')
                ->count(),

            'berbeda' => $hasil
                ->whereNotIn('status', [
                    'SESUAI',
                    'TIDAK_ADA_BPJS',
                ])
                ->count(),

            'tidak_ada_bpjs' => $hasil
                ->where('status', 'TIDAK_ADA_BPJS')
                ->count(),
        ];

        return view(
            'bpjs-antrean.cek-jadwal-dokter',
            compact(
                'tanggal',
                'dokterId',
                'namaDokter',
                'kodePoli',
                'namaPoli',
                'ruanganId',
                'dokter',
                'hasil',
                'summary',
                'error'
            )
        );
    }


    private function ambilDokterDariApi(
        SimrsJadwalDokterService $simrs,
        int $dokterId,
        string $namaDokter
    ): object {
        /*
         * Cari dengan nama yang dipilih agar respons API lebih kecil.
         */
        $response = $simrs->searchDokter(
            $namaDokter,
            1000
        );

        $dokter = $this->normalisasiDokterSearch(
            $response
        )->firstWhere('id', $dokterId);

        /*
         * Cadangan apabila API tidak menemukan nama lengkap karena
         * perbedaan tanda baca atau format gelar.
         */
        if (! $dokter) {
            $response = $simrs->searchDokter(
                '',
                1000
            );

            $dokter = $this->normalisasiDokterSearch(
                $response
            )->firstWhere('id', $dokterId);
        }

        if (! $dokter) {
            throw new \RuntimeException(
                'Dokter tidak ditemukan pada API SIMRS.'
            );
        }

        $kodeDokterBpjs = trim(
            (string) data_get(
                $dokter,
                'kodedokter_bpjs',
                ''
            )
        );

        if ($kodeDokterBpjs === '') {
            throw new \RuntimeException(
                'Kode dokter BPJS untuk '
                    . data_get($dokter, 'nama', 'dokter terpilih')
                    . ' belum tersedia pada API dokter-paging.'
            );
        }

        return (object) [
            'id' => (int) data_get($dokter, 'id'),
            'namalengkap' => (string) data_get(
                $dokter,
                'nama'
            ),
            'kddokterbpjs' => $kodeDokterBpjs,
        ];
    }

    private function bandingkanJadwal(
        Collection $jadwalSimrs,
        Collection $jadwalBpjs,
        object $dokter,
        string $tanggal,
        string $kodePoli
    ): Collection {
        if ($jadwalSimrs->isEmpty()) {
            return collect();
        }

        $kodeDokterBpjs = trim(
            (string) $dokter->kddokterbpjs
        );

        /*
         * Ambil kandidat jadwal BPJS berdasarkan:
         * - kode dokter BPJS
         * - kode poli utama atau kode subspesialis
         *
         * Beberapa respons BPJS mengirim kode pada `kodepoli`,
         * sebagian lainnya menggunakan `kodesubspesialis`.
         */
        $kandidatBpjs = $jadwalBpjs->filter(
            function (array $item) use (
                $kodeDokterBpjs,
                $kodePoli
            ) {
                $kodeDokter = trim(
                    (string) data_get($item, 'kodedokter')
                );

                $kodePoliUtama = strtoupper(
                    trim((string) data_get($item, 'kodepoli', ''))
                );

                $kodeSubspesialis = strtoupper(
                    trim(
                        (string) data_get(
                            $item,
                            'kodesubspesialis',
                            ''
                        )
                    )
                );

                return $kodeDokter === $kodeDokterBpjs
                    && in_array(
                        $kodePoli,
                        [
                            $kodePoliUtama,
                            $kodeSubspesialis,
                        ],
                        true
                    );
            }
        )->values();

        $hariTanggal = Carbon::parse($tanggal)->dayOfWeekIso;

        /*
         * Prioritaskan jadwal yang harinya sama dengan tanggal pilihan.
         * Jika BPJS tidak mengirim field hari, gunakan kandidat pertama.
         * Jika harinya berbeda, kandidat pertama tetap digunakan agar
         * dapat ditampilkan sebagai status BEDA_HARI.
         */
        $bpjsItem = $kandidatBpjs->first(
            function (array $item) use ($hariTanggal) {
                $hariBpjs = (int) data_get($item, 'hari', 0);

                return $hariBpjs === 0
                    || $hariBpjs === $hariTanggal;
            }
        ) ?? $kandidatBpjs->first();

        return $jadwalSimrs
            ->map(function (array $simrsItem) use (
                $dokter,
                $tanggal,
                $kodePoli,
                $bpjsItem
            ) {
                $jamSimrs = $this->formatJamSimrs(
                    data_get($simrsItem, 'jammulai'),
                    data_get($simrsItem, 'jamakhir')
                );

                $quotaSimrs = (int) data_get(
                    $simrsItem,
                    'quota',
                    0
                );

                if (! $bpjsItem) {
                    return $this->buatHasil(
                        $simrsItem,
                        $dokter,
                        $tanggal,
                        $kodePoli,
                        null,
                        $jamSimrs,
                        $quotaSimrs,
                        'TIDAK_ADA_BPJS',
                        'Jadwal dokter tidak ditemukan pada poli '
                            . $kodePoli
                            . ' di BPJS.'
                    );
                }

                $jamBpjs = $this->normalisasiRentangJam(
                    data_get($bpjsItem, 'jadwal')
                );

                $quotaBpjs = (int) data_get(
                    $bpjsItem,
                    'kapasitaspasien',
                    0
                );

                $hariTanggal = Carbon::parse(
                    $tanggal
                )->dayOfWeekIso;

                $hariBpjs = (int) data_get(
                    $bpjsItem,
                    'hari',
                    0
                );

                $liburBpjs = (int) data_get(
                    $bpjsItem,
                    'libur',
                    0
                );

                if ($liburBpjs === 1) {
                    return $this->buatHasil(
                        $simrsItem,
                        $dokter,
                        $tanggal,
                        $kodePoli,
                        $bpjsItem,
                        $jamSimrs,
                        $quotaSimrs,
                        'LIBUR_BPJS',
                        'Jadwal BPJS berstatus libur.'
                    );
                }

                if (
                    $hariBpjs > 0 &&
                    $hariBpjs !== $hariTanggal
                ) {
                    return $this->buatHasil(
                        $simrsItem,
                        $dokter,
                        $tanggal,
                        $kodePoli,
                        $bpjsItem,
                        $jamSimrs,
                        $quotaSimrs,
                        'BEDA_HARI',
                        'Hari jadwal BPJS tidak sesuai dengan tanggal yang dipilih.'
                    );
                }

                $jamSesuai = $jamSimrs === $jamBpjs;
                $quotaSesuai = $quotaSimrs === $quotaBpjs;

                if ($jamSesuai && $quotaSesuai) {
                    $status = 'SESUAI';
                    $keterangan =
                        'Dokter, poli, jam, dan kuota sesuai.';
                } elseif (
                    ! $jamSesuai &&
                    ! $quotaSesuai
                ) {
                    $status = 'BEDA_JAM_DAN_QUOTA';
                    $keterangan =
                        'Jam praktik dan kuota berbeda.';
                } elseif (! $jamSesuai) {
                    $status = 'BEDA_JAM';
                    $keterangan =
                        'Jam praktik SIMRS dan BPJS berbeda.';
                } else {
                    $status = 'BEDA_QUOTA';
                    $keterangan =
                        'Kuota SIMRS dan BPJS berbeda.';
                }

                return $this->buatHasil(
                    $simrsItem,
                    $dokter,
                    $tanggal,
                    $kodePoli,
                    $bpjsItem,
                    $jamSimrs,
                    $quotaSimrs,
                    $status,
                    $keterangan
                );
            })
            ->values();
    }

    private function buatHasil(
        array $simrsItem,
        object $dokter,
        string $tanggal,
        string $kodePoli,
        ?array $bpjsItem,
        string $jamSimrs,
        int $quotaSimrs,
        string $status,
        string $keterangan
    ): array {
        return [
            'tanggal' => $tanggal,

            'id_jadwal_simrs' => data_get(
                $simrsItem,
                'id'
            ),

            'tanggal_simrs' => data_get(
                $simrsItem,
                'tanggal',
                '-'
            ),

            'statuspraktek_simrs' => data_get(
                $simrsItem,
                'statuspraktek',
                '-'
            ),

            'keterangan_simrs' => data_get(
                $simrsItem,
                'keterangan',
                '-'
            ),

            'objectpegawaifk' => data_get(
                $simrsItem,
                'objectpegawaifk'
            ),

            'kodedokter_bpjs' => $dokter->kddokterbpjs,

            'dokter_simrs' => data_get(
                $simrsItem,
                'namalengkap',
                $dokter->namalengkap
            ),

            'dokter_bpjs' => data_get(
                $bpjsItem,
                'namadokter',
                '-'
            ),

            'objectruanganfk' => data_get(
                $simrsItem,
                'objectruanganfk'
            ),

            'poli_simrs' => data_get(
                $simrsItem,
                'namaruangan',
                '-'
            ),

            /*
             * Kode hasil pilihan pencarian poli:
             * kdsubspesialisbpjs, atau kdspesialisbpjs
             * jika subspesialis kosong.
             */
            'kodepoli_bpjs' => $kodePoli,

            'poli_bpjs' => data_get(
                $bpjsItem,
                'namapoli',
                data_get(
                    $bpjsItem,
                    'namasubspesialis',
                    '-'
                )
            ),

            'jam_simrs' => $jamSimrs,

            'jam_bpjs' => $bpjsItem
                ? $this->normalisasiRentangJam(
                    data_get($bpjsItem, 'jadwal')
                )
                : '-',

            'quota_simrs' => $quotaSimrs,

            'quota_bpjs' => $bpjsItem
                ? (int) data_get(
                    $bpjsItem,
                    'kapasitaspasien',
                    0
                )
                : null,

            'hari_bpjs' => data_get(
                $bpjsItem,
                'namahari',
                '-'
            ),

            'libur_bpjs' => data_get(
                $bpjsItem,
                'libur'
            ),

            'status' => $status,
            'keterangan' => $keterangan,
        ];
    }

    private function normalisasiListSimrs(
        array $response,
        string $tanggal,
        int $dokterId,
        ?int $ruanganId = null
    ): Collection {
        $metaCode = (int) data_get(
            $response,
            'metaData.code',
            0
        );

        if ($metaCode !== 200) {
            throw new \RuntimeException(
                (string) data_get(
                    $response,
                    'metaData.message',
                    'Gagal mengambil jadwal dokter dari SIMRS.'
                )
            );
        }

        $data = data_get(
            $response,
            'response.data',
            []
        );

        /*
         * Antisipasi apabila API mengirim satu objek jadwal,
         * bukan array daftar jadwal.
         */
        if (
            is_array($data)
            && array_key_exists('id', $data)
        ) {
            $data = [$data];
        }

        if (! is_array($data)) {
            return collect();
        }

        return collect($data)
            ->filter(fn($item) => is_array($item))
            ->filter(function (array $item) use (
                $tanggal,
                $dokterId,
                $ruanganId
            ) {
                if (
                    (int) data_get(
                        $item,
                        'objectpegawaifk',
                        0
                    ) !== $dokterId
                ) {
                    return false;
                }

                if (
                    data_get(
                        $item,
                        'statusenabled',
                        true
                    ) === false
                ) {
                    return false;
                }

                /*
                 * Jika ID ruangan tersedia dari pencarian poli,
                 * jadwal SIMRS dibatasi pada ruangan tersebut.
                 */
                if (
                    $ruanganId
                    && (int) data_get(
                        $item,
                        'objectruanganfk',
                        0
                    ) !== $ruanganId
                ) {
                    return false;
                }

                $tanggalSimrs = $this
                    ->normalisasiTanggalSimrs(
                        data_get($item, 'tanggal')
                    );

                return $tanggalSimrs === null
                    || $tanggalSimrs === $tanggal;
            })
            ->values();
    }

    private function normalisasiTanggalSimrs(
        ?string $tanggal
    ): ?string {
        if (! $tanggal) {
            return null;
        }

        foreach (
            [
                'd-m-Y',
                'Y-m-d',
                'd/m/Y',
                'Y/m/d',
            ] as $format
        ) {
            try {
                return Carbon::createFromFormat(
                    $format,
                    trim($tanggal)
                )->format('Y-m-d');
            } catch (Throwable $e) {
                // Coba format berikutnya.
            }
        }

        try {
            return Carbon::parse(
                $tanggal
            )->format('Y-m-d');
        } catch (Throwable $e) {
            return null;
        }
    }

    private function normalisasiListBpjs(
        array $response
    ): Collection {
        /*
         * Mendukung beberapa kemungkinan struktur respons:
         *
         * response: [...]
         * response: { list: [...] }
         * langsung berupa array jadwal
         * langsung berupa satu objek jadwal
         */
        $data = data_get($response, 'response', $response);

        if (
            is_array($data) &&
            isset($data['list']) &&
            is_array($data['list'])
        ) {
            $data = $data['list'];
        }

        if (
            is_array($data) &&
            array_key_exists('kodedokter', $data)
        ) {
            $data = [$data];
        }

        if (! is_array($data)) {
            return collect();
        }

        return collect($data)
            ->filter(fn($item) => is_array($item))
            ->values();
    }

    private function formatJamSimrs(
        ?string $jamMulai,
        ?string $jamAkhir
    ): string {
        if (! $jamMulai || ! $jamAkhir) {
            return '-';
        }

        return substr($jamMulai, 0, 5)
            . '-'
            . substr($jamAkhir, 0, 5);
    }

    private function normalisasiRentangJam(
        ?string $jadwal
    ): string {
        if (! $jadwal) {
            return '-';
        }

        $jadwal = str_replace(
            '.',
            ':',
            trim($jadwal)
        );

        preg_match_all(
            '/\d{1,2}:\d{2}/',
            $jadwal,
            $matches
        );

        if (count($matches[0] ?? []) < 2) {
            return preg_replace(
                '/\s+/',
                '',
                $jadwal
            );
        }

        $mulai = Carbon::createFromFormat(
            'H:i',
            str_pad(
                $matches[0][0],
                5,
                '0',
                STR_PAD_LEFT
            )
        )->format('H:i');

        $akhir = Carbon::createFromFormat(
            'H:i',
            str_pad(
                $matches[0][1],
                5,
                '0',
                STR_PAD_LEFT
            )
        )->format('H:i');

        return $mulai . '-' . $akhir;
    }
    public function ajaxDokter(
        Request $request,
        SimrsJadwalDokterService $simrs
    ) {
        try {
            $keyword = trim(
                (string) $request->get(
                    'q',
                    $request->get('query', '')
                )
            );

            $response = $simrs->searchDokter(
                $keyword,
                30
            );

            $list = $this->normalisasiDokterSearch(
                $response
            );

            return response()->json([
                'success' => true,
                'data' => $list->values(),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    private function normalisasiDokterSearch(
        array $response
    ): Collection {
        /*
         * Struktur API dokter-paging:
         *
         * response.dokter: [
         *   {
         *     id,
         *     namalengkap,
         *     kddokterbpjs,
         *     kodeexternal,
         *     statusenabled
         *   }
         * ]
         */
        $data = data_get(
            $response,
            'response.dokter',
            []
        );

        /*
         * Antisipasi satu object dokter.
         */
        if (
            is_array($data)
            && array_key_exists('id', $data)
        ) {
            $data = [$data];
        }

        if (! is_array($data)) {
            return collect();
        }

        return collect($data)
            ->filter(fn($item) => is_array($item))
            ->filter(function (array $item) {
                return data_get(
                    $item,
                    'statusenabled',
                    true
                ) !== false;
            })
            ->map(function (array $item) {
                $id = (int) data_get(
                    $item,
                    'id',
                    0
                );

                $nama = trim(
                    (string) data_get(
                        $item,
                        'namalengkap',
                        data_get(
                            $item,
                            'nama',
                            ''
                        )
                    )
                );

                $kodeDokterBpjs = trim(
                    (string) data_get(
                        $item,
                        'kddokterbpjs',
                        ''
                    )
                );

                if (
                    $id <= 0
                    || $nama === ''
                    || $kodeDokterBpjs === ''
                ) {
                    return null;
                }

                return [
                    'id' => $id,
                    'nama' => $nama,
                    'kodedokter_bpjs' =>
                        $kodeDokterBpjs,
                    'kodeexternal' => trim(
                        (string) data_get(
                            $item,
                            'kodeexternal',
                            ''
                        )
                    ),
                ];
            })
            ->filter()
            ->unique('id')
            ->sortBy('nama')
            ->values();
    }
}
