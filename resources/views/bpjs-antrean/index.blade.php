@extends('layouts.app', ['title' => 'Monitoring Antrean BPJS'])

@section('content')
<style>
    .bpjs-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 22px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .bpjs-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .bpjs-title {
        font-size: 26px;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
    }

    .bpjs-subtitle {
        color: #64748b;
        margin-top: 6px;
        font-size: 14px;
    }

    .filter-card,
    .stat-card,
    .table-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        border: 1px solid rgba(226, 232, 240, .8);
    }

    .filter-card {
        padding: 16px;
        margin-bottom: 18px;
    }


    .filter-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(170px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .form-label {
        font-weight: 800;
        font-size: 13px;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-control {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        min-height: 44px;
    }

    .btn-primary-custom {
        border: 0;
        border-radius: 14px;
        min-height: 44px;
        padding: 0 18px;
        font-weight: 900;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .stat-card {
        padding: 16px;
        position: relative;
        overflow: hidden;
    }

    .stat-card:before {
        content: "";
        position: absolute;
        right: -30px;
        top: -30px;
        width: 90px;
        height: 90px;
        background: rgba(249, 115, 22, .13);
        border-radius: 999px;
    }

    .stat-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .stat-value {
        color: #0f172a;
        font-size: 28px;
        font-weight: 950;
        margin-top: 6px;
    }

    .table-card {
        overflow: hidden;
    }

    .table-card-header {
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 16px;
        font-weight: 900;
        color: #0f172a;
    }

    .bpjs-table {
        width: 100%;
        margin: 0;
    }

    .bpjs-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 13px;
        white-space: nowrap;
    }

    .bpjs-table td {
        padding: 13px;
        vertical-align: middle;
        color: #334155;
        border-top: 1px solid #eef2f7;
        font-size: 13px;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        background: #ffedd5;
        color: #c2410c;
    }

    .btn-detail {
        border-radius: 12px;
        padding: 7px 11px;
        background: #0f172a;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .btn-detail:hover {
        color: #fff;
        opacity: .9;
    }

    .auto-refresh {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 900px) {

        .bpjs-header,
        .filter-grid {
            grid-template-columns: 1fr;
            display: grid;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }

    @media (max-width: 1200px) {
        .stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 420px) {
        .stats {
            grid-template-columns: 1fr;
        }
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .badge-status-selesai {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status-batal {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-status-belum {
        background: #e0f2fe;
        color: #0369a1;
    }

    .badge-status-sedang {
        background: #ffedd5;
        color: #c2410c;
    }

    .badge-status-default {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-reset-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        border-radius: 14px;
        padding: 0 18px;
        font-weight: 900;
        background: #e2e8f0;
        color: #334155;
        text-decoration: none;
    }

    .btn-reset-filter:hover {
        color: #0f172a;
        text-decoration: none;
    }

    .bpjs-tab-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        border: 1px solid rgba(226, 232, 240, .8);
        overflow: hidden;
    }

    .bpjs-tabs {
        display: flex;
        gap: 8px;
        padding: 12px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
        overflow-x: auto;
    }

    .bpjs-tab-btn {
        border: 0;
        background: #f1f5f9;
        color: #475569;
        border-radius: 14px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 900;
        white-space: nowrap;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .bpjs-tab-btn.active {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        box-shadow: 0 8px 20px rgba(249, 115, 22, .25);
    }

    .bpjs-tab-count {
        min-width: 24px;
        height: 24px;
        padding: 0 8px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, .08);
        font-size: 12px;
        font-weight: 950;
    }

    .bpjs-tab-btn.active .bpjs-tab-count {
        background: rgba(255, 255, 255, .22);
        color: #fff;
    }

    .bpjs-tab-panel {
        display: none;
    }

    .bpjs-tab-panel.active {
        display: block;
    }

    .bpjs-tab-panel .table-card {
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .bpjs-pagination {
        padding: 14px 18px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
    }

    .bpjs-pagination .pagination {
        margin: 0;
        gap: 6px;
        display: flex;
        flex-wrap: wrap;
    }

    .bpjs-pagination .page-item .page-link {
        border: 0;
        border-radius: 10px;
        min-width: 36px;
        height: 36px;
        padding: 8px 12px;
        color: #475569;
        background: #f1f5f9;
        font-size: 13px;
        font-weight: 800;
    }

    .bpjs-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
    }

    .bpjs-pagination .page-item.disabled .page-link {
        opacity: .45;
    }
</style>

@php
$itemsTanggalRaw = data_get($antreanTanggal, 'response.list', data_get($antreanTanggal, 'response', []));

if (!is_array($itemsTanggalRaw)) {
$itemsTanggalRaw = [];
}

$itemsTanggal = $itemsTanggalPaginated ?? collect($itemsTanggalRaw);

$noKartuDuplikatUntukTabel = $noKartuDuplikatPaginated ?? collect($noKartuDuplikat ?? []);

$totalTanggal = $itemsTanggal instanceof \Illuminate\Pagination\LengthAwarePaginator
? $itemsTanggal->total()
: count($itemsTanggal);

$itemsAktif = data_get($antreanAktif, 'response.list', data_get($antreanAktif, 'response', []));
$dashboardItems = data_get($dashboard, 'response.list', data_get($dashboard, 'response', []));

$itemsSuratKontrol = data_get($suratKontrol ?? [], 'response.list', data_get($suratKontrol ?? [], 'response', []));

if (!is_array($itemsAktif)) {
$itemsAktif = [];
}

if (!is_array($dashboardItems)) {
$dashboardItems = [];
}

if (!is_array($itemsSuratKontrol)) {
$itemsSuratKontrol = [];
}

$totalAktif = count($itemsAktif);
$totalSuratKontrol = count($itemsSuratKontrol);

$lastMetadata = data_get($antreanTanggal, 'metadata.message', '-');
@endphp
<a href="{{ route('bpjs-antrean.menu') }}" style="
    display:inline-flex;
    margin-bottom:14px;
    background:#0f172a;
    color:#fff;
    padding:9px 14px;
    border-radius:14px;
    font-weight:900;
    text-decoration:none;
">
    Kembali ke Menu
</a>
<div class="bpjs-page">
    <div class="bpjs-header">
        <div>
            <h1 class="bpjs-title">Monitoring Antrean BPJS</h1>
            <div class="bpjs-subtitle">
                Pantau antrean Mobile JKN, antrean aktif, detail kode booking, dan waktu tunggu.
            </div>
        </div>

        <div class="auto-refresh">
            Auto refresh setiap <b>30 detik</b>
        </div>
    </div>

    <form method="GET" action="{{ route('bpjs-antrean.dashboard') }}" class="filter-card">
        <div class="filter-grid">
            <div>
                <div class="form-label">Tanggal</div>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>

            <!-- <div>
                <div class="form-label">Waktu Dashboard</div>
                <input type="time" name="waktu" value="{{ substr($waktu, 0, 5) }}" class="form-control">
            </div> -->

            <div>
                <div class="form-label">Jenis Antrean</div>
                <select name="jenis" class="form-control">
                    <option value="semua" {{ ($jenis ?? 'semua') === 'semua' ? 'selected' : '' }}>
                        Semua Jenis
                    </option>
                    <option value="mobile_jkn" {{ ($jenis ?? 'semua') === 'mobile_jkn' ? 'selected' : '' }}>
                        Mobile JKN
                    </option>
                    <option value="bridging" {{ ($jenis ?? 'semua') === 'bridging' ? 'selected' : '' }}>
                        Bridging
                    </option>
                    <option value="onsite" {{ ($jenis ?? 'semua') === 'onsite' ? 'selected' : '' }}>
                        Onsite / Loket
                    </option>
                    <option value="lainnya" {{ ($jenis ?? 'semua') === 'lainnya' ? 'selected' : '' }}>
                        Lainnya
                    </option>
                </select>
            </div>

            <div>
                <div class="form-label">Status Antrean</div>
                <select name="status" class="form-control">
                    <option value="semua" {{ ($status ?? 'semua') === 'semua' ? 'selected' : '' }}>
                        Semua Status
                    </option>
                    <option value="selesai" {{ ($status ?? 'semua') === 'selesai' ? 'selected' : '' }}>
                        Selesai Dilayani
                    </option>
                    <option value="sedang" {{ ($status ?? 'semua') === 'sedang' ? 'selected' : '' }}>
                        Sedang Dilayani
                    </option>
                    <option value="belum" {{ ($status ?? 'semua') === 'belum' ? 'selected' : '' }}>
                        Belum Dilayani
                    </option>
                    <option value="batal" {{ ($status ?? 'semua') === 'batal' ? 'selected' : '' }}>
                        Batal
                    </option>
                    <option value="lainnya" {{ ($status ?? 'semua') === 'lainnya' ? 'selected' : '' }}>
                        Lainnya
                    </option>
                </select>
            </div>
            <div>
                <div class="form-label">Nama Pasien</div>
                <input
                    type="text"
                    name="nama"
                    value="{{ $nama ?? request('nama') }}"
                    class="form-control"
                    placeholder="Cari nama pasien">
            </div>

            <div>
                <div class="form-label">No Kartu BPJS</div>
                <input
                    type="text"
                    name="nokartu"
                    value="{{ $nokartu ?? request('nokartu') }}"
                    class="form-control"
                    placeholder="Cari no kartu">
            </div>
            <div>
                <div class="form-label">Duplikat BPJS</div>
                <select name="duplikat_only" class="form-control">
                    <option value="ALL" {{ ($duplikatOnly ?? 'ALL') === 'ALL' ? 'selected' : '' }}>
                        Semua Data
                    </option>

                    <option value="DUPLIKAT" {{ ($duplikatOnly ?? 'ALL') === 'DUPLIKAT' ? 'selected' : '' }}>
                        Hanya No Kartu Duplikat
                    </option>

                    <option value="TIDAK_DUPLIKAT" {{ ($duplikatOnly ?? 'ALL') === 'TIDAK_DUPLIKAT' ? 'selected' : '' }}>
                        Hanya No Kartu Tidak Duplikat
                    </option>
                </select>
            </div>

            <button class="btn-primary-custom" type="submit">
                Tampilkan Data
            </button>
            <a href="{{ route('bpjs-antrean.dashboard') }}" class="btn-reset-filter">
                Reset
            </a>
        </div>
    </form>

    @php
    $totalSimrs = data_get($statistikAntrean, 'total_simrs', 0);
    $totalBpjs = data_get($statistikAntrean, 'total_bpjs', 0);

    $totalMobileJkn = data_get($statistikAntrean, 'jenis.mobile_jkn', 0);
    $totalBridging = data_get($statistikAntrean, 'jenis.bridging', 0);
    $totalOnsite = data_get($statistikAntrean, 'jenis.onsite', 0);
    $totalLainnya = data_get($statistikAntrean, 'jenis.lainnya', 0);

    $mjknSelesai = data_get($statistikAntrean, 'mjkn_status.selesai', 0);
    $mjknSedang = data_get($statistikAntrean, 'mjkn_status.sedang', 0);
    $mjknBelum = data_get($statistikAntrean, 'mjkn_status.belum', 0);
    $mjknBatal = data_get($statistikAntrean, 'mjkn_status.batal', 0);
    @endphp

    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Total Antrean SIMRS</div>
            <div class="stat-value">{{ $totalSimrs }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Antrean BPJS</div>
            <div class="stat-value">{{ $totalBpjs }}</div>
        </div>



        <div class="stat-card">
            <div class="stat-label">Mobile JKN</div>
            <div class="stat-value">{{ $totalMobileJkn }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Bridging</div>
            <div class="stat-value">{{ $totalBridging }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Onsite / Loket</div>
            <div class="stat-value">{{ $totalOnsite }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Jenis Lainnya</div>
            <div class="stat-value">{{ $totalLainnya }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">MJKN Selesai</div>
            <div class="stat-value">{{ $mjknSelesai }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">MJKN Sedang Dilayani</div>
            <div class="stat-value">{{ $mjknSedang }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">MJKN Belum Dilayani</div>
            <div class="stat-value">{{ $mjknBelum }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">MJKN Batal</div>
            <div class="stat-value">{{ $mjknBatal }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">No Kartu BPJS Duplikat</div>
            <div class="stat-value">{{ $totalNoKartuDuplikat ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Data Duplikat</div>
            <div class="stat-value">{{ $totalDataNoKartuDuplikat ?? 0 }}</div>
        </div>
    </div>
    <div class="bpjs-tab-card">
        <div class="bpjs-tabs" role="tablist">
            <button
                type="button"
                class="bpjs-tab-btn active"
                data-tab-target="tab-duplikat"
                role="tab"
                aria-selected="true">
                No Kartu Duplikat
                <span class="bpjs-tab-count">
                    {{
        $noKartuDuplikatUntukTabel instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $noKartuDuplikatUntukTabel->total()
            : count($noKartuDuplikatUntukTabel ?? [])
    }}
                </span>
            </button>

            <button
                type="button"
                class="bpjs-tab-btn"
                data-tab-target="tab-antrean"
                role="tab"
                aria-selected="false">
                Daftar Antrean
                <span class="bpjs-tab-count">{{ $totalTanggal }}</span>
            </button>
        </div>

        <div id="tab-duplikat" class="bpjs-tab-panel active" role="tabpanel">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <div class="table-title">No Kartu BPJS Duplikat</div>
                        <div class="auto-refresh">
                            Menampilkan no kartu BPJS yang muncul lebih dari 1 kali pada data yang sedang difilter.
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="bpjs-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Kartu BPJS</th>
                                <th>Jumlah</th>
                                <th>Daftar Pasien / Antrean</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($noKartuDuplikatUntukTabel as $index => $duplikat)
                            <tr>
                                <td>
                                    {{
        $noKartuDuplikatUntukTabel instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $noKartuDuplikatUntukTabel->firstItem() + $index
            : $index + 1
    }}
                                </td>
                                <td>
                                    <b>{{ data_get($duplikat, 'no_kartu', '-') }}</b>
                                </td>
                                <td>
                                    <span class="badge-soft">
                                        {{ data_get($duplikat, 'jumlah', 0) }} data
                                    </span>
                                <td>
    @foreach (data_get($duplikat, 'items', []) as $item)
        @php
            $kodebookingDup = data_get($item, 'kodebooking', data_get($item, 'kode_booking', '-'));
            $namaDup = data_get($item, 'namapasien', '-');
            $noAntreanDup = data_get($item, 'noantrean', data_get($item, 'noantrian', '-'));
            $poliDup = data_get($item, 'namapoli', data_get($item, 'kodepoli', '-'));
            $statusDup = data_get($item, 'status', data_get($item, 'statusantrean', '-'));

            $jenisAntreanDup = data_get($item, 'jenis',
                data_get($item, 'jenisantrean',
                data_get($item, 'jenis_antrean',
                data_get($item, 'sumberdata',
                data_get($item, 'asalantrean',
                data_get($item, 'asal_antrian',
                data_get($item, 'asalpendaftaran',
                data_get($item, 'asal_pendaftaran', '-')))))))
            );

            $sumberDup = data_get($item, 'sumber_data', '-');

            $sumberClass = 'badge-status-default';

            if ($sumberDup === 'BPJS') {
                $sumberClass = 'badge-status-belum';
            } elseif ($sumberDup === 'SIMRS') {
                $sumberClass = 'badge-status-sedang';
            } elseif ($sumberDup === 'BPJS DAN SIMRS') {
                $sumberClass = 'badge-status-selesai';
            }
        @endphp

        <div style="padding:8px 0; border-bottom:1px solid #eef2f7;">
            <b>{{ $namaDup }}</b>
            <div style="color:#64748b; font-size:12px; margin-top:3px;">
                Booking: {{ $kodebookingDup }} |
                Antrean: {{ $noAntreanDup ?: '-' }} |
                Poli: {{ $poliDup }} |
                Jenis: {{ $jenisAntreanDup ?: '-' }} |
                Status: {{ $statusDup }} |
                Sumber:
                <span class="badge-status {{ $sumberClass }}" style="padding:3px 8px; font-size:11px;">
                    {{ $sumberDup }}
                </span>
            </div>
        </div>
    @endforeach
</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:30px; color:#64748b">
                                    Tidak ada No Kartu BPJS yang duplikat pada data ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($noKartuDuplikatUntukTabel instanceof \Illuminate\Pagination\LengthAwarePaginator && $noKartuDuplikatUntukTabel->hasPages())
                    <div class="bpjs-pagination">
                        {{ $noKartuDuplikatUntukTabel->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div id="tab-antrean" class="bpjs-tab-panel" role="tabpanel">
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <div class="table-title">Daftar Antrean BPJS Per Tanggal</div>
                        <div class="auto-refresh">Tanggal {{ $tanggal }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="bpjs-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Booking</th>
                                <th>No Antrean</th>
                                <th>Nama Pasien</th>
                                <th>No Kartu</th>
                                <th>Duplikat</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Jam Praktek</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($itemsTanggal as $index => $item)
                            @php
                            $kodebooking = data_get($item, 'kodebooking', data_get($item, 'kode_booking'));

                            $noKartuItem = preg_replace('/\D/', '', (string) data_get($item, 'nokapst', data_get($item, 'nomorkartu', '')));
                            $isDuplikatBpjs = in_array(
                            $noKartuItem,
                            collect($noKartuDuplikat ?? [])->pluck('no_kartu')->toArray(),
                            true
                            );

                            $jenisAntreanText = data_get($item, 'jenis',
                            data_get($item, 'jenisantrean',
                            data_get($item, 'jenis_antrean',
                            data_get($item, 'sumberdata',
                            data_get($item, 'sumber_data',
                            data_get($item, 'sumber',
                            data_get($item, 'asalantrean',
                            data_get($item, 'asal_antrian',
                            data_get($item, 'asalpendaftaran',
                            data_get($item, 'asal_pendaftaran', '-')))))))))
                            );

                            $statusAntrean = data_get($item, 'status', data_get($item, 'statusantrean', '-'));
                            $statusLower = strtolower(trim($statusAntrean));

                            $badgeClass = 'badge-status-default';

                            if (str_contains($statusLower, 'selesai')) {
                            $badgeClass = 'badge-status-selesai';
                            } elseif (str_contains($statusLower, 'batal')) {
                            $badgeClass = 'badge-status-batal';
                            } elseif (str_contains($statusLower, 'belum')) {
                            $badgeClass = 'badge-status-belum';
                            } elseif (str_contains($statusLower, 'sedang')) {
                            $badgeClass = 'badge-status-sedang';
                            }
                            @endphp

                            <tr>
                                <td>
                                    {{
        $itemsTanggal instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $itemsTanggal->firstItem() + $index
            : $index + 1
    }}
                                </td>
                                <td><b>{{ $kodebooking ?? '-' }}</b></td>
                                <td>{{ data_get($item, 'noantrean', data_get($item, 'noantrian', '-')) }}</td>
                                <td>{{ data_get($item, 'namapasien', '-') }}</td>
                                <td>{{ data_get($item, 'nokapst', data_get($item, 'nomorkartu', '-')) }}</td>
                                <td>
                                    @if ($isDuplikatBpjs)
                                    <span class="badge-status badge-status-batal">Duplikat</span>
                                    @else
                                    <span class="badge-status badge-status-selesai">Tidak</span>
                                    @endif
                                </td>
                                <td>{{ data_get($item, 'namapoli', data_get($item, 'kodepoli', '-')) }}</td>
                                <td>{{ data_get($item, 'namadokter', data_get($item, 'kodedokter', '-')) }}</td>
                                <td>{{ data_get($item, 'jampraktek', '-') }}</td>
                                <td>
                                    <span class="badge-soft">
                                        {{ $jenisAntreanText ?: '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status {{ $badgeClass }}">
                                        {{ $statusAntrean ?: '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($kodebooking)
                                    <a class="btn-detail" href="{{ route('bpjs-antrean.detail', $kodebooking) }}">
                                        Detail
                                    </a>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" style="text-align:center; padding:30px; color:#64748b">
                                    Tidak ada data antrean atau response BPJS kosong.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($itemsTanggal instanceof \Illuminate\Pagination\LengthAwarePaginator && $itemsTanggal->hasPages())
                    <div class="bpjs-pagination">
                        {{ $itemsTanggal->links('pagination::bootstrap-4') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const TAB_STORAGE_KEY = 'bpjs_antrean_active_tab';

    function activateBpjsTab(targetId) {
        document.querySelectorAll('.bpjs-tab-btn').forEach(function(btn) {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
        });

        document.querySelectorAll('.bpjs-tab-panel').forEach(function(panel) {
            panel.classList.remove('active');
        });

        const activeButton = document.querySelector('.bpjs-tab-btn[data-tab-target="' + targetId + '"]');
        const activePanel = document.getElementById(targetId);

        if (activeButton && activePanel) {
            activeButton.classList.add('active');
            activeButton.setAttribute('aria-selected', 'true');
            activePanel.classList.add('active');

            localStorage.setItem(TAB_STORAGE_KEY, targetId);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedTab = localStorage.getItem(TAB_STORAGE_KEY);

        if (savedTab && document.getElementById(savedTab)) {
            activateBpjsTab(savedTab);
        }

        document.querySelectorAll('.bpjs-tab-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-tab-target');
                activateBpjsTab(targetId);
            });
        });

        setTimeout(function() {
            window.location.reload();
        }, 30000);
    });
</script>
@endsection