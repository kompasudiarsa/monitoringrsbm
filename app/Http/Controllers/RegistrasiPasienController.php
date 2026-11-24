<?php

namespace App\Http\Controllers;

use App\Services\BpjsVclaimService;
use App\Services\SimrsRegistrasiPasienService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class RegistrasiPasienController extends Controller
{
    /**
     * Menampilkan daftar pasien registrasi.
     *
     * Method ini tidak melakukan request ke VClaim agar loading halaman
     * tetap cepat. Pengecekan SEP BPJS dilakukan melalui method checkSep().
     */
    public function index(
        Request $request,
        SimrsRegistrasiPasienService $simrs
    ) {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
            'poli' => ['nullable', 'string'],
            'bpjs' => ['nullable', 'in:ALL,BPJS,NON_BPJS'],
            'status_sep' => ['nullable', 'in:ALL,SUDAH,BELUM'],
            'limit' => [
                'nullable',
                'integer',
                'in:10,25,50,100,250,500,1000',
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $tanggalDari = (string) $request->get(
            'dari',
            now()->format('Y-m-d')
        );

        $tanggalSampai = (string) $request->get(
            'sampai',
            $tanggalDari
        );

        $dariCarbon = Carbon::parse($tanggalDari);
        $sampaiCarbon = Carbon::parse($tanggalSampai);

        if ($sampaiCarbon->lt($dariCarbon)) {
            return back()
                ->withInput()
                ->withErrors([
                    'sampai' => 'Tanggal sampai tidak boleh lebih kecil dari tanggal dari.',
                ]);
        }

        $poliFilter = (string) $request->get('poli', 'ALL');
        $bpjsFilter = (string) $request->get('bpjs', 'ALL');
        $statusSepFilter = (string) $request->get('status_sep', 'ALL');

        $limit = (int) $request->get('limit', 25);
        $page = max(1, (int) $request->get('page', 1));

        $errorApi = null;
        $poliOptions = collect();
        $semuaPasien = collect();

        try {
            /*
             * Mengambil seluruh halaman data SIMRS supaya filter lokal dan
             * pagination aplikasi tetap konsisten.
             */
            $apiPage = 1;
            $apiLimit = 1000;
            $maksimalPageApi = 50;
            $masihAdaPage = true;

            do {
                $responseApi = $simrs->getListPasien(
                    $dariCarbon->format('Y-m-d') . ' 00:00',
                    $sampaiCarbon->format('Y-m-d') . ' 23:59',
                    $apiPage,
                    $apiLimit
                );

                $dataPerPage = collect(
                    data_get($responseApi, 'data', [])
                );

                $semuaPasien = $semuaPasien->concat($dataPerPage);

                $lastPageApi = (int) data_get(
                    $responseApi,
                    'last_page',
                    0
                );

                $apiPage++;

                if ($lastPageApi > 0) {
                    $masihAdaPage = $apiPage <= $lastPageApi;
                } else {
                    $masihAdaPage = $dataPerPage->count() >= $apiLimit;
                }
            } while (
                $masihAdaPage &&
                $apiPage <= $maksimalPageApi
            );

            /*
             * Pilihan poli diambil sebelum filter poli diterapkan agar semua
             * poli pada periode tersebut tetap tersedia di dropdown.
             */
            $poliOptions = $semuaPasien
                ->map(function ($item) {
                    return [
                        'id' => (string) data_get(
                            $item,
                            'objectruanganlastfk',
                            ''
                        ),
                        'nama' => trim((string) data_get(
                            $item,
                            'namaruangan',
                            ''
                        )),
                    ];
                })
                ->filter(function ($item) {
                    return $item['id'] !== '' && $item['nama'] !== '';
                })
                ->unique('id')
                ->sortBy('nama')
                ->values();

            /* Filter poli/ruangan. */
            if ($poliFilter !== 'ALL' && $poliFilter !== '') {
                $semuaPasien = $semuaPasien->filter(
                    function ($item) use ($poliFilter) {
                        return (string) data_get(
                            $item,
                            'objectruanganlastfk'
                        ) === $poliFilter;
                    }
                );
            }

            /* Filter cara bayar. */
            if ($bpjsFilter === 'BPJS') {
                $semuaPasien = $semuaPasien->filter(
                    fn ($item) => $this->isPasienBpjs($item)
                );
            } elseif ($bpjsFilter === 'NON_BPJS') {
                $semuaPasien = $semuaPasien->filter(
                    fn ($item) => !$this->isPasienBpjs($item)
                );
            }

            /*
             * Filter status SEP di halaman daftar menggunakan field nosep
             * yang sudah tersimpan di SIMRS. Filter ini tidak memanggil BPJS.
             */
            if ($statusSepFilter === 'SUDAH') {
                $semuaPasien = $semuaPasien->filter(
                    function ($item) {
                        return trim((string) data_get(
                            $item,
                            'nosep',
                            ''
                        )) !== '';
                    }
                );
            } elseif ($statusSepFilter === 'BELUM') {
                $semuaPasien = $semuaPasien->filter(
                    function ($item) {
                        return trim((string) data_get(
                            $item,
                            'nosep',
                            ''
                        )) === '';
                    }
                );
            }

            $semuaPasien = $semuaPasien
                ->sortByDesc('tglregistrasi')
                ->values();
        } catch (Throwable $e) {
            report($e);
            $errorApi = $e->getMessage();
        }

        /* Pagination dilakukan setelah seluruh filter lokal selesai. */
        $total = $semuaPasien->count();

        $dataHalaman = $semuaPasien
            ->forPage($page, $limit)
            ->values();

        $pasien = new LengthAwarePaginator(
            $dataHalaman,
            $total,
            $limit,
            $page,
            [
                'path' => route('registrasi-pasien.index'),
                'query' => $request->except('page'),
            ]
        );

        return view('registrasi-pasien.index', [
            'pasien' => $pasien,
            'tanggalDari' => $tanggalDari,
            'tanggalSampai' => $tanggalSampai,
            'poliOptions' => $poliOptions,
            'poliFilter' => $poliFilter,
            'bpjsFilter' => $bpjsFilter,
            'statusSepFilter' => $statusSepFilter,
            'limit' => $limit,
            'errorApi' => $errorApi,
        ]);
    }

    /**
     * Mengecek SEP BPJS berdasarkan nomor kartu dan tanggal registrasi.
     * Dipanggil secara AJAX ketika tombol "Cek SEP BPJS" ditekan.
     */
    public function checkSep(
    Request $request,
    BpjsVclaimService $vclaim
) {
    $validated = $request->validate([
        'no_kartu' => [
            'required',
            'string',
            'max:30',
        ],

        'tanggal_registrasi' => [
            'required',
            'date',
        ],

        'no_sep_simrs' => [
            'nullable',
            'string',
            'max:50',
        ],

        'jns_pelayanan' => [
            'nullable',
            'integer',
            'in:1,2',
        ],
    ]);

    $noKartu = preg_replace(
        '/[^0-9]/',
        '',
        trim($validated['no_kartu'])
    ) ?? '';

    if ($noKartu === '') {
        return response()->json([
            'success' => false,
            'status' => 'TIDAK_ADA_KARTU',
            'message' =>
                'Nomor kartu BPJS tidak valid.',
        ], 422);
    }

    try {
        $tanggalRegistrasi = Carbon::parse(
            $validated['tanggal_registrasi']
        )->format('Y-m-d');

        /*
         * Default 2 karena halaman ini digunakan
         * untuk registrasi rawat jalan.
         */
        $jnsPelayanan = (int) (
            $validated['jns_pelayanan'] ?? 2
        );

        $hasil = $vclaim
            ->bandingkanSepRegistrasi(
                $noKartu,
                $tanggalRegistrasi,
                $validated['no_sep_simrs'] ?? null,
                $jnsPelayanan
            );

        return response()->json([
            'success' => data_get(
                $hasil,
                'success',
                false
            ),

            'status' => data_get(
                $hasil,
                'status'
            ),

            'cocok' => data_get(
                $hasil,
                'cocok',
                false
            ),

            'message' => data_get(
                $hasil,
                'message',
                'Pengecekan SEP selesai.'
            ),

            'no_kartu' => data_get(
                $hasil,
                'no_kartu'
            ),

            'tanggal_registrasi' => data_get(
                $hasil,
                'tanggal_registrasi'
            ),

            'jns_pelayanan' => data_get(
                $hasil,
                'jns_pelayanan'
            ),

            'no_sep_simrs' => data_get(
                $hasil,
                'no_sep_simrs'
            ),

            'no_sep_bpjs' => data_get(
                $hasil,
                'no_sep_bpjs'
            ),

            'sep_bpjs' => data_get(
                $hasil,
                'sep_bpjs'
            ),

            'sep_bpjs_list' => data_get(
                $hasil,
                'sep_bpjs_list',
                []
            ),

            'jumlah_sep_bpjs' => (int) data_get(
                $hasil,
                'jumlah_sep_bpjs',
                0
            ),

            'sumber_bpjs' => data_get(
                $hasil,
                'sumber_bpjs'
            ),

            'errors' => data_get(
                $hasil,
                'errors',
                []
            ),
        ]);
    } catch (Throwable $e) {
        report($e);

        return response()->json([
            'success' => false,
            'status' => 'ERROR',
            'cocok' => false,
            'message' =>
                'Gagal melakukan pengecekan SEP.',
            'error' => config('app.debug')
                ? $e->getMessage()
                : null,
        ], 500);
    }
}

    /**
     * Memeriksa apakah cara bayar pasien termasuk kelompok BPJS.
     */
    protected function isPasienBpjs($item): bool
    {
        $caraBayar = strtoupper(
            trim((string) data_get(
                $item,
                'kelompokpasien',
                ''
            ))
        );

        return strpos($caraBayar, 'BPJS') !== false;
    }
}