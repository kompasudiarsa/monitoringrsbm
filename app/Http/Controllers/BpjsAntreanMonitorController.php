<?php

namespace App\Http\Controllers;

use App\Services\BpjsAntreanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BpjsVclaimService;
use App\Services\SimrsRiwayatKontrolService;
use App\Services\SimrsApiService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class BpjsAntreanMonitorController extends Controller
{
    public function menu()
    {
        return view('bpjs-antrean.menu');
    }
    public function dashboard(Request $request, BpjsAntreanService $bpjs, SimrsRiwayatKontrolService $simrs)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $waktu = $request->get('waktu', now()->format('H:i:s'));
        $jenis = $request->get('jenis', 'semua');
        $status = $request->get('status', 'semua');
        $antreanTanggal = $bpjs->antreanTanggal($tanggal);
        $antreanAktif = $bpjs->antreanAktif();
        $dashboard = $bpjs->dashboardTanggal($tanggal, $waktu);
        $nama = trim($request->get('nama', ''));
        $nokartu = trim($request->get('nokartu', ''));
        $duplikatOnly = $request->get('duplikat_only', 'ALL');

        if (!in_array($duplikatOnly, ['ALL', 'DUPLIKAT', 'TIDAK_DUPLIKAT'], true)) {
            $duplikatOnly = 'ALL';
        }
        $dataSimrs = $simrs->getRiwayatKontrol(
            $tanggal,
            $tanggal,
            'tglreservasi'
        );

        $riwayatSimrs = data_get($dataSimrs, 'response.riwayat', []);

        if (!is_array($riwayatSimrs)) {
            $riwayatSimrs = [];
        }

        $itemsTanggalBpjs = data_get(
            $antreanTanggal,
            'response.list',
            data_get($antreanTanggal, 'response', [])
        );

        if (!is_array($itemsTanggalBpjs)) {
            $itemsTanggalBpjs = [];
        }

        $itemsTanggalGabungan = $this->mergeAntreanBpjsDenganSimrs(
            $itemsTanggalBpjs,
            $riwayatSimrs,
            $tanggal
        );
        $statistikAntrean = $this->buatStatistikAntrean(
            $itemsTanggalBpjs,
            $itemsTanggalGabungan,
            $riwayatSimrs
        );

        $itemsTanggalTampil = $this->filterItemsAntrean(
            $itemsTanggalGabungan,
            $jenis,
            $status,
            $nama,
            $nokartu
        );

        /*
        Cari no kartu yang duplikat dari data hasil filter awal.
        */
        $noKartuDuplikat = $this->cariNoKartuDuplikat($itemsTanggalTampil);

        $noKartuDuplikatKeys = collect($noKartuDuplikat)
            ->pluck('no_kartu')
            ->values()
            ->toArray();

        $totalNoKartuDuplikat = count($noKartuDuplikat);

        $totalDataNoKartuDuplikat = collect($noKartuDuplikat)
            ->sum('jumlah');

        /*
        Filter table utama:
        ALL              = semua data
        DUPLIKAT         = hanya data yang no kartu BPJS-nya muncul lebih dari sekali
        TIDAK_DUPLIKAT   = hanya data yang no kartu BPJS-nya tidak duplikat
        */
        if ($duplikatOnly === 'DUPLIKAT') {
            $itemsTanggalTampil = collect($itemsTanggalTampil)
                ->filter(function ($item) use ($noKartuDuplikatKeys) {
                    $noKartu = $this->ambilNoKartuBpjs($item);

                    return $noKartu !== ''
                        && in_array($noKartu, $noKartuDuplikatKeys, true);
                })
                ->values()
                ->toArray();
        }

        if ($duplikatOnly === 'TIDAK_DUPLIKAT') {
            $itemsTanggalTampil = collect($itemsTanggalTampil)
                ->filter(function ($item) use ($noKartuDuplikatKeys) {
                    $noKartu = $this->ambilNoKartuBpjs($item);

                    /*
            Data tanpa no kartu tetap ditampilkan sebagai tidak duplikat,
            terutama data dari SIMRS.
            */
                    if ($noKartu === '') {
                        return true;
                    }

                    return !in_array($noKartu, $noKartuDuplikatKeys, true);
                })
                ->values()
                ->toArray();
        }
        logger()->info('Monitoring Antrean Count', [
            'bpjs_awal' => count($itemsTanggalBpjs),
            'simrs_awal' => count($riwayatSimrs),
            'gabungan' => count($itemsTanggalGabungan),
            'setelah_filter' => count($itemsTanggalTampil),
            'jenis' => $jenis,
            'status' => $status,
            'nama' => $nama,
            'nokartu' => $nokartu,
            'duplikat_only' => $duplikatOnly,
        ]);
        if (isset($antreanTanggal['response']['list'])) {
            $antreanTanggal['response']['list'] = $itemsTanggalTampil;
        } else {
            $antreanTanggal['response'] = $itemsTanggalTampil;
        }
        $itemsTanggalPaginated = $this->paginateArray($itemsTanggalTampil, 20, 'page_antrean');

        $noKartuDuplikatPaginated = $this->paginateArray($noKartuDuplikat, 10, 'page_duplikat');
        logger()->info('COUNT MONITORING BPJS', [
            'bpjs_awal' => count($itemsTanggalBpjs),
            'simrs_awal' => count($riwayatSimrs),
            'gabungan' => count($itemsTanggalGabungan),
            'setelah_filter' => count($itemsTanggalTampil),
            'duplikat_only' => $duplikatOnly,
        ]);
        return view('bpjs-antrean.index', compact(
            'tanggal',
            'waktu',
            'jenis',
            'status',
            'nama',
            'nokartu',
            'duplikatOnly',
            'statistikAntrean',
            'noKartuDuplikat',
            'totalNoKartuDuplikat',
            'totalDataNoKartuDuplikat',
            'antreanTanggal',
            'antreanAktif',
            'dashboard',
            'itemsTanggalPaginated',
            'noKartuDuplikatPaginated',
            'dataSimrs'

        ));
    }
    private function cariNoKartuDuplikat(array $items): array
    {
        $groups = collect($items)
            ->map(function ($item) {
                $noKartu = $this->ambilNoKartuBpjs($item);

                return [
                    'no_kartu' => $noKartu,
                    'item' => $item,
                ];
            })
            ->filter(fn($row) => $row['no_kartu'] !== '')
            ->groupBy('no_kartu')
            ->filter(fn($group) => $group->count() > 1);

        return $groups
            ->map(function ($group, $noKartu) {
                return [
                    'no_kartu' => $noKartu,
                    'jumlah' => $group->count(),
                    'items' => $group->pluck('item')->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }
    private function filterItemsAntrean(
        array $items,
        string $jenis = 'semua',
        string $status = 'semua',
        string $nama = '',
        string $nokartu = ''
    ): array {
        $jenis = strtolower(trim($jenis));
        $status = strtolower(trim($status));
        $nama = strtolower(trim($nama));
        $nokartu = preg_replace('/\D/', '', $nokartu);

        return collect($items)
            ->filter(function ($item) use ($jenis, $status, $nama, $nokartu) {
                if ($jenis !== 'semua') {
                    $jenisText = strtolower(trim((string) data_get(
                        $item,
                        'jenis',
                        data_get(
                            $item,
                            'jenisantrean',
                            data_get(
                                $item,
                                'sumberdata',
                                data_get(
                                    $item,
                                    'sumber_data',
                                    data_get(
                                        $item,
                                        'asalantrean',
                                        data_get($item, 'asal_pendaftaran', '')
                                    )
                                )
                            )
                        )
                    )));

                    if ($jenis === 'mobile_jkn' && !str_contains($jenisText, 'mobile')) {
                        return false;
                    }

                    if ($jenis === 'bridging' && !str_contains($jenisText, 'bridging')) {
                        return false;
                    }

                    if ($jenis === 'onsite' && !str_contains($jenisText, 'onsite') && !str_contains($jenisText, 'loket')) {
                        return false;
                    }

                    if ($jenis === 'lainnya') {
                        if (
                            str_contains($jenisText, 'mobile') ||
                            str_contains($jenisText, 'bridging') ||
                            str_contains($jenisText, 'onsite') ||
                            str_contains($jenisText, 'loket')
                        ) {
                            return false;
                        }
                    }
                }

                if ($status !== 'semua') {
                    $statusText = strtolower(trim((string) data_get(
                        $item,
                        'status',
                        data_get($item, 'statusantrean', '')
                    )));

                    if ($status === 'selesai' && !str_contains($statusText, 'selesai')) {
                        return false;
                    }

                    if ($status === 'sedang' && !str_contains($statusText, 'sedang')) {
                        return false;
                    }

                    if ($status === 'belum' && !str_contains($statusText, 'belum')) {
                        return false;
                    }

                    if ($status === 'batal' && !str_contains($statusText, 'batal')) {
                        return false;
                    }

                    if ($status === 'lainnya') {
                        if (
                            str_contains($statusText, 'selesai') ||
                            str_contains($statusText, 'sedang') ||
                            str_contains($statusText, 'belum') ||
                            str_contains($statusText, 'batal')
                        ) {
                            return false;
                        }
                    }
                }

                if ($nama !== '') {
                    $namaPasien = strtolower(trim((string) data_get(
                        $item,
                        'namapasien',
                        data_get(
                            $item,
                            'nama_pasien',
                            data_get(
                                $item,
                                'nama',
                                data_get($item, 'pasien.nama', '')
                            )
                        )
                    )));

                    if (!str_contains($namaPasien, $nama)) {
                        return false;
                    }
                }

                if ($nokartu !== '') {
                    $noKartuItem = preg_replace('/\D/', '', (string) data_get(
                        $item,
                        'nokapst',
                        data_get(
                            $item,
                            'nomorkartu',
                            data_get(
                                $item,
                                'nokartu',
                                data_get($item, 'no_kartu', '')
                            )
                        )
                    ));

                    if (!str_contains($noKartuItem, $nokartu)) {
                        return false;
                    }
                }

                return true;
            })
            ->values()
            ->toArray();
    }

    private function matchJenisAntrean(array $item, string $jenis): bool
    {
        $jenisText = strtolower(trim((string) $this->ambilJenisAntrean($item)));

        if ($jenisText === '') {
            return false;
        }

        if ($jenis === 'mobile_jkn') {
            return str_contains($jenisText, 'mobile')
                || str_contains($jenisText, 'jkn');
        }

        if ($jenis === 'bridging') {
            return str_contains($jenisText, 'bridging')
                || str_contains($jenisText, 'simrs')
                || str_contains($jenisText, 'ws');
        }

        if ($jenis === 'onsite') {
            return str_contains($jenisText, 'onsite')
                || str_contains($jenisText, 'loket')
                || str_contains($jenisText, 'manual')
                || str_contains($jenisText, 'offline');
        }

        return true;
    }


    public function suratKontrol(Request $request, BpjsVclaimService $vclaim)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $filter = (int) $request->get('filter', 2);

        $suratKontrol = $vclaim->listSuratKontrolPerHari($tanggal, $filter);

        return view('bpjs-antrean.surat-kontrol', compact(
            'tanggal',
            'filter',
            'suratKontrol'
        ));
    }

    public function aktif(BpjsAntreanService $bpjs)
    {
        $antreanAktif = $bpjs->antreanAktif();

        return view('bpjs-antrean.aktif', compact('antreanAktif'));
    }

    public function detailForm()
    {
        return view('bpjs-antrean.detail-form');
    }

    public function detail(string $kodebooking, BpjsAntreanService $bpjs)
    {
        $detail = $bpjs->antreanKodeBooking($kodebooking);
        $tasks = $bpjs->listTask($kodebooking);

        return view('bpjs-antrean.detail', compact(
            'kodebooking',
            'detail',
            'tasks'
        ));
    }

    public function logs()
    {
        $logs = DB::table('bpjs_antrean_logs')
            ->orderByDesc('id')
            ->paginate(20);

        return view('bpjs-antrean.logs', compact('logs'));
    }

    public function selisih(
        Request $request,
        BpjsAntreanService $bpjs,
        SimrsRiwayatKontrolService $simrs
    ) {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $cekTask = $request->boolean('cek_task', false);

        /*
        1. Ambil data antrean BPJS
        */
        $antreanTanggal = $bpjs->antreanTanggal($tanggal);

        $itemsBpjs = data_get(
            $antreanTanggal,
            'response.list',
            data_get($antreanTanggal, 'response', [])
        );

        if (!is_array($itemsBpjs)) {
            $itemsBpjs = [];
        }

        /*
            2. Ambil data antrean/reservasi SIMRS
        */
        $dataSimrs = $simrs->getRiwayatKontrol(
            $tanggal,
            $tanggal,
            'tglreservasi'
        );

        $itemsSimrs = data_get($dataSimrs, 'response.riwayat', []);

        if (!is_array($itemsSimrs)) {
            $itemsSimrs = [];
        }

        /*
        3. Index data berdasarkan kode booking
        */
        $bpjsByKode = collect($itemsBpjs)
            ->filter(fn($item) => $this->ambilKodeBookingBpjs($item) !== '')
            ->keyBy(fn($item) => $this->ambilKodeBookingBpjs($item));

        $simrsByNoReservasi = collect($itemsSimrs)
            ->filter(fn($item) => $this->ambilNoReservasiSimrs($item) !== '')
            ->keyBy(fn($item) => $this->ambilNoReservasiSimrs($item));

        /*
        4. Kode booking ada di SIMRS tetapi tidak ada di BPJS
      */
        $simrsTidakAdaDiBpjs = collect($itemsSimrs)
            ->filter(function ($item) use ($bpjsByKode) {
                $noreservasi = $this->ambilNoReservasiSimrs($item);

                return $noreservasi !== '' && !$bpjsByKode->has($noreservasi);
            })
            ->values()
            ->toArray();

        /*
        5. Antrean BPJS ada tetapi tidak ada di SIMRS
      */
        $bpjsTidakAdaDiSimrs = collect($itemsBpjs)
            ->filter(function ($item) use ($simrsByNoReservasi) {
                $kodebooking = $this->ambilKodeBookingBpjs($item);

                return $kodebooking !== '' && !$simrsByNoReservasi->has($kodebooking);
            })
            ->values()
            ->toArray();

        /*
        6. Pasien sudah check-in di SIMRS tetapi task ID BPJS belum update.
        Logika ini pakai status SIMRS dan status BPJS.
      */
        $checkinBelumUpdateTask = collect($itemsSimrs)
            ->filter(function ($item) use ($bpjsByKode) {
                $noreservasi = $this->ambilNoReservasiSimrs($item);

                if ($noreservasi === '' || !$bpjsByKode->has($noreservasi)) {
                    return false;
                }

                $bpjsItem = $bpjsByKode->get($noreservasi);

                $statusSimrs = strtolower($this->ambilStatusSimrs($item));
                $statusBpjs = strtolower($this->ambilStatusAntrean($bpjsItem));

                $sudahCheckinSimrs =
                    str_contains($statusSimrs, 'check') ||
                    str_contains($statusSimrs, 'registrasi') ||
                    str_contains($statusSimrs, 'dilayani') ||
                    str_contains($statusSimrs, 'selesai');

                $bpjsBelumBerjalan =
                    str_contains($statusBpjs, 'belum') ||
                    $statusBpjs === '' ||
                    $statusBpjs === '-';

                return $sudahCheckinSimrs && $bpjsBelumBerjalan;
            })
            ->values()
            ->toArray();

        /*
        7. Antrean BPJS aktif tetapi SIMRS sudah selesai
      */
        $bpjsAktifSimrsSelesai = collect($itemsBpjs)
            ->filter(function ($bpjsItem) use ($simrsByNoReservasi) {
                $kodebooking = $this->ambilKodeBookingBpjs($bpjsItem);

                if ($kodebooking === '' || !$simrsByNoReservasi->has($kodebooking)) {
                    return false;
                }

                $simrsItem = $simrsByNoReservasi->get($kodebooking);

                $statusBpjs = strtolower($this->ambilStatusAntrean($bpjsItem));
                $statusSimrs = strtolower($this->ambilStatusSimrs($simrsItem));

                $bpjsMasihAktif =
                    str_contains($statusBpjs, 'belum') ||
                    str_contains($statusBpjs, 'sedang') ||
                    str_contains($statusBpjs, 'aktif');

                $simrsSudahSelesai =
                    str_contains($statusSimrs, 'selesai') ||
                    str_contains($statusSimrs, 'sudah dilayani');

                return $bpjsMasihAktif && $simrsSudahSelesai;
            })
            ->values()
            ->toArray();

        /*
        8. Task ID belum lengkap.
        Default tidak hit API task satu-satu agar tidak berat.
        Aktifkan dengan checkbox cek_task=1.
     */
        $taskBelumLengkap = [];

        if ($cekTask) {
            foreach ($itemsBpjs as $bpjsItem) {
                $kodebooking = $this->ambilKodeBookingBpjs($bpjsItem);

                if ($kodebooking === '') {
                    continue;
                }

                try {
                    $tasks = $bpjs->listTask($kodebooking);

                    $taskList = data_get(
                        $tasks,
                        'response.list',
                        data_get($tasks, 'response', [])
                    );

                    if (!is_array($taskList)) {
                        $taskList = [];
                    }

                    $taskIds = collect($taskList)
                        ->pluck('taskid')
                        ->map(fn($id) => (int) $id)
                        ->filter()
                        ->values()
                        ->toArray();

                    $wajibAda = [1, 2, 3, 4, 5];
                    $belumAda = array_values(array_diff($wajibAda, $taskIds));

                    if (count($belumAda) > 0) {
                        $taskBelumLengkap[] = [
                            'kodebooking' => $kodebooking,
                            'namapasien' => data_get($bpjsItem, 'namapasien', '-'),
                            'noantrean' => data_get($bpjsItem, 'noantrean', data_get($bpjsItem, 'noantrian', '-')),
                            'nomorkartu' => data_get($bpjsItem, 'nomorkartu', data_get($bpjsItem, 'nokapst', '-')),
                            'task_ada' => $taskIds,
                            'task_belum_ada' => $belumAda,
                        ];
                    }
                } catch (\Throwable $e) {
                    $taskBelumLengkap[] = [
                        'kodebooking' => $kodebooking,
                        'namapasien' => data_get($bpjsItem, 'namapasien', '-'),
                        'noantrean' => data_get($bpjsItem, 'noantrean', data_get($bpjsItem, 'noantrian', '-')),
                        'nomorkartu' => data_get($bpjsItem, 'nomorkartu', data_get($bpjsItem, 'nokapst', '-')),
                        'task_ada' => [],
                        'task_belum_ada' => [
                            'Gagal cek task ID: ' . $e->getMessage()
                        ],
                    ];
                }
            }
        }

        $statistik = [
            'total_simrs' => count($itemsSimrs),
            'total_bpjs' => count($itemsBpjs),
            'simrs_tidak_ada_di_bpjs' => count($simrsTidakAdaDiBpjs),
            'bpjs_tidak_ada_di_simrs' => count($bpjsTidakAdaDiSimrs),
            'checkin_belum_update_task' => count($checkinBelumUpdateTask),
            'bpjs_aktif_simrs_selesai' => count($bpjsAktifSimrsSelesai),
            'task_belum_lengkap' => count($taskBelumLengkap),
        ];

        return view('bpjs-antrean.selisih', compact(
            'tanggal',
            'cekTask',
            'statistik',
            'simrsTidakAdaDiBpjs',
            'bpjsTidakAdaDiSimrs',
            'checkinBelumUpdateTask',
            'bpjsAktifSimrsSelesai',
            'taskBelumLengkap'
        ));
    }

    public function refresh(Request $request, BpjsAntreanService $bpjs)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $waktu = $request->get('waktu', now()->format('H:i:s'));

        return response()->json([
            'tanggal' => $tanggal,
            'waktu' => $waktu,
            'antrean_tanggal' => $bpjs->antreanTanggal($tanggal),
            'antrean_aktif' => $bpjs->antreanAktif(),
            'dashboard' => $bpjs->dashboardTanggal($tanggal, $waktu),
        ]);
    }

    public function cekSuratKontrol(
        Request $request,
        SimrsRiwayatKontrolService $simrs,
        BpjsVclaimService $vclaim
    ) {
        $dari = $request->get('dari', now()->format('Y-m-d'));
        $sampai = $request->get('sampai', $dari);

        // Jhon | 2026-11-23 20:29 WITA | UBAH: default pencarian tanggal entri; validasi kategori dan filter BPJS.
        $kategori = $request->get('kategori', 'tglentri');
        if (!in_array($kategori, ['tglentri', 'tglreservasi'], true)) {
            $kategori = 'tglentri';
        }
        // Jhon | 2026-11-23 20:33 WITA | UBAH: filter BPJS mengikuti kategori SIMRS; abaikan parameter BPJS lama.
        $filterBpjs = $kategori === 'tglentri' ? 1 : 2;

        $statusFilter = $request->get('status', 'ALL');
        $keyword = trim((string) $request->get('keyword', ''));
        $poliFilter = trim((string) $request->get('poli', ''));
        $dokterFilter = trim((string) $request->get('dokter', ''));
        // Jhon | 2026-11-23 20:34 WITA | UBAH: filter jenis dan Mobile JKN dihapus; parameter lama tidak diterapkan.
        // Jhon | 2026-11-23 18:17 WITA | UBAH: halaman ini khusus reservasi BPJS Kesehatan.
        $kelompokPasienFilter = 'BPJS';
        // Jhon | 2026-11-23 18:22 WITA | UBAH: ukuran batch scroll tetap 25, abaikan parameter lama.
        $perPage = 25;

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }
        /*
        Ambil data SIMRS berdasarkan filter tanggal dan kategori.
     */
        $dataSimrs = $simrs->getRiwayatKontrol(
            $dari,
            $sampai,
            $kategori,
            'undefined'
        );

        /*
        Ambil data BPJS VClaim.
        filter 2 = tanggal rencana kontrol.
        filter 1 = tanggal entri.
       */
        $dataBpjs = $vclaim->listSuratKontrolRange(
            $dari,
            $sampai,
            $filterBpjs
        );

        $riwayatSimrs = data_get($dataSimrs, 'response.riwayat', []);
        $suratKontrolBpjs = data_get($dataBpjs, 'response.list', data_get($dataBpjs, 'response', []));

        if (!is_array($riwayatSimrs)) {
            $riwayatSimrs = [];
        }

        // Jhon | 2026-11-27 10:52 WITA | UBAH: urut tglinput ASC sebelum filter/pagination, sama dengan kolom input; kosong/tidak valid tetap di akhir.
        $riwayatSimrs = collect($riwayatSimrs)->map(function ($row, $index) {
            $created = data_get($row, 'tglinput');
            $timestamp = is_string($created) && trim($created) !== '' ? strtotime($created) : false;
            return ['row' => $row, 'time' => $timestamp === false ? PHP_INT_MAX : $timestamp, 'index' => $index];
        })->sort(function ($a, $b) {
            return ($a['time'] <=> $b['time']) ?: ($a['index'] <=> $b['index']);
        })->pluck('row')->values()->all();

        // Jhon | 2026-11-23 19:38 WITA | TAMBAH: simpan validitas respons, jangan samakan gagal decoding dengan daftar kosong.
        $bpjsListValid = is_array($suratKontrolBpjs) && !isset($dataBpjs['decrypt_error']);
        if (!is_array($suratKontrolBpjs)) {
            $suratKontrolBpjs = [];
        }

        // Jhon | 2026-11-23 20:00 WITA | UBAH: pulihkan indeks kartu/tanggal dari cadangan sebelum perubahan pencocokan.
        /*
        Index BPJS:
        noKartu|tglRencanaKontrol
      */
        $indexBpjs = [];

        foreach ($suratKontrolBpjs as $itemBpjs) {
            $noKartuBpjs = $this->cleanNoKartu(
                data_get($itemBpjs, 'noKartu', data_get($itemBpjs, 'nokartu'))
            );

            $tglRencanaKontrol = data_get($itemBpjs, 'tglRencanaKontrol');

            if (!$noKartuBpjs || !$tglRencanaKontrol) {
                continue;
            }

            $key = $noKartuBpjs . '|' . $tglRencanaKontrol;

            $indexBpjs[$key][] = $itemBpjs;
        }

        $matcher = new \App\Services\SuratKontrolMatcher();
        $hasil = [];

        // Jhon | 2026-11-27 11:14 WITA | TAMBAH: baca status tersimpan sekaligus; kegagalan database tidak dianggap belum diverifikasi.
        $verificationRecords = null;
        try {
            $verificationRecords = \App\Models\SuratKontrolVerification::whereIn('norec', collect($riwayatSimrs)->pluck('norec')->filter()->unique()->values()->all())
                // Jhon | 2026-11-27 12:28 WITA | UBAH: baca field surat BPJS agar jawaban tersimpan terisi saat form dibuka ulang.
                ->get(['norec','permasalahan','keterangan','surat_kontrol_bpjs'])->keyBy('norec');
        } catch (\Throwable $e) { /* Status tidak diketahui sampai tabel/koneksi siap. */ }

        foreach ($riwayatSimrs as $row) {
            // Jhon | 2026-11-24 15:13 WITA | UBAH: syarat tampil norec terisi menggantikan kewajiban tglkontrol.
            $norecWajib = data_get($row, 'norec');
            if (!is_scalar($norecWajib) || in_array(strtoupper(trim((string) $norecWajib)), ['', '-', 'NULL', '0'], true)) {
                continue;
            }
            $noKartuSimrs = $this->cleanNoKartu(
                data_get($row, 'nobpjs') ?: data_get($row, 'nokartu')
            );

            $tanggalReservasi = data_get($row, 'tanggalreservasi');
            $tanggalKontrolSimrs = data_get($row, 'tglkontrol');
            // Jhon | 2026-11-27 10:25 WITA | TAMBAH: format tampilan terpisah dari tanggal pembanding; nilai kosong tidak menjadi tanggal hari ini.
            $formatTanggalSurkon = static function ($value, $format) {
                if (!is_string($value) || trim($value) === '') return null;
                try { return \Carbon\Carbon::parse($value)->format($format); }
                catch (\Throwable $e) { return null; }
            };


            $tanggalPembanding = null;

            // Jhon | 2026-11-23 20:00 WITA | UBAH: pulihkan tanggal fallback, kandidat pertama, dan status keberadaan surat seperti awal.
            try {
                if (!empty($tanggalKontrolSimrs)) {
                    $tanggalPembanding = \Carbon\Carbon::parse($tanggalKontrolSimrs)->format('Y-m-d');
                } elseif (!empty($tanggalReservasi)) {
                    $tanggalPembanding = \Carbon\Carbon::parse($tanggalReservasi)->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                $tanggalPembanding = null;
            }

            $noSuratSimrs = data_get($row, 'nosuratkontrol') ?: data_get($row, 'nosurat');

            $key = $noKartuSimrs . '|' . $tanggalPembanding;

            // Jhon | 2026-11-28 14:50 WITA | UBAH: cari kandidat kartu/tanggal yang memiliki nomor surat, bukan kandidat kosong pertama.
            $bpjsMatch = $indexBpjs[$key][0] ?? null;
            foreach ($indexBpjs[$key] ?? [] as $candidate) {
                if (!empty(data_get($candidate, 'noSuratKontrol', data_get($candidate, 'no_surat_kontrol')))) {
                    $bpjsMatch = $candidate;
                    break;
                }
            }
            $bpjsComparisonValid = $bpjsListValid && in_array((string) data_get($dataBpjs, 'metaData.code', data_get($dataBpjs, 'metadata.code', '')), ['200', '201'], true);


            $noSuratBpjs = $bpjsMatch
                ? data_get($bpjsMatch, 'noSuratKontrol', data_get($bpjsMatch, 'no_surat_kontrol'))
                : null;

            if (!$noKartuSimrs || !$tanggalPembanding) {
                $status = 'DATA_TIDAK_LENGKAP';
                $statusLabel = 'Data tidak lengkap';
            } elseif (!empty($noSuratSimrs)) {
                // Jhon | 2026-11-28 14:50 WITA | UBAH: pisahkan tidak ditemukan dari kegagalan pemeriksaan BPJS.
                if (!$bpjsComparisonValid) {
                    $status = 'BPJS_GAGAL';
                    $statusLabel = 'Ada di SIMRS, pengecekan BPJS gagal';
                } elseif (!empty($noSuratBpjs)) {
                    $status = 'ADA_DI_SIMRS';
                    $statusLabel = 'Ada di SIMRS dan BPJS';
                } else {
                    $status = 'ADA_SIMRS_TIDAK_BPJS';
                    $statusLabel = 'Ada di SIMRS, tidak ada di BPJS';
                }
            } elseif (!empty($noSuratBpjs)) {
                $status = 'ADA_DI_BPJS_BELUM_SIMRS';
                $statusLabel = 'Ada di BPJS, belum masuk SIMRS';
            } else {
                $status = 'BELUM_ADA_SURAT_KONTROL';
                $statusLabel = 'Belum ada surat kontrol';
            }

            $kandidatIdentitas = $indexBpjs[$key] ?? [];

            // Jhon | 2026-11-23 20:22 WITA | TAMBAH: jelaskan bukti yang tidak cocok tanpa mengubah aturan pencocokan awal.
            $alasan = [];
            $kodeResponsBpjs = (string) data_get($dataBpjs, 'metaData.code', data_get($dataBpjs, 'metadata.code', ''));
            if (!$bpjsListValid || !in_array($kodeResponsBpjs, ['200', '201'], true)) {
                $alasan[] = 'Respons BPJS gagal atau tidak dapat dibaca; kecocokan belum dapat dipastikan, bukan bukti surat tidak ada.';
            } elseif (!$noKartuSimrs || !$tanggalPembanding) {
                if (!$noKartuSimrs) $alasan[] = 'Nomor kartu SIMRS kosong/tidak memiliki angka sehingga tidak bisa dibandingkan.';
                if (!$tanggalPembanding) $alasan[] = 'Tanggal pembanding SIMRS kosong/tidak valid sehingga tidak bisa dibandingkan.';
            } elseif ($bpjsMatch) {
                $alasan[] = 'Nomor kartu SIMRS dan BPJS sama; tanggal pembanding SIMRS ' . $tanggalPembanding . ' sama dengan tanggal rencana kontrol BPJS ' . data_get($bpjsMatch, 'tglRencanaKontrol') . '.';
                if (!$noSuratBpjs) $alasan[] = 'Pasangan kartu/tanggal ditemukan, tetapi nomor surat pada kandidat BPJS pertama kosong.';
                if (!$noSuratSimrs) $alasan[] = 'Nomor surat SIMRS belum terisi.';
                if (count($kandidatIdentitas) > 1) $alasan[] = 'Ada beberapa kandidat kartu/tanggal yang sama; aturan awal mengambil kandidat pertama.';
            } else {
                $tanggalKartuSama = [];
                foreach ($suratKontrolBpjs as $candidate) {
                    $kartuCandidate = $this->cleanNoKartu(data_get($candidate, 'noKartu', data_get($candidate, 'nokartu')));
                    if ($kartuCandidate === $noKartuSimrs) {
                        $tanggalKartuSama[] = (string) data_get($candidate, 'tglRencanaKontrol', '(kosong)');
                    }
                }
                if ($tanggalKartuSama) {
                    $alasan[] = 'Nomor kartu ditemukan di BPJS, tetapi tanggal tidak sama. SIMRS: ' . $tanggalPembanding . '; tanggal BPJS untuk kartu ini: ' . implode(', ', array_unique($tanggalKartuSama)) . '.';
                } else {
                    $alasan[] = 'Nomor kartu SIMRS tidak ditemukan pada daftar BPJS dalam periode filter. Tidak ada pasangan yang dapat dipakai untuk menyimpulkan tanggal berbeda; belum berarti nomor kartu SIMRS salah.';
                }
                if ($noSuratSimrs) $alasan[] = 'Nomor surat SIMRS terisi, tetapi tidak ditemukan surat BPJS dengan nomor kartu dan tanggal pembanding yang sama dalam periode filter.';
            }

            // Jhon | 2026-11-27 11:06 WITA | TAMBAH: grant reservasi UUID dari sumber SIMRS, terikat sesi selama dua jam.
            $verificationToken = null;
            // Jhon | 2026-11-27 11:09 WITA | UBAH: grant hanya untuk surat kosong pada kedua sumber dan hasil BPJS yang valid; gagal pencarian bukan bukti kosong.
            $verificationAllowed = $status === 'BELUM_ADA_SURAT_KONTROL'
                && $bpjsListValid && in_array($kodeResponsBpjs, ['200', '201'], true)
                && !collect($kandidatIdentitas)->contains(function ($candidate) use ($matcher) { return $matcher->number($candidate) !== ''; });
            if ($verificationAllowed && !\Illuminate\Support\Facades\Validator::make(['norec'=>$norecWajib], ['norec'=>'required|uuid'])->fails()) {
                $verificationToken = \Illuminate\Support\Facades\Crypt::encryptString(json_encode([
                    'norec'=>(string) $norecWajib, 'session'=>hash('sha256', $request->session()->getId()), 'expires'=>time()+7200,
                ]));
            }
            $hasil[] = [
                'verification_token' => $verificationToken,
                'verification_norec' => (string) $norecWajib,
                // Jhon | 2026-11-27 11:14 WITA | TAMBAH: null membedakan gagal baca dari belum tersimpan.
                'verification_saved' => $verificationRecords === null ? null : $verificationRecords->has((string) $norecWajib),
                'verification_problem' => $verificationRecords === null ? null : data_get($verificationRecords->get((string) $norecWajib), 'permasalahan'),
                // Jhon | 2026-11-27 12:28 WITA | TAMBAH: null berarti catatan lama belum menjawab.
                'verification_bpjs' => $verificationRecords === null ? null : data_get($verificationRecords->get((string) $norecWajib), 'surat_kontrol_bpjs'),
                'verification_note' => $verificationRecords === null ? null : data_get($verificationRecords->get((string) $norecWajib), 'keterangan'),
                'status' => $status,
                'status_label' => $statusLabel,
                // Jhon | 2026-11-23 20:22 WITA | TAMBAH: rincian alasan untuk klik status.
                'alasan_pembandingan' => $alasan,
                // Jhon | 2026-11-23 19:33 WITA | TAMBAH: rincian baca-saja untuk diagnosis kartu/tanggal, nomor surat, dan poli.
                // Jhon | 2026-11-23 19:38 WITA | TAMBAH: hitungan respons dan indeks membantu membedakan data API kosong dari gagal pencocokan.
                'jumlah_bpjs' => count($suratKontrolBpjs),
                'jumlah_bpjs_terindeks' => array_sum(array_map('count', $indexBpjs)),
                // Jhon | 2026-11-23 19:42 WITA | TAMBAH: tampilkan SEP yang benar-benar dibandingkan.
                // Jhon | 2026-11-23 21:06 WITA | UBAH: SEP asal SIMRS berasal dari field nosepasal, bukan nomor SEP lain.
                'sep_simrs' => data_get($row, 'nosepasal'),
                // Jhon | 2026-11-28 08:49 WITA | TAMBAH: teruskan penanda pembuatan dari respons SIMRS, tanpa menyimpulkan dari nomor surat.
                'buatreservasi' => data_get($row, 'buatreservasi'),
                'buatsurkon' => data_get($row, 'buatsurkon'),
                'kodepoli_simrs' => data_get($row, 'kodepolibpjs'),
                'kandidat_bpjs' => array_map(function ($candidate) use ($matcher) {
                    return [
                        'nomor' => $matcher->number($candidate),
                        'sep' => $matcher->sep($candidate),
                        'tanggal' => data_get($candidate, 'tglRencanaKontrol'),
                        'poli' => data_get($candidate, 'namaPoliTujuan') ?: data_get($candidate, 'namaPoli'),
                        'kodepoli' => data_get($candidate, 'poliTujuan') ?: data_get($candidate, 'kodePoli') ?: data_get($candidate, 'kodepoli'),
                    ];
                }, $kandidatIdentitas),

                'nocm' => data_get($row, 'nocm'),
                'namapasien' => data_get($row, 'namapasien'),
                'nobpjs' => $noKartuSimrs,
                'noreservasi' => data_get($row, 'noreservasi'),
                // Jhon | 2026-11-27 10:52 WITA | UBAH: tanggal input memakai field tglinput sesuai contoh respons pengguna, bukan tglentry/created_at.
                'tanggalreservasi' => $tanggalReservasi,
                'tanggalreservasi_tampilan' => $formatTanggalSurkon($tanggalReservasi, 'Y-m-d'),
                'tanggalinput_tampilan' => $formatTanggalSurkon(data_get($row, 'tglinput'), 'Y-m-d H:i:s'),
                'tanggal_pembanding' => $tanggalPembanding,

                'poli' => data_get($row, 'poli') ?: data_get($row, 'namaruangan'),
                'dokter' => data_get($row, 'namadokter') ?: data_get($row, 'namalengkap'),
                'jenis' => data_get($row, 'jenis'),
                'type' => data_get($row, 'type'),
                'tipepasien' => data_get($row, 'tipepasien'),
                'ismobilejkn' => data_get($row, 'ismobilejkn'),
                'kelompokpasien' => data_get($row, 'kelompokpasien'),
                'noantrianpoli' => data_get($row, 'noantrianpoli'),

                'nosurat_simrs' => $noSuratSimrs,
                'nosurat_bpjs' => $noSuratBpjs,

                'no_sep_asal_kontrol_bpjs' => $bpjsMatch
                    ? data_get($bpjsMatch, 'noSepAsalKontrol', data_get($bpjsMatch, 'noSEP', data_get($bpjsMatch, 'noSep')))
                    : null,
            ];
        }

        /*
        Filter hasil setelah data dibandingkan.
       */
        $normalizeKelompokPasien = function ($value) {
            $value = strtoupper(trim((string) $value));
            $value = preg_replace('/\s+/', ' ', $value);
            $value = preg_replace('/\s*\/\s*/', '/', $value);

            return $value;
        };
        // Jhon | 2026-11-23 18:17 WITA | UBAH: batasi BPJS sebelum filter, ringkasan, dan pagination.
        $hasilCollection = collect($hasil)
            ->filter(function ($item) use ($normalizeKelompokPasien) {
                return $normalizeKelompokPasien($item['kelompokpasien'] ?? '') === 'BPJS';
            })
            ->when($statusFilter !== 'ALL', function ($items) use ($statusFilter) {
                // Jhon | 2026-11-27 11:35 WITA | TAMBAH: dua filter verifikasi sebelum hitungan/pagination, null tidak dianggap belum.
                // Jhon | 2026-11-27 12:34 WITA | TAMBAH: filter jawaban petugas tersimpan, terpisah dari pencocokan otomatis BPJS.
                if (in_array($statusFilter, ['VERIFIKASI_SURAT_ADA','VERIFIKASI_SURAT_TIDAK'], true)) {
                    $answer = $statusFilter === 'VERIFIKASI_SURAT_ADA' ? 'ada' : 'tidak';
                    return $items->filter(function ($item) use ($answer) {
                        return ($item['verification_saved'] ?? null) === true && ($item['verification_bpjs'] ?? null) === $answer;
                    });
                }
                if (in_array($statusFilter, ['BELUM_SURAT_SUDAH_VERIFIKASI', 'BELUM_SURAT_BELUM_VERIFIKASI'], true)) {
                    $verified = $statusFilter === 'BELUM_SURAT_SUDAH_VERIFIKASI';
                    return $items->filter(function ($item) use ($verified) {
                        return $item['status'] === 'BELUM_ADA_SURAT_KONTROL'
                            && !empty($item['verification_token'])
                            && ($item['verification_saved'] ?? null) === $verified;
                    });
                }
                return $items->where('status', $statusFilter);
            })
            ->when($keyword !== '', function ($items) use ($keyword) {
                $keywordLower = strtolower($keyword);

                return $items->filter(function ($item) use ($keywordLower) {
                    return str_contains(strtolower((string) ($item['namapasien'] ?? '')), $keywordLower)
                        || str_contains(strtolower((string) ($item['nocm'] ?? '')), $keywordLower)
                        || str_contains(strtolower((string) ($item['nobpjs'] ?? '')), $keywordLower)
                        || str_contains(strtolower((string) ($item['noreservasi'] ?? '')), $keywordLower)
                        || str_contains(strtolower((string) ($item['nosurat_simrs'] ?? '')), $keywordLower)
                        || str_contains(strtolower((string) ($item['nosurat_bpjs'] ?? '')), $keywordLower);
                });
            })
            ->when($poliFilter !== '', function ($items) use ($poliFilter) {
                return $items->filter(function ($item) use ($poliFilter) {
                    return str_contains(
                        strtolower((string) ($item['poli'] ?? '')),
                        strtolower($poliFilter)
                    );
                });
            })
            ->when($dokterFilter !== '', function ($items) use ($dokterFilter) {
                return $items->filter(function ($item) use ($dokterFilter) {
                    return str_contains(
                        strtolower((string) ($item['dokter'] ?? '')),
                        strtolower($dokterFilter)
                    );
                });
            })
            ->when($kelompokPasienFilter !== '', function ($items) use ($kelompokPasienFilter, $normalizeKelompokPasien) {
                $filter = $normalizeKelompokPasien($kelompokPasienFilter);

                return $items->filter(function ($item) use ($filter, $normalizeKelompokPasien) {
                    $kelompokPasien = $normalizeKelompokPasien($item['kelompokpasien'] ?? '');

                    return $kelompokPasien === $filter;
                });
            })
            ->values();

        $summary = [
            'total_simrs' => $hasilCollection->count(),
            // Jhon | 2026-11-23 18:31 WITA | UBAH: ringkasan sesuai, berbeda, ambigu, belum verifikasi, dan gagal.
            // Jhon | 2026-11-28 14:50 WITA | TAMBAH: rekap selisih SIMRS/BPJS dan kegagalan pengecekan.
            'ada_simrs_tidak_bpjs' => $hasilCollection->where('status', 'ADA_SIMRS_TIDAK_BPJS')->count(),
            'bpjs_gagal' => $hasilCollection->where('status', 'BPJS_GAGAL')->count(),
            'ada_di_simrs' => $hasilCollection->where('status', 'ADA_DI_SIMRS')->count(),
            'tidak_sesuai' => $hasilCollection->whereIn('status', ['TIDAK_SESUAI', 'TIDAK_DITEMUKAN_BPJS'])->count(),
            'belum_verifikasi' => $hasilCollection->whereIn('status', ['AMBIGU', 'BELUM_TERVERIFIKASI', 'BPJS_GAGAL'])->count(),
            'ada_di_bpjs_belum_simrs' => $hasilCollection->where('status', 'ADA_DI_BPJS_BELUM_SIMRS')->count(),
            'belum_ada_surat_kontrol' => $hasilCollection->where('status', 'BELUM_ADA_SURAT_KONTROL')->count(),
            'data_tidak_lengkap' => $hasilCollection->where('status', 'DATA_TIDAK_LENGKAP')->count(),
        ];
        $page = LengthAwarePaginator::resolveCurrentPage();

        $hasil = new LengthAwarePaginator(
            $hasilCollection->forPage($page, $perPage)->values(),
            $hasilCollection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
            ]
        );

        $hasil->appends($request->except(['page', 'jenis', 'ismobilejkn']));

        $filters = [
            'dari' => $dari,
            'sampai' => $sampai,
            'kategori' => $kategori,
            'filter_bpjs' => $filterBpjs,
            'status' => $statusFilter,
            'keyword' => $keyword,
            'poli' => $poliFilter,
            'dokter' => $dokterFilter,
            'kelompok_pasien' => $kelompokPasienFilter,
            'per_page' => $perPage,
        ];

        // Jhon | 2026-11-27 16:07 WITA | TAMBAH: ekspor seluruh hasil filter sebelum pagination; tidak hanya baris DOM infinite scroll.
        if (in_array($request->query('export'), ['excel','print'], true)) {
            // Jhon | 2026-11-27 16:08 WITA | TAMBAH: validasi kedua sumber, termasuk hasil kosong tanpa iterasi baris.
            $exportBpjsCode = (string) data_get($dataBpjs, 'metaData.code', data_get($dataBpjs, 'metadata.code', ''));
            $exportSimrsCode = (string) data_get($dataSimrs, 'metaData.code', data_get($dataSimrs, 'metadata.code', ''));
            if (!$bpjsListValid || !in_array($exportBpjsCode, ['200','201'], true) || $exportSimrsCode !== '200') {
                return response('Ekspor dibatalkan: hasil pemeriksaan SIMRS/BPJS belum valid. Muat ulang laporan.', 502)->header('Cache-Control','private, no-store');
            }
            return app(\App\Services\SuratKontrolExportService::class)->export($hasilCollection, $filters, $request->query('export'));
        }

        return view('bpjs-antrean.cek-surat-kontrol', compact(
            'dataSimrs',
            'dataBpjs',
            'hasil',
            'summary',
            'filters'
        ));
    }

    private function mergeAntreanBpjsDenganSimrs(array $itemsBpjs, array $itemsSimrs, string $tanggal): array
    {
        $hasil = [];
        $kodeBookingBpjs = [];

        foreach ($itemsBpjs as $itemBpjs) {
            $kodebookingBpjs = strtolower(trim((string) data_get(
                $itemBpjs,
                'kodebooking',
                data_get($itemBpjs, 'kode_booking', '')
            )));

            $simrsMatch = $this->cariDataSimrsYangCocok($itemBpjs, $itemsSimrs);

            $itemBpjs['sumber_data'] = $simrsMatch ? 'BPJS DAN SIMRS' : 'BPJS';

            if ($simrsMatch) {
                $namaSimrs = $this->ambilNamaPasienSimrs($simrsMatch);

                if ($namaSimrs !== '') {
                    $itemBpjs['namapasien'] = $namaSimrs;
                }
            }

            if ($kodebookingBpjs !== '') {
                $kodeBookingBpjs[] = $kodebookingBpjs;
            }

            $hasil[] = $itemBpjs;
        }

        foreach ($itemsSimrs as $itemSimrs) {
            $kodebookingSimrs = strtolower(trim((string) data_get(
                $itemSimrs,
                'kodebooking',
                data_get(
                    $itemSimrs,
                    'kode_booking',
                    data_get(
                        $itemSimrs,
                        'nobooking',
                        data_get(
                            $itemSimrs,
                            'noreservasi',
                            data_get(
                                $itemSimrs,
                                'kode_booking_bpjs',
                                data_get($itemSimrs, 'kodebooking_bpjs', '')
                            )
                        )
                    )
                )
            )));

            /*
        INI BAGIAN PENTING:
        Kalau kodebooking SIMRS sudah ada di BPJS,
        jangan tampilkan lagi sebagai data SIMRS terpisah.
        */
            if ($kodebookingSimrs !== '' && in_array($kodebookingSimrs, $kodeBookingBpjs, true)) {
                continue;
            }

            $namaPasienSimrs = $this->ambilNamaPasienSimrs($itemSimrs);

            $hasil[] = [
                'kodebooking' => $kodebookingSimrs ?: null,
                'noantrean' => data_get(
                    $itemSimrs,
                    'noantrean',
                    data_get(
                        $itemSimrs,
                        'noantrian',
                        data_get($itemSimrs, 'no_antrian', '-')
                    )
                ),
                'namapasien' => $namaPasienSimrs ?: '-',
                'nokapst' => data_get(
                    $itemSimrs,
                    'nokapst',
                    data_get(
                        $itemSimrs,
                        'nokartu',
                        data_get(
                            $itemSimrs,
                            'nomorkartu',
                            data_get($itemSimrs, 'no_kartu', '-')
                        )
                    )
                ),
                'nomorkartu' => data_get(
                    $itemSimrs,
                    'nomorkartu',
                    data_get(
                        $itemSimrs,
                        'nokapst',
                        data_get(
                            $itemSimrs,
                            'nokartu',
                            data_get($itemSimrs, 'no_kartu', '-')
                        )
                    )
                ),
                'namapoli' => data_get(
                    $itemSimrs,
                    'namapoli',
                    data_get(
                        $itemSimrs,
                        'nama_poli',
                        data_get(
                            $itemSimrs,
                            'namaruangan',
                            data_get($itemSimrs, 'poli', '-')
                        )
                    )
                ),
                'namadokter' => data_get(
                    $itemSimrs,
                    'namadokter',
                    data_get(
                        $itemSimrs,
                        'nama_dokter',
                        data_get(
                            $itemSimrs,
                            'dokter',
                            data_get($itemSimrs, 'namadokterpemeriksa', '-')
                        )
                    )
                ),
                'jampraktek' => data_get(
                    $itemSimrs,
                    'jampraktek',
                    data_get(
                        $itemSimrs,
                        'jam_praktek',
                        data_get($itemSimrs, 'jam', '-')
                    )
                ),
                'jenis' => 'SIMRS',
                'status' => data_get(
                    $itemSimrs,
                    'status',
                    data_get(
                        $itemSimrs,
                        'statusantrean',
                        data_get($itemSimrs, 'label_statusperiksa', 'Belum dilayani')
                    )
                ),
                'sumber_data' => 'SIMRS',
                'tanggal' => $tanggal,
                'is_simrs_only' => true,
            ];
        }

        return $hasil;
    }

    private function cleanNoKartu($value): ?string
    {
        if (!$value) {
            return null;
        }

        return preg_replace('/\D/', '', (string) $value);
    }
    public function ajaxCekSuratKontrolByNoKartu(Request $request, BpjsVclaimService $vclaim)
    {
        $noKartu = $this->cleanNoKartu($request->get('no_kartu'));
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $filter = (int) $request->get('filter', 2);

        if (!$noKartu) {
            return response()->json([
                'success' => false,
                'message' => 'No kartu BPJS kosong.',
                'data' => null,
            ], 422);
        }

        try {
            $date = Carbon::parse($tanggal);
            $bulan = $date->format('m');
            $tahun = $date->format('Y');

            $result = $vclaim->getSuratKontrolByNoKartu(
                $noKartu,
                $bulan,
                $tahun,
                $filter
            );

            return response()->json([
                'success' => true,
                'type' => 'no_kartu',
                'no_kartu' => $noKartu,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'filter' => $filter,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function ajaxCekSuratKontrolByNoSurat(Request $request, BpjsVclaimService $vclaim)
    {
        $noSuratKontrol = trim((string) $request->get('no_surat'));

        if ($noSuratKontrol === '') {
            return response()->json([
                'success' => false,
                'message' => 'No surat kontrol kosong.',
                'data' => null,
            ], 422);
        }

        try {
            $result = $vclaim->getSuratKontrolByNoSurat($noSuratKontrol);

            return response()->json([
                'success' => true,
                'type' => 'no_surat',
                'no_surat' => $noSuratKontrol,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
    private function buatStatistikAntrean(
        array $itemsTanggalBpjs,
        array $itemsTanggalGabungan,
        array $riwayatSimrs
    ): array {
        $statistik = [
            'total_simrs' => count($riwayatSimrs),
            'total_bpjs' => count($itemsTanggalBpjs),
            'total_gabungan' => count($itemsTanggalGabungan),

            'jenis' => [
                'mobile_jkn' => 0,
                'bridging' => 0,
                'onsite' => 0,
                'lainnya' => 0,
            ],

            'mjkn_status' => [
                'selesai' => 0,
                'sedang' => 0,
                'belum' => 0,
                'batal' => 0,
                'lainnya' => 0,
            ],
        ];

        foreach ($itemsTanggalGabungan as $item) {
            $jenis = $this->klasifikasiJenisAntrean($item);

            if (!array_key_exists($jenis, $statistik['jenis'])) {
                $jenis = 'lainnya';
            }

            $statistik['jenis'][$jenis]++;

            if ($jenis === 'mobile_jkn') {
                $status = $this->klasifikasiStatusAntrean($item);

                if (!array_key_exists($status, $statistik['mjkn_status'])) {
                    $status = 'lainnya';
                }

                $statistik['mjkn_status'][$status]++;
            }
        }

        return $statistik;
    }
    private function klasifikasiJenisAntrean(array $item): string
    {
        $jenisText = strtolower(trim($this->ambilJenisAntrean($item)));

        if ($jenisText === '') {
            return 'lainnya';
        }

        if (
            str_contains($jenisText, 'mobile') ||
            str_contains($jenisText, 'mjkn') ||
            str_contains($jenisText, 'jkn')
        ) {
            return 'mobile_jkn';
        }

        if (
            str_contains($jenisText, 'bridging') ||
            str_contains($jenisText, 'simrs') ||
            str_contains($jenisText, 'ws') ||
            str_contains($jenisText, 'web service')
        ) {
            return 'bridging';
        }

        if (
            str_contains($jenisText, 'onsite') ||
            str_contains($jenisText, 'loket') ||
            str_contains($jenisText, 'manual') ||
            str_contains($jenisText, 'offline')
        ) {
            return 'onsite';
        }

        return 'lainnya';
    }

    private function klasifikasiStatusAntrean(array $item): string
    {
        $statusText = strtolower(trim($this->ambilStatusAntrean($item)));

        if ($statusText === '') {
            return 'lainnya';
        }

        if (str_contains($statusText, 'selesai')) {
            return 'selesai';
        }

        if (str_contains($statusText, 'sedang')) {
            return 'sedang';
        }

        if (str_contains($statusText, 'belum')) {
            return 'belum';
        }

        if (str_contains($statusText, 'batal')) {
            return 'batal';
        }

        return 'lainnya';
    }

    private function ambilJenisAntrean(array $item): string
    {
        $fields = [
            'jenis',
            'jenisantrean',
            'jenis_antrean',
            'sumberdata',
            'sumber_data',
            'sumber',
            'asalantrean',
            'asal_antrian',
            'asalpendaftaran',
            'asal_pendaftaran',
            'method',
            'channel',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return (string) $value;
            }
        }

        return '';
    }

    private function ambilStatusAntrean(array $item): string
    {
        $fields = [
            'status',
            'statusantrean',
            'status_antrean',
            'keterangan',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return (string) $value;
            }
        }

        return '';
    }
    private function ambilKodeBooking(array $item): string
    {
        $fields = [
            'kodebooking',
            'kode_booking',
            'kodeBooking',
            'nobooking',
            'no_booking',
            'norec_apd',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return '';
    }

    private function ambilStatusSimrs(array $item): string
    {
        $fields = [
            'status',
            'statusreservasi',
            'status_reservasi',
            'label_statusperiksa',
            'statusperiksa',
            'status_periksa',
            'statuspasien',
            'keterangan',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return '';
    }
    private function ambilKodeBookingBpjs(array $item): string
    {
        $fields = [
            'kodebooking',
            'kode_booking',
            'kodeBooking',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return '';
    }

    private function ambilNoReservasiSimrs(array $item): string
    {
        $fields = [
            'noreservasi',
            'no_reservasi',
            'noReservasi',
            'nobooking',
            'no_booking',
            'kodebooking',
            'kode_booking',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return '';
    }
    private function ambilNoKartuBpjs(array $item): string
    {
        $fields = [
            'nomorkartu',
            'nokapst',
            'noKartu',
            'nokartu',
            'no_kartu',
            'nobpjs',
        ];

        foreach ($fields as $field) {
            $value = data_get($item, $field);

            if ($value !== null && trim((string) $value) !== '') {
                return preg_replace('/\D/', '', trim((string) $value));
            }
        }

        return '';
    }
    private function ambilNamaPasienSimrs(array $item): string
    {
        return trim(
            (string) data_get(
                $item,
                'namapasien',
                data_get(
                    $item,
                    'nama_pasien',
                    data_get(
                        $item,
                        'nama',
                        data_get(
                            $item,
                            'namalengkap',
                            data_get(
                                $item,
                                'nama_lengkap',
                                data_get(
                                    $item,
                                    'pasien.namapasien',
                                    data_get(
                                        $item,
                                        'pasien.nama',
                                        data_get(
                                            $item,
                                            'registrasi.namapasien',
                                            data_get($item, 'pasien_nama', '')
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }
    private function paginateArray(array $items, int $perPage = 20, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage($pageName);

        $collection = collect($items);

        $currentItems = $collection
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => $pageName,
            ]
        );
    }
    private function cekAdaDiSimrs(array $itemBpjs, array $itemsSimrs): bool
    {
        $kodebookingBpjs = strtolower(trim((string) data_get(
            $itemBpjs,
            'kodebooking',
            data_get($itemBpjs, 'kode_booking', '')
        )));

        $noRmBpjs = preg_replace('/\D/', '', (string) data_get($itemBpjs, 'norekammedis', ''));

        $noKartuBpjs = preg_replace('/\D/', '', (string) data_get(
            $itemBpjs,
            'nokapst',
            data_get(
                $itemBpjs,
                'nomorkartu',
                data_get(
                    $itemBpjs,
                    'nokartu',
                    data_get($itemBpjs, 'no_kartu', '')
                )
            )
        ));

        $nikBpjs = preg_replace('/\D/', '', (string) data_get($itemBpjs, 'nik', ''));

        $tanggalBpjs = trim((string) data_get(
            $itemBpjs,
            'tanggal',
            data_get($itemBpjs, 'tglreservasi', '')
        ));

        $kodepoliBpjs = strtoupper(trim((string) data_get(
            $itemBpjs,
            'kodepoli',
            data_get(
                $itemBpjs,
                'kode_poli',
                data_get($itemBpjs, 'namapoli', '')
            )
        )));

        $noAntreanBpjs = $this->normalisasiNoAntreanCek(data_get(
            $itemBpjs,
            'noantrean',
            data_get(
                $itemBpjs,
                'noantrian',
                data_get($itemBpjs, 'no_antrian', '')
            )
        ));

        return collect($itemsSimrs)->contains(function ($itemSimrs) use (
            $kodebookingBpjs,
            $noRmBpjs,
            $noKartuBpjs,
            $nikBpjs,
            $tanggalBpjs,
            $kodepoliBpjs,
            $noAntreanBpjs
        ) {
            $kodebookingSimrs = strtolower(trim((string) data_get(
                $itemSimrs,
                'kodebooking',
                data_get(
                    $itemSimrs,
                    'kode_booking',
                    data_get(
                        $itemSimrs,
                        'nobooking',
                        data_get(
                            $itemSimrs,
                            'noreservasi',
                            data_get(
                                $itemSimrs,
                                'kode_booking_bpjs',
                                data_get($itemSimrs, 'kodebooking_bpjs', '')
                            )
                        )
                    )
                )
            )));

            /*
        1. Kalau kode booking sama, langsung dianggap BPJS DAN SIMRS.
        Contoh:
        BPJS  = 42bdf88
        SIMRS = 42bdf88
        */
            if (
                $kodebookingBpjs !== ''
                && $kodebookingSimrs !== ''
                && $kodebookingBpjs === $kodebookingSimrs
            ) {
                return true;
            }

            $noRmSimrs = preg_replace('/\D/', '', (string) data_get(
                $itemSimrs,
                'norekammedis',
                data_get(
                    $itemSimrs,
                    'nocm',
                    data_get(
                        $itemSimrs,
                        'norm',
                        data_get($itemSimrs, 'no_rm', '')
                    )
                )
            ));

            $noKartuSimrs = preg_replace('/\D/', '', (string) data_get(
                $itemSimrs,
                'nokapst',
                data_get(
                    $itemSimrs,
                    'nokartu',
                    data_get(
                        $itemSimrs,
                        'nomorkartu',
                        data_get($itemSimrs, 'no_kartu', '')
                    )
                )
            ));

            $nikSimrs = preg_replace('/\D/', '', (string) data_get(
                $itemSimrs,
                'nik',
                data_get(
                    $itemSimrs,
                    'noidentitas',
                    data_get($itemSimrs, 'no_ktp', '')
                )
            ));

            $tanggalSimrs = trim((string) data_get(
                $itemSimrs,
                'tanggal',
                data_get(
                    $itemSimrs,
                    'tglreservasi',
                    data_get($itemSimrs, 'tgl_reservasi', '')
                )
            ));

            $kodepoliSimrs = strtoupper(trim((string) data_get(
                $itemSimrs,
                'kodepoli',
                data_get(
                    $itemSimrs,
                    'kode_poli',
                    data_get(
                        $itemSimrs,
                        'namapoli',
                        data_get(
                            $itemSimrs,
                            'nama_poli',
                            data_get($itemSimrs, 'namaruangan', '')
                        )
                    )
                )
            )));

            $noAntreanSimrs = $this->normalisasiNoAntreanCek(data_get(
                $itemSimrs,
                'noantrean',
                data_get(
                    $itemSimrs,
                    'noantrian',
                    data_get(
                        $itemSimrs,
                        'no_antrian',
                        data_get($itemSimrs, 'antrian', '')
                    )
                )
            ));

            /*
        2. Cocokkan identitas pasien.
        Minimal salah satu harus sama.
        */
            $identitasSama =
                ($noKartuBpjs !== '' && $noKartuSimrs !== '' && $noKartuBpjs === $noKartuSimrs)
                || ($noRmBpjs !== '' && $noRmSimrs !== '' && $noRmBpjs === $noRmSimrs)
                || ($nikBpjs !== '' && $nikSimrs !== '' && $nikBpjs === $nikSimrs);

            if (!$identitasSama) {
                return false;
            }

            /*
        3. Kalau tanggal tersedia di dua-duanya, tanggal harus sama.
        */
            if ($tanggalBpjs !== '' && $tanggalSimrs !== '' && $tanggalBpjs !== $tanggalSimrs) {
                return false;
            }

            /*
        4. Cocokkan poli.
        Contoh:
        BPJS  = ANA / 017
        SIMRS = POLI ANAK / 017
        */
            $poliSama = false;

            if ($kodepoliBpjs !== '' && $kodepoliSimrs !== '') {
                $poliSama =
                    $kodepoliBpjs === $kodepoliSimrs
                    || str_contains($kodepoliSimrs, $kodepoliBpjs)
                    || str_contains($kodepoliBpjs, $kodepoliSimrs);
            }

            /*
        5. Cocokkan nomor antrean.
        Contoh:
        BPJS  = RL85
        SIMRS = 017-085
        Hasil normalisasi sama-sama 85.
        */
            $antreanSama = false;

            if ($noAntreanBpjs !== '' && $noAntreanSimrs !== '') {
                $antreanSama = (int) $noAntreanBpjs === (int) $noAntreanSimrs;
            }

            /*
        Data dianggap ada di BPJS DAN SIMRS jika:
        - identitas pasien sama
        - dan poli sama atau nomor antrean sama
        */
            return $identitasSama && ($poliSama || $antreanSama);
        });
    }
    private function normalisasiNoAntreanCek($value): string
    {
        $value = strtoupper(trim((string) $value));

        if ($value === '') {
            return '';
        }

        /*
        ANA-003 menjadi 003
        SAR-014 menjadi 014
        */
        if (str_contains($value, '-')) {
            $parts = explode('-', $value);
            $value = end($parts);
        }

        $angka = preg_replace('/\D/', '', $value);

        if ($angka === '') {
            return '';
        }

        return ltrim($angka, '0') ?: '0';
    }
    private function cariDataSimrsYangCocok(array $itemBpjs, array $itemsSimrs): ?array
    {
        $kodebookingBpjs = strtolower(trim((string) data_get(
            $itemBpjs,
            'kodebooking',
            data_get($itemBpjs, 'kode_booking', '')
        )));

        $noRmBpjs = preg_replace('/\D/', '', (string) data_get($itemBpjs, 'norekammedis', ''));

        $noKartuBpjs = preg_replace('/\D/', '', (string) data_get(
            $itemBpjs,
            'nokapst',
            data_get(
                $itemBpjs,
                'nomorkartu',
                data_get(
                    $itemBpjs,
                    'nokartu',
                    data_get($itemBpjs, 'no_kartu', '')
                )
            )
        ));

        $nikBpjs = preg_replace('/\D/', '', (string) data_get($itemBpjs, 'nik', ''));

        $tanggalBpjs = trim((string) data_get(
            $itemBpjs,
            'tanggal',
            data_get($itemBpjs, 'tglreservasi', '')
        ));

        $kodepoliBpjs = strtoupper(trim((string) data_get(
            $itemBpjs,
            'kodepoli',
            data_get(
                $itemBpjs,
                'kode_poli',
                data_get($itemBpjs, 'namapoli', '')
            )
        )));

        $noAntreanBpjs = $this->normalisasiNoAntreanCek(data_get(
            $itemBpjs,
            'noantrean',
            data_get(
                $itemBpjs,
                'noantrian',
                data_get($itemBpjs, 'no_antrian', '')
            )
        ));

        return collect($itemsSimrs)->first(function ($itemSimrs) use (
            $kodebookingBpjs,
            $noRmBpjs,
            $noKartuBpjs,
            $nikBpjs,
            $tanggalBpjs,
            $kodepoliBpjs,
            $noAntreanBpjs
        ) {
            $kodebookingSimrs = strtolower(trim((string) data_get(
                $itemSimrs,
                'kodebooking',
                data_get(
                    $itemSimrs,
                    'kode_booking',
                    data_get(
                        $itemSimrs,
                        'nobooking',
                        data_get(
                            $itemSimrs,
                            'noreservasi',
                            data_get(
                                $itemSimrs,
                                'kode_booking_bpjs',
                                data_get($itemSimrs, 'kodebooking_bpjs', '')
                            )
                        )
                    )
                )
            )));

            if (
                $kodebookingBpjs !== ''
                && $kodebookingSimrs !== ''
                && $kodebookingBpjs === $kodebookingSimrs
            ) {
                return true;
            }

            $noRmSimrs = preg_replace('/\D/', '', (string) data_get(
                $itemSimrs,
                'norekammedis',
                data_get(
                    $itemSimrs,
                    'nocm',
                    data_get(
                        $itemSimrs,
                        'norm',
                        data_get($itemSimrs, 'no_rm', '')
                    )
                )
            ));

            $noKartuSimrs = preg_replace('/\D/', '', (string) data_get(
                $itemSimrs,
                'nokapst',
                data_get(
                    $itemSimrs,
                    'nokartu',
                    data_get(
                        $itemSimrs,
                        'nomorkartu',
                        data_get($itemSimrs, 'no_kartu', '')
                    )
                )
            ));

            $nikSimrs = preg_replace('/\D/', '', (string) data_get(
                $itemSimrs,
                'nik',
                data_get(
                    $itemSimrs,
                    'noidentitas',
                    data_get($itemSimrs, 'no_ktp', '')
                )
            ));

            $tanggalSimrs = trim((string) data_get(
                $itemSimrs,
                'tanggal',
                data_get(
                    $itemSimrs,
                    'tglreservasi',
                    data_get($itemSimrs, 'tgl_reservasi', '')
                )
            ));

            $kodepoliSimrs = strtoupper(trim((string) data_get(
                $itemSimrs,
                'kodepoli',
                data_get(
                    $itemSimrs,
                    'kode_poli',
                    data_get(
                        $itemSimrs,
                        'namapoli',
                        data_get(
                            $itemSimrs,
                            'nama_poli',
                            data_get($itemSimrs, 'namaruangan', '')
                        )
                    )
                )
            )));

            $noAntreanSimrs = $this->normalisasiNoAntreanCek(data_get(
                $itemSimrs,
                'noantrean',
                data_get(
                    $itemSimrs,
                    'noantrian',
                    data_get(
                        $itemSimrs,
                        'no_antrian',
                        data_get($itemSimrs, 'antrian', '')
                    )
                )
            ));

            $identitasSama =
                ($noKartuBpjs !== '' && $noKartuSimrs !== '' && $noKartuBpjs === $noKartuSimrs)
                || ($noRmBpjs !== '' && $noRmSimrs !== '' && $noRmBpjs === $noRmSimrs)
                || ($nikBpjs !== '' && $nikSimrs !== '' && $nikBpjs === $nikSimrs);

            if (!$identitasSama) {
                return false;
            }

            if ($tanggalBpjs !== '' && $tanggalSimrs !== '' && $tanggalBpjs !== $tanggalSimrs) {
                return false;
            }

            $poliSama = false;

            if ($kodepoliBpjs !== '' && $kodepoliSimrs !== '') {
                $poliSama =
                    $kodepoliBpjs === $kodepoliSimrs
                    || str_contains($kodepoliSimrs, $kodepoliBpjs)
                    || str_contains($kodepoliBpjs, $kodepoliSimrs);
            }

            $antreanSama = false;

            if ($noAntreanBpjs !== '' && $noAntreanSimrs !== '') {
                $antreanSama = (int) $noAntreanBpjs === (int) $noAntreanSimrs;
            }

            return $identitasSama && ($poliSama || $antreanSama);
        });
    }
    public function dashboardWaktuTunggu(Request $request, BpjsAntreanService $bpjs)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $waktu = $request->get('waktu', 'rs');

        if (!in_array($waktu, ['rs', 'server'], true)) {
            $waktu = 'rs';
        }

        $dashboard = $bpjs->dashboardTanggal($tanggal, $waktu);

        $dashboardItems = data_get($dashboard, 'response.list', data_get($dashboard, 'response', []));

        if (!is_array($dashboardItems)) {
            $dashboardItems = [];
        }

        return view('bpjs-antrean.dashboard-waktu-tunggu', compact(
            'tanggal',
            'waktu',
            'dashboard',
            'dashboardItems'
        ));
    }
    public function batalForm()
    {
        return view('bpjs-antrean.batal');
    }

    public function batalAntreanManual(
        Request $request,
        BpjsAntreanService $bpjs
    ) {
        $request->validate([
            'kodebooking' => ['required', 'string', 'max:50'],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        $kodebooking = trim($request->kodebooking);
        $keterangan = trim($request->keterangan);

        $response = $bpjs->batalAntrean($kodebooking, $keterangan);

        $code = (int) data_get($response, 'metadata.code');
        $message = data_get($response, 'metadata.message', 'Tidak ada pesan dari BPJS');

        if ($code === 200) {
            return back()
                ->with('success', "Antrean dengan kode booking {$kodebooking} berhasil dibatalkan. {$message}")
                ->with('bpjs_response', $response);
        }

        return back()
            ->withInput()
            ->with('error', "Gagal membatalkan antrean {$kodebooking}. {$message}")
            ->with('bpjs_response', $response);
    }

    public function batalByJadwalForm(
        Request $request,
        BpjsAntreanService $bpjs,
        SimrsApiService $simrs
    ) {
        $kodepoli = trim((string) $request->get('kodepoli', ''));
        $kodedokter = trim((string) $request->get('kodedokter', ''));
        $hari = trim((string) $request->get('hari', ''));
        $jampraktek = trim((string) $request->get('jampraktek', ''));

        $poliList = $simrs->poliBpjsOptions();

        $response = null;
        $list = [];

        if ($kodepoli && $kodedokter && $hari && $jampraktek) {
            $response = $bpjs->antreanByJadwal(
                $kodepoli,
                $kodedokter,
                $hari,
                $jampraktek
            );

            $list = data_get($response, 'response', []);

            if (!is_array($list)) {
                $list = [];
            }
        }

        return view('bpjs-antrean.batal-jadwal', compact(
            'kodepoli',
            'kodedokter',
            'hari',
            'jampraktek',
            'poliList',
            'response',
            'list'
        ));
    }
    public function batalByJadwalSubmit(Request $request, BpjsAntreanService $bpjs)
    {
        $request->validate([
            'kodebooking' => ['required', 'array', 'min:1'],
            'kodebooking.*' => ['required', 'string', 'max:50'],
            'keterangan' => ['required', 'string', 'max:255'],

            'kodepoli' => ['nullable', 'string'],
            'kodedokter' => ['nullable', 'string'],
            'hari' => ['nullable', 'string'],
            'jampraktek' => ['nullable', 'string'],
        ]);

        $keterangan = trim($request->keterangan);
        $hasil = [];

        foreach ($request->kodebooking as $kodebooking) {
            $kodebooking = trim($kodebooking);

            if (!$kodebooking) {
                continue;
            }

            $response = $bpjs->batalAntrean($kodebooking, $keterangan);

            $hasil[] = [
                'kodebooking' => $kodebooking,
                'code' => data_get($response, 'metadata.code'),
                'message' => data_get($response, 'metadata.message'),
                'response' => $response,
            ];
        }

        $berhasil = collect($hasil)->where('code', 200)->count();
        $gagal = collect($hasil)->where('code', '!=', 200)->count();

        return redirect()
            ->route('bpjs-antrean.batal-jadwal.form', [
                'kodepoli' => $request->kodepoli,
                'kodedokter' => $request->kodedokter,
                'hari' => $request->hari,
                'jampraktek' => $request->jampraktek,
            ])
            ->with('success', "Proses batal selesai. Berhasil: {$berhasil}, gagal: {$gagal}.")
            ->with('hasil_batal', $hasil);
    }
    public function ajaxPoliBpjs(Request $request, SimrsApiService $simrs)
    {
        $query = trim((string) $request->get('query', ''));

        $poliList = $simrs->poliBpjsOptions($query);

        return response()->json([
            'success' => true,
            'total' => count($poliList),
            'data' => $poliList,
        ]);
    }
    public function ajaxDokterBpjs(Request $request, SimrsApiService $simrs)
    {
        $query = trim((string) $request->get('query', ''));

        $dokterList = $simrs->dokterBpjsOptions($query);

        return response()->json([
            'success' => true,
            'total' => count($dokterList),
            'data' => $dokterList,
        ]);
    }

    public function batalByTanggalDokterPoli(Request $request, BpjsAntreanService $bpjs)
    {
        $response = null;
        $list = [];

        if ($request->filled(['tanggal', 'kodepoli', 'kodedokter'])) {
            $request->validate([
                'tanggal' => ['required', 'date'],
                'kodepoli' => ['required', 'string'],
                'kodedokter' => ['required', 'string'],
            ]);

            $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            $kodepoli = trim((string) $request->kodepoli);
            $kodedokter = trim((string) $request->kodedokter);

            // Endpoint:
            // antrean/pendaftaran/tanggal/{tanggal}
            $response = $bpjs->pendaftaranTanggal($tanggal);
            // return $response;
            $rawList = data_get($response, 'response', []);

            $list = collect($rawList)
                ->filter(function ($item) use ($kodepoli, $kodedokter) {
                    return trim((string) data_get($item, 'kodepoli')) === trim((string) $kodepoli)
                        && trim((string) data_get($item, 'kodedokter')) === trim((string) $kodedokter);
                })
                ->unique(function ($item) {
                    return data_get($item, 'kodebooking');
                })
                ->values()
                ->all();
        }
        // return $list;

        return view('bpjs-antrean.batal-jadwal', compact('response', 'list'));
    }
    public function formBatalJadwal()
    {
        $response = null;
        $list = [];

        return view('bpjs-antrean.batal-jadwal', compact('response', 'list'));
    }
    public function submitBatalJadwal(Request $request, BpjsAntreanService $bpjs)
    {
        $request->validate([
            'tanggal' => ['required', 'date'],
            'kodepoli' => ['required', 'string'],
            'kodedokter' => ['required', 'string'],
            'kodebooking' => ['required', 'array'],
            'kodebooking.*' => ['required', 'string'],
            'keterangan' => ['required', 'string'],
        ], [
            'kodebooking.required' => 'Silakan pilih minimal satu kode booking yang ingin dibatalkan.',
        ]);

        $hasil = [];

        foreach ($request->kodebooking as $kodebooking) {
            try {
                $batal = $bpjs->batalAntrean(
                    $kodebooking,
                    trim((string) $request->keterangan)
                );

                $hasil[] = [
                    'kodebooking' => $kodebooking,
                    'code' => data_get($batal, 'metadata.code', data_get($batal, 'metaData.code', '-')),
                    'message' => data_get($batal, 'metadata.message', data_get($batal, 'metaData.message', '-')),
                ];
            } catch (\Throwable $e) {
                Log::error('Gagal batal antrean BPJS', [
                    'kodebooking' => $kodebooking,
                    'error' => $e->getMessage(),
                ]);

                $hasil[] = [
                    'kodebooking' => $kodebooking,
                    'code' => 'ERROR',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return redirect()
            ->route('bpjs-antrean.batal-by-tanggal-dokter-poli', [
                'tanggal' => $request->tanggal,
                'kodepoli' => $request->kodepoli,
                'kodedokter' => $request->kodedokter,
                'nama_poli' => $request->nama_poli,
                'nama_dokter' => $request->nama_dokter,
            ])
            ->with('success', 'Proses batal antrean selesai.')
            ->with('hasil_batal', $hasil);
    }
    // Jhon | 2026-11-24 12:57 WITA | TAMBAH: GET antrean per kode booking, respons whitelist tanpa operasi pendaftaran.
    // Jhon | 2026-11-24 14:04 WITA | TAMBAH: lookup log baca-saja untuk status Belum, sesi dan CSRF mengikuti web middleware.
    // Jhon | 2026-11-24 14:15 WITA | TAMBAH: log surat kontrol menurut SEP asal SIMRS; POST terproteksi sesi/CSRF.
    // Jhon | 2026-11-27 10:33 WITA | TAMBAH: respons SEP diproyeksikan ke informasi tampilan; exception mentah tidak dikirim.
    // Jhon | 2026-11-27 10:50 WITA | TAMBAH: nomor rujukan diambil dari SEP sumber, bukan ditebak dari nomor reservasi.
    public function suratKontrolRujukan(Request $request, \App\Services\BpjsVclaimService $service)
    {
        $input = $request->validate(['noSEP'=>'required|string|max:64|regex:/^[A-Za-z0-9.\-]+$/']);
        try {
            $sep = $service->getSepDetail($input['noSEP']);
            $number = data_get($sep, 'response.noRujukan');
            if (!is_string($number) || !preg_match('/^[A-Za-z0-9.\-]{1,64}$/', $number)) return response()->json(['message'=>'Nomor rujukan tidak tersedia pada SEP ini.'], 404);
            $result = $service->getRujukanDetail($number);
            if ((string) data_get($result, 'metaData.code', data_get($result, 'metadata.code')) !== '200') $result = $service->getRujukanDetail($number, true);
            $detail = data_get($result, 'response.rujukan');
            if ((string) data_get($result, 'metaData.code', data_get($result, 'metadata.code')) !== '200' || !is_array($detail)) return response()->json(['message'=>'Rincian rujukan belum tersedia dari BPJS.','number'=>$number], 502);
            $data = ['No Rujukan'=>$number];
            foreach (['Tanggal Rujukan'=>'tglKunjungan','Nama Peserta'=>'peserta.nama','No Kartu'=>'peserta.noKartu','Asal Faskes'=>'provPerujuk.nama','Poli Rujukan'=>'poliRujukan.nama','Diagnosa'=>'diagnosa.nama','Pelayanan'=>'pelayanan.nama','Keluhan'=>'keluhan'] as $label=>$path) {
                $value = data_get($detail,$path); $data[$label] = is_scalar($value) ? (string) $value : null;
            }
            return response()->json(['data'=>$data,'number'=>$number])->header('Cache-Control','private, no-store');
        } catch (\Throwable $e) { return response()->json(['message'=>'Informasi rujukan belum dapat diambil.'],502); }
    }

    public function suratKontrolSep(Request $request, \App\Services\BpjsVclaimService $service)
    {
        $input = $request->validate(['noSEP' => 'required|string|max:64|regex:/^[A-Za-z0-9.\-]+$/']);
        try {
            $result = $service->getSepDetail($input['noSEP']);
            $code = data_get($result, 'metaData.code', data_get($result, 'metadata.code'));
            $detail = data_get($result, 'response');
            if ((string) $code !== '200' || !is_array($detail)) {
                return response()->json(['message' => 'Informasi SEP belum tersedia dari BPJS.'], 502);
            }
            $fields = ['No SEP'=>'noSep','Tanggal SEP'=>'tglSep','Jenis Pelayanan'=>'jnsPelayanan','Poli'=>'poli','Diagnosa'=>'diagnosa','Kelas Rawat'=>'kelasRawat','No Rujukan'=>'noRujukan','Nama Peserta'=>'peserta.nama','No Kartu'=>'peserta.noKartu','No RM'=>'peserta.noMr','Tanggal Lahir'=>'peserta.tglLahir','Jenis Peserta'=>'peserta.jnsPeserta','Hak Kelas'=>'peserta.hakKelas'];
            $data = [];
            foreach ($fields as $label => $path) {
                $value = data_get($detail, $path);
                $data[$label] = is_scalar($value) && trim((string) $value) !== '' ? (string) $value : null;
            }
            return response()->json(['data'=>$data])->header('Cache-Control', 'private, no-store');
        } catch (\Throwable $e) {
            return response()->json(['message'=>'Informasi SEP belum dapat diambil. Silakan coba kembali.'], 502);
        }
    }

    public function suratKontrolLog(Request $request, \App\Services\AntreanBridgingLogService $service)
    {
        $input = $request->validate(['noSEP' => 'required|string|max:64|regex:/^[A-Za-z0-9.\-]+$/']);
        try { return response()->json($service->suratKontrol($input['noSEP'])); }
        catch (\DomainException $e) { return response()->json(['message' => $e->getMessage()], 502); }
        catch (\Throwable $e) { return response()->json(['message' => 'Log bridging surat kontrol belum dapat diambil.'], 502); }
    }

    public function riwayatAntreanLog(Request $request, \App\Services\AntreanBridgingLogService $service)
    {
        $input = $request->validate(['kodebooking' => 'required|string|max:64|regex:/^[A-Za-z0-9.\-]+$/']);
        try { return response()->json($service->lookup($input['kodebooking'])); }
        // Jhon | 2026-11-24 14:06 WITA | UBAH: hanya tampilkan pesan diagnosis yang ditulis service; exception mentah tetap disembunyikan.
        catch (\DomainException $e) { return response()->json(['message' => $e->getMessage()], 502); }
        catch (\Throwable $e) { return response()->json(['message' => 'Log bridging belum dapat diambil. Periksa koneksi serta konfigurasi token/signature API pada server.'], 502); }
    }

    public function riwayatAntreanDetail(Request $request, BpjsAntreanService $bpjs)
    {
        $input = $request->validate(['kodebooking' => 'required|string|max:100|regex:/^[A-Za-z0-9._-]+$/']);
        try {
            $result = $bpjs->antreanKodeBooking(rawurlencode($input['kodebooking']));
            $code = (string) data_get($result, 'metadata.code', data_get($result, 'metaData.code', ''));
            if ($code !== '200') return response()->json(['message' => 'BPJS belum mengembalikan antrean untuk kode booking ini atau layanan sedang gagal.'], 502);
            $data = data_get($result, 'response', []);
            if (!is_array($data)) return response()->json(['message' => 'Respons antrean BPJS tidak dapat dibaca.'], 502);
            if (isset($data['kodebooking'])) $data = [$data];
            elseif (isset($data['list']) && is_array($data['list'])) $data = $data['list'];
            $allowed = array_flip(['kodebooking', 'tanggal', 'tanggalperiksa', 'kodepoli', 'namapoli', 'kodedokter', 'namadokter', 'noantrean', 'nomorantrean', 'status', 'sumberdata', 'jampraktek']);
            $rows = [];
            foreach ($data as $item) {
                if (!is_array($item)) continue;
                if (strtolower(trim((string) ($item['kodebooking'] ?? ''))) !== strtolower($input['kodebooking'])) continue;
                $rows[] = array_intersect_key($item, $allowed);
            }
            return response()->json(['data' => $rows]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Pengecekan antrean BPJS gagal. Silakan coba lagi.'], 502);
        }
    }

    public function cekRiwayatKontrolBpjs(
        Request $request,
        SimrsRiwayatKontrolService $simrs,
        BpjsAntreanService $bpjs
    ) {

        // Jhon | 2026-11-24 13:06 WITA | TAMBAH: pilihan kategori tervalidasi; default tanggal input SIMRS.
        $request->validate(['tanggal' => 'nullable|date_format:Y-m-d', 'kategori' => 'nullable|in:tglentri,tglreservasi']);
        $kategori = $request->input('kategori', 'tglentri');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d')) ?: now()->format('Y-m-d');
        $tanggal = \Carbon\Carbon::parse($tanggal)->format('Y-m-d');

        $jenisPasienFilter = strtoupper(
            trim((string) $request->get('jenis_pasien', 'ALL'))
        );

        if (!in_array(
            $jenisPasienFilter,
            ['ALL', 'BPJS', 'NON_BPJS'],
            true
        )) {
            $jenisPasienFilter = 'ALL';
        }

        $statusAntreanFilter = strtoupper(
            trim((string) $request->get('status_antrean', 'ALL'))
        );

        if (!in_array(
            $statusAntreanFilter,
            ['ALL', 'SUDAH', 'BELUM'],
            true
        )) {
            $statusAntreanFilter = 'ALL';
        }

        $responseSimrs = null;
        $responseBpjs = null;
        $list = [];

        /*
        |--------------------------------------------------------------------------
        | Ambil Data SIMRS dan BPJS
        |--------------------------------------------------------------------------
        */
        $responseSimrs = $simrs->getRiwayatKontrol(
            $tanggal,
            $tanggal,
            $kategori,
            'undefined'
        );

        // Jhon | 2026-11-24 13:06 WITA | PINDAH: ambil antrean sesudah tanggal pelayanan hasil SIMRS tersedia.

        /*
        |--------------------------------------------------------------------------
        | Ambil Riwayat Kontrol SIMRS
        |--------------------------------------------------------------------------
        */
        $riwayatKontrol = data_get($responseSimrs, 'riwayat', []);

        if (empty($riwayatKontrol)) {
            $riwayatKontrol = data_get(
                $responseSimrs,
                'response.riwayat',
                []
            );
        }

        if (empty($riwayatKontrol)) {
            $riwayatKontrol = data_get(
                $responseSimrs,
                'data.riwayat',
                []
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Antrean BPJS
        |--------------------------------------------------------------------------
        */
        // Jhon | 2026-11-24 13:06 WITA | UBAH: antrean memakai tanggal pelayanan, bukan tanggal input SIMRS.
        if (!is_array($riwayatKontrol)) $riwayatKontrol = [];
        $tanggalBpjs = [];
        $tanggalTidakLengkap = false;
        if ($kategori === 'tglreservasi') {
            $tanggalBpjs[] = $tanggal;
        } else {
            foreach ($riwayatKontrol as $item) {
                $serviceDate = data_get($item, 'tanggalreservasi') ?: data_get($item, 'tglkontrol');
                try {
                    if (!$serviceDate) { $tanggalTidakLengkap = true; continue; }
                    $tanggalBpjs[] = \Carbon\Carbon::parse($serviceDate)->format('Y-m-d');
                } catch (\Throwable $e) { $tanggalTidakLengkap = true; }
            }
        }
        $tanggalBpjs = array_values(array_unique($tanggalBpjs));
        $antreanBpjs = [];
        $bpjsLengkap = !$tanggalTidakLengkap;
        foreach ($tanggalBpjs as $serviceDate) {
            $result = $bpjs->pendaftaranTanggal($serviceDate);
            $code = (string) data_get($result, 'metadata.code', data_get($result, 'metaData.code', ''));
            $items = data_get($result, 'response', []);
            if ($code !== '200' || !is_array($items)) { $bpjsLengkap = false; continue; }
            if (isset($items['list']) && is_array($items['list'])) $items = $items['list'];
            if (isset($items['kodebooking'])) $items = [$items];
            foreach ($items as $entry) if (is_array($entry)) $antreanBpjs[] = $entry;
        }
        $responseBpjs = ['metadata' => [
            'code' => $bpjsLengkap ? 200 : 502,
            'message' => $bpjsLengkap ? 'Pengecekan berdasarkan tanggal pelayanan selesai' : 'Pengecekan belum lengkap; tanggal pelayanan kosong atau sebagian request BPJS gagal',
        ], 'response' => $antreanBpjs];

        /*
        |--------------------------------------------------------------------------
        | Kelompokkan Antrean BPJS Berdasarkan Kode Booking
        |--------------------------------------------------------------------------
        */
        $antreanByKodeBooking = collect($antreanBpjs)
            ->filter(function ($item) {
                return trim(
                    (string) data_get($item, 'kodebooking')
                ) !== '';
            })
            ->keyBy(function ($item) {
                return strtolower(
                    trim((string) data_get($item, 'kodebooking'))
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Helper Mengambil Nilai Pertama yang Tidak Kosong
        |--------------------------------------------------------------------------
        */
        $ambilNilai = function ($data, array $keys, $default = '') {
            foreach ($keys as $key) {
                $value = data_get($data, $key);

                if ($value !== null && trim((string) $value) !== '') {
                    return trim((string) $value);
                }
            }

            return $default;
        };

        /*
        |--------------------------------------------------------------------------
        | Gabungkan Riwayat Kontrol dengan Antrean BPJS
        |--------------------------------------------------------------------------
        */
        $list = collect($riwayatKontrol)
            ->map(function ($item) use (
                $antreanByKodeBooking,
                $ambilNilai
            ) {
                $noReservasi = $ambilNilai(
                    $item,
                    [
                        'noreservasi',
                        'no_reservasi',
                        'noReservasi',
                    ]
                );

                $antrean = null;

                if ($noReservasi !== '') {
                    $antrean = $antreanByKodeBooking->get(
                        strtolower($noReservasi)
                    );
                }

                /*
        |--------------------------------------------------------------------------
        | Ambil Jenis Pasien
        |--------------------------------------------------------------------------
        */
                $jenisPasienRaw = $ambilNilai(
                    $item,
                    [
                        'jenispasien',
                        'jenis_pasien',
                        'jenisPasien',
                        'kelompokpasien',
                        'kelompok_pasien',
                        'kelompokPasien',
                        'namakelompokpasien',
                        'nama_kelompok_pasien',
                    ]
                );

                /*
        |--------------------------------------------------------------------------
        | Ambil Nomor BPJS
        |--------------------------------------------------------------------------
        */
                $noBpjs = $ambilNilai(
                    $item,
                    [
                        'nokapst',
                        'nokartu',
                        'noKartu',
                        'no_kartu',
                        'nobpjs',
                        'no_bpjs',
                        'noBpjs',
                        'nomorkartu',
                        'nomor_kartu',
                    ],
                    '-'
                );

                /*
        |--------------------------------------------------------------------------
        | Tentukan BPJS atau NON BPJS
        |--------------------------------------------------------------------------
        |
        | NON BPJS harus diperiksa terlebih dahulu karena teks
        | "NON BPJS" juga mengandung kata "BPJS".
        */
                $jenisNormalized = strtoupper(
                    trim(
                        preg_replace(
                            '/[\s_-]+/',
                            ' ',
                            $jenisPasienRaw
                        )
                    )
                );

                $isExplicitNonBpjs =
                    str_contains($jenisNormalized, 'NON BPJS')
                    || str_contains($jenisNormalized, 'NONBPJS')
                    || in_array(
                        $jenisNormalized,
                        [
                            'UMUM',
                            'PRIBADI',
                            'UMUM/PRIBADI',
                            'UMUM / PRIBADI',
                        ],
                        true
                    );

                $memilikiNoBpjs = !in_array(
                    strtoupper(trim($noBpjs)),
                    ['', '-', '0', 'NULL'],
                    true
                );

                $isBpjs = !$isExplicitNonBpjs
                    && (
                        str_contains($jenisNormalized, 'BPJS')
                        || $memilikiNoBpjs
                    );

                $jenisPasien = $isBpjs
                    ? 'BPJS'
                    : 'NON BPJS';

                return [
                    'kontrol' => $item,
                    'noreservasi' => $noReservasi,
                    'antrean' => $antrean,

                    'jenis_pasien_raw' => $jenisPasienRaw,
                    'jenis_pasien' => $jenisPasien,
                    'is_bpjs' => $isBpjs,
                    'no_bpjs' => $memilikiNoBpjs ? $noBpjs : '-',

                    'sudah_dibuat_antrean' => !empty($antrean),

                    'kodebooking' => data_get(
                        $antrean,
                        'kodebooking'
                    ),

                    'noantrean' => data_get(
                        $antrean,
                        'noantrean'
                    ),

                    'status_antrean' => data_get(
                        $antrean,
                        'status'
                    ),

                    'sumberdata_bpjs' => data_get(
                        $antrean,
                        'sumberdata'
                    ),

                    'kodepoli_bpjs' => data_get(
                        $antrean,
                        'kodepoli'
                    ),

                    'kodedokter_bpjs' => data_get(
                        $antrean,
                        'kodedokter'
                    ),

                    'jampraktek_bpjs' => data_get(
                        $antrean,
                        'jampraktek'
                    ),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Filter Jenis Pasien
        |--------------------------------------------------------------------------
        |
        | Filter dilakukan sebelum summary dan pagination agar seluruh hasil
        | mengikuti pilihan BPJS atau NON BPJS.
        */
        if ($jenisPasienFilter === 'BPJS') {
            $list = $list->filter(function ($row) {
                return (bool) data_get($row, 'is_bpjs', false);
            });
        }

        if ($jenisPasienFilter === 'NON_BPJS') {
            $list = $list->filter(function ($row) {
                return !data_get($row, 'is_bpjs', false);
            });
        }
        /*
|--------------------------------------------------------------------------
| Filter Status Antrean
|--------------------------------------------------------------------------
|
| SUDAH  = noreservasi SIMRS ditemukan sebagai kodebooking BPJS
| BELUM  = noreservasi SIMRS belum ditemukan di antrean BPJS
*/
        if ($statusAntreanFilter === 'SUDAH') {
            $list = $list->filter(function ($row) {
                return (bool) data_get(
                    $row,
                    'sudah_dibuat_antrean',
                    false
                );
            });
        }

        if ($statusAntreanFilter === 'BELUM') {
            $list = $list->filter(function ($row) {
                return !data_get(
                    $row,
                    'sudah_dibuat_antrean',
                    false
                );
            });
        }


        $list = $list
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Summary Semua Data Setelah Filter
        |--------------------------------------------------------------------------
        */
        $fullList = collect($list);

        $summaryAll = [
            'total' => $fullList->count(),

            'sudah' => $fullList
                ->where('sudah_dibuat_antrean', true)
                ->count(),

            'belum' => $fullList
                ->where('sudah_dibuat_antrean', false)
                ->count(),

            'bpjs' => $fullList
                ->where('is_bpjs', true)
                ->count(),

            'non_bpjs' => $fullList
                ->where('is_bpjs', false)
                ->count(),
        ];

        $summaryAll['persen'] = $summaryAll['total'] > 0
            ? round(
                ($summaryAll['sudah'] / $summaryAll['total']) * 100
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $perPage = 20;

        $list = $this->paginateArray(
            $list,
            $perPage
        );

        /*
        |--------------------------------------------------------------------------
        | Pertahankan Query String Saat Pindah Halaman
        |--------------------------------------------------------------------------
        */
        if (
            $list instanceof
            \Illuminate\Pagination\LengthAwarePaginator
        ) {
            $list->appends($request->query());
        }

        /*
        |--------------------------------------------------------------------------
        | Summary Halaman Aktif
        |--------------------------------------------------------------------------
        */
        $pageItems = collect($list->items());

        $summaryPage = [
            'total' => $pageItems->count(),

            'sudah' => $pageItems
                ->where('sudah_dibuat_antrean', true)
                ->count(),

            'belum' => $pageItems
                ->where('sudah_dibuat_antrean', false)
                ->count(),

            'bpjs' => $pageItems
                ->where('is_bpjs', true)
                ->count(),

            'non_bpjs' => $pageItems
                ->where('is_bpjs', false)
                ->count(),
        ];

        $summaryPage['persen'] = $summaryPage['total'] > 0
            ? round(
                ($summaryPage['sudah'] / $summaryPage['total']) * 100
            )
            : 0;

        return view(
            'bpjs-antrean.cek-riwayat-kontrol',
            compact(
                'tanggal',
                'kategori',
                'tanggalBpjs',
                'bpjsLengkap',
                'jenisPasienFilter',
                'statusAntreanFilter',
                'responseSimrs',
                'responseBpjs',
                'list',
                'summaryAll',
                'summaryPage'
            )
        );
    }
}
