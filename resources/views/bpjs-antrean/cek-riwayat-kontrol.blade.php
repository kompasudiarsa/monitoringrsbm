@extends('layouts.app', ['title' => 'Cek Riwayat Kontrol vs Antrean BPJS'])

@section('content')
<style>
    .bpjs-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(249, 115, 22, .16), transparent 32%),
            linear-gradient(180deg, #fff7ed 0%, #f8fafc 38%, #f8fafc 100%);
        padding: 28px;
    }

    .bpjs-container {
        max-width: 1320px;
        margin: 0 auto;
    }

    .bpjs-eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: #ffedd5;
        color: #c2410c;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 12px;
    }

    .bpjs-title {
        font-size: 32px;
        font-weight: 950;
        color: #0f172a;
        line-height: 1.15;
        margin: 0;
        letter-spacing: -.04em;
    }

    .bpjs-desc {
        color: #64748b;
        font-size: 15px;
        margin-top: 10px;
        margin-bottom: 22px;
        line-height: 1.7;
    }

    .bpjs-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 28px;
        padding: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 220px 220px 240px 1fr;
        gap: 14px;
        align-items: end;
    }

    .form-group {
        display: grid;
        gap: 8px;
    }

    .form-label {
        color: #0f172a;
        font-size: 13px;
        font-weight: 900;
        margin: 0;
    }

    .form-control {
        width: 100%;
        height: 48px;
        border-radius: 16px;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #0f172a;
        font-size: 14px;
        padding: 0 14px;
        outline: none;
    }

    .form-control:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, .12);
    }

    .actions-row {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-orange,
    .btn-soft {
        height: 48px;
        border-radius: 16px;
        padding: 0 18px;
        font-size: 14px;
        font-weight: 950;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-orange {
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, #f97316, #ea580c);
        box-shadow: 0 10px 20px rgba(234, 88, 12, .22);
    }

    .btn-soft {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #dbe3ef;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .summary-box {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 24px;
        padding: 18px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .06);
    }

    .summary-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .summary-value {
        color: #0f172a;
        font-size: 30px;
        font-weight: 950;
        margin-top: 5px;
        letter-spacing: -.04em;
    }

    .summary-hint {
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
        margin-top: 4px;
    }

    .toolbar-grid {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 230px 230px;
        gap: 12px;
        margin-bottom: 16px;
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .bpjs-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .bpjs-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 0 12px 4px;
        white-space: nowrap;
    }

    .bpjs-table td {
        background: #f8fafc;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        padding: 14px 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .bpjs-table td:first-child {
        border-radius: 16px 0 0 16px;
    }

    .bpjs-table td:last-child {
        border-radius: 0 16px 16px 0;
    }

    .patient-main {
        display: grid;
        gap: 4px;
    }

    .patient-name {
        font-weight: 950;
        color: #0f172a;
    }

    .patient-sub {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
    }

    .badge-success-soft,
    .badge-warning-soft,
    .badge-danger-soft,
    .badge-muted-soft,
    .badge-orange-soft {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }

    .badge-success-soft {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning-soft {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger-soft {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-muted-soft {
        background: #e2e8f0;
        color: #475569;
    }

    .badge-orange-soft {
        background: #ffedd5;
        color: #c2410c;
    }

    .empty-state {
        text-align: center;
        color: #64748b;
        padding: 34px 20px;
        font-weight: 800;
    }


    /* Pagination */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-info {
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .pagination-list {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pagination-link,
    .pagination-current,
    .pagination-disabled {
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #334155;
        font-size: 13px;
        font-weight: 900;
        text-decoration: none;
        line-height: 1;
        transition: .2s ease;
    }

    .pagination-link:hover {
        border-color: #f97316;
        background: #fff7ed;
        color: #c2410c;
        text-decoration: none;
    }

    .pagination-current {
        border-color: #f97316;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
    }

    .pagination-disabled {
        background: #f8fafc;
        color: #cbd5e1;
        cursor: not-allowed;
    }

    .pagination-dots {
        min-width: 30px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 14px;
        font-weight: 900;
    }

    @media (max-width: 640px) {
        .pagination-wrap {
            justify-content: center;
        }

        .pagination-info {
            width: 100%;
            text-align: center;
        }

        .pagination-list {
            justify-content: center;
        }

        .pagination-link,
        .pagination-current,
        .pagination-disabled {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
        }
    }

    .meta-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 14px;
    }

    @media (max-width: 900px) {

        .filter-grid,
        .summary-grid,
        .toolbar-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .bpjs-page {
            padding: 18px;
        }

        .bpjs-title {
            font-size: 28px;
        }

        .btn-orange,
        .btn-soft {
            width: 100%;
        }

        .actions-row {
            width: 100%;
        }
    }
</style>

@php
$tanggalAktif = $tanggal ?? request('tanggal', now()->format('Y-m-d'));
$list = $list ?? [];

$items = collect($list instanceof \Illuminate\Pagination\LengthAwarePaginator ? $list->items() : $list);

$total = $list instanceof \Illuminate\Pagination\LengthAwarePaginator
? $list->total()
: $items->count();
$summaryAll = $summaryAll ?? [
'total' => 0,
'sudah' => 0,
'belum' => 0,
'persen' => 0,
];

$summaryPage = $summaryPage ?? [
'total' => 0,
'sudah' => 0,
'belum' => 0,
'persen' => 0,
];

$total = $summaryAll['total'] ?? 0;
$sudah = $summaryAll['sudah'] ?? 0;
$belum = $summaryAll['belum'] ?? 0;

$bpjsMessage = data_get($responseBpjs ?? [], 'metadata.message', '-');
$simrsMessage = data_get($responseSimrs ?? [], 'metadata.message',
data_get($responseSimrs ?? [], 'message', '-')
);

$isSearched = request()->filled('tanggal') || !empty($responseSimrs) || !empty($responseBpjs);
@endphp

<div class="bpjs-page">
    <div class="bpjs-container">
        <div class="bpjs-eyebrow">
            Riwayat Kontrol vs Antrean BPJS
        </div>
        <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
            Kembali ke Menu
        </a>
        <h1 class="bpjs-title">
            Cek riwayat kontrol SIMRS yang sudah dibuatkan antrean BPJS.
        </h1>

        <div class="bpjs-desc">
            Sistem mengambil data dari <strong>SIMRS pasien/get-riwayat-kontrol</strong>,
            lalu membandingkan <strong>noreservasi SIMRS</strong> dengan
            <strong>kodebooking BPJS</strong> dari antrean per tanggal.
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bpjs-card">
            <style>
                .filter-grid {
                    display: grid;
                    grid-template-columns: 240px 240px 1fr;
                    gap: 14px;
                    align-items: end;
                }

                @media (max-width: 768px) {
                    .filter-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>

            <form
                method="GET"
                action="{{ route('bpjs-antrean.cek-riwayat-kontrol') }}">

                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">Tanggal Kontrol</label>

                        <input
                            type="date"
                            name="tanggal"
                            value="{{ request('tanggal', $tanggalAktif) }}"
                            class="form-control"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Pasien</label>

                        <select name="jenis_pasien" class="form-control">
                            <option
                                value="ALL"
                                {{ request('jenis_pasien', 'ALL') === 'ALL'
                        ? 'selected'
                        : '' }}>
                                Semua Jenis Pasien
                            </option>

                            <option
                                value="BPJS"
                                {{ request('jenis_pasien') === 'BPJS'
                        ? 'selected'
                        : '' }}>
                                BPJS
                            </option>

                            <option
                                value="NON_BPJS"
                                {{ request('jenis_pasien') === 'NON_BPJS'
                        ? 'selected'
                        : '' }}>
                                NON BPJS
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status Antrean</label>

                        <select name="status_antrean" class="form-control">
                            <option
                                value="ALL"
                                {{ request('status_antrean', 'ALL') === 'ALL'
                        ? 'selected'
                        : '' }}>
                                Semua Status Antrean
                            </option>

                            <option
                                value="SUDAH"
                                {{ request('status_antrean') === 'SUDAH'
                        ? 'selected'
                        : '' }}>
                                Sudah Ada Antrean
                            </option>

                            <option
                                value="BELUM"
                                {{ request('status_antrean') === 'BELUM'
                        ? 'selected'
                        : '' }}>
                                Belum Ada Antrean
                            </option>
                        </select>
                    </div>

                    <div class="actions-row">
                        <button type="submit" class="btn-orange">
                            <i class="fas fa-search"></i>
                            Cek Data
                        </button>

                        <a
                            href="{{ route('bpjs-antrean.cek-riwayat-kontrol') }}"
                            class="btn-soft">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
            @if ($isSearched)
            <div class="meta-row">
                <span class="badge-orange-soft">
                    Tanggal: {{ \Carbon\Carbon::parse($tanggalAktif)->format('d-m-Y') }}
                </span>

                <span class="badge-muted-soft">
                    SIMRS: {{ $simrsMessage }}
                </span>

                <span class="badge-muted-soft">
                    BPJS: {{ $bpjsMessage }}
                </span>
            </div>
            @endif
        </div>

        @if ($isSearched)
        <div style="margin-bottom:10px; color:#0f172a; font-weight:950; font-size:15px;">
            Total Semua Data
        </div>

        <div class="summary-grid">
            <div class="summary-box">
                <div class="summary-label">Total Riwayat Kontrol</div>
                <div class="summary-value">{{ $summaryAll['total'] ?? 0 }}</div>
                <div class="summary-hint">Semua data dari SIMRS</div>
            </div>

            <div class="summary-box">
                <div class="summary-label">Sudah Ada Antrean di BPJS</div>
                <div class="summary-value">{{ $summaryAll['sudah'] ?? 0 }}</div>
                <div class="summary-hint">Semua noreservasi cocok dengan kodebooking</div>
            </div>

            <div class="summary-box">
                <div class="summary-label">Belum Ada Antrean</div>
                <div class="summary-value">{{ $summaryAll['belum'] ?? 0 }}</div>
                <div class="summary-hint">Semua data belum ditemukan di BPJS</div>
            </div>

            <div class="summary-box">
                <div class="summary-label">Persentase Sinkron</div>
                <div class="summary-value">{{ $summaryAll['persen'] ?? 0 }}%</div>
                <div class="summary-hint">Total SIMRS sudah masuk antrean BPJS</div>
            </div>
        </div>

        <div style="margin:22px 0 10px; color:#0f172a; font-weight:950; font-size:15px;">
            Data Halaman Ini
        </div>

        <div class="summary-grid">
            <div class="summary-box">
                <div class="summary-label">Total Per Page</div>
                <div class="summary-value">{{ $summaryPage['total'] ?? 0 }}</div>
                <div class="summary-hint">Jumlah data di halaman aktif</div>
            </div>

            <div class="summary-box">
                <div class="summary-label">Sudah Ada Antrean</div>
                <div class="summary-value">{{ $summaryPage['sudah'] ?? 0 }}</div>
                <div class="summary-hint">Sudah cocok di halaman ini</div>
            </div>

            <div class="summary-box">
                <div class="summary-label">Belum Ada Antrean</div>
                <div class="summary-value">{{ $summaryPage['belum'] ?? 0 }}</div>
                <div class="summary-hint">Belum cocok di halaman ini</div>
            </div>

            <div class="summary-box">
                <div class="summary-label">Persentase Page</div>
                <div class="summary-value">{{ $summaryPage['persen'] ?? 0 }}%</div>
                <div class="summary-hint">Persentase halaman aktif</div>
            </div>
        </div>

        <div class="bpjs-card">
            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
                <div>
                    <div style="font-weight:950; color:#0f172a; font-size:18px;">
                        List Riwayat Kontrol
                    </div>
                    <div style="color:#64748b; font-size:13px; margin-top:4px;">
                        Menampilkan status antrean berdasarkan pencocokan noreservasi SIMRS dengan kodebooking BPJS.
                    </div>
                </div>

                <div class="badge-orange-soft">
                    Total Semua: {{ $summaryAll['total'] ?? 0 }} data
                </div>

                <div class="badge-muted-soft">
                    Halaman Ini: {{ $summaryPage['total'] ?? 0 }} data
                </div>
            </div>

            @if ($total > 0)
            <div class="toolbar-grid">
                <input
                    type="text"
                    id="keywordFilter"
                    class="form-control"
                    placeholder="Cari nama pasien, no RM, no reservasi, kode booking, poli, dokter...">

                <select id="statusFilter" class="form-control">
                    <option value="all">Semua Status</option>
                    <option value="sudah">Sudah Ada Antrean BPJS</option>
                    <option value="belum">Belum Ada Antrean BPJS</option>
                </select>

                <select id="jenisPasienFilter" class="form-control">
                    <option value="all">Semua Jenis Pasien</option>
                    <option value="bpjs">BPJS</option>
                    <option value="non-bpjs">NON BPJS</option>
                </select>
            </div>

            <div class="table-wrap">
                <table class="bpjs-table" id="riwayatTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>No Reservasi SIMRS</th>
                            <th>Pasien</th>
                            <th>Jenis Pasien</th>
                            <th>No BPJS</th>
                            <th>Tanggal</th>
                            <th>Poli SIMRS</th>
                            <th>Dokter SIMRS</th>
                            <th>Kode Booking BPJS</th>
                            <th>No Antrean</th>
                            <th>Status Antrean</th>
                            <th>Sumber</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $i => $row)
                        @php
                        $kontrol = $row['kontrol'] ?? [];
                        $sudahAntrean = (bool) ($row['sudah_dibuat_antrean'] ?? false);

                        $noReservasi = $row['noreservasi']
                        ?? data_get($kontrol, 'noreservasi')
                        ?? data_get($kontrol, 'no_reservasi')
                        ?? data_get($kontrol, 'noReservasi')
                        ?? '-';

                        $noRm = data_get($kontrol, 'norekammedis')
                        ?? data_get($kontrol, 'norm')
                        ?? data_get($kontrol, 'no_rm')
                        ?? data_get($kontrol, 'nocm')
                        ?? '-';

                        $namaPasien = data_get($kontrol, 'namapasien')
                        ?? data_get($kontrol, 'nama_pasien')
                        ?? data_get($kontrol, 'nama')
                        ?? '-';

                        $noBpjs = $row['no_bpjs']
                        ?? $row['nokartu']
                        ?? $row['nokapst']
                        ?? data_get($kontrol, 'nokapst')
                        ?? data_get($kontrol, 'noKartu')
                        ?? data_get($kontrol, 'nokartu')
                        ?? data_get($kontrol, 'no_kartu')
                        ?? data_get($kontrol, 'nobpjs')
                        ?? data_get($kontrol, 'no_bpjs')
                        ?? '-';

                        $jenisPasienRaw = $row['jenis_pasien']
                        ?? $row['jenispasien']
                        ?? data_get($kontrol, 'jenispasien')
                        ?? data_get($kontrol, 'jenisPasien')
                        ?? data_get($kontrol, 'jenis_pasien')
                        ?? data_get($kontrol, 'kelompokpasien')
                        ?? data_get($kontrol, 'kelompok_pasien')
                        ?? data_get($kontrol, 'namakelompokpasien')
                        ?? data_get($kontrol, 'nama_kelompok_pasien')
                        ?? '';

                        $jenisPasienLower = strtolower(trim((string) $jenisPasienRaw));
                        $noBpjsValid = !empty(trim((string) $noBpjs)) && trim((string) $noBpjs) !== '-';

                        $isExplicitNonBpjs = str_contains($jenisPasienLower, 'non bpjs')
                        || str_contains($jenisPasienLower, 'non-bpjs')
                        || in_array($jenisPasienLower, ['umum', 'pribadi', 'mandiri'], true);

                        $isExplicitBpjs = ! $isExplicitNonBpjs
                        && (
                        str_contains($jenisPasienLower, 'bpjs')
                        || str_contains($jenisPasienLower, 'jkn')
                        );

                        $isPasienBpjs = $isExplicitBpjs
                        || (! $isExplicitNonBpjs && $noBpjsValid);

                        $jenisPasien = $isPasienBpjs ? 'BPJS' : 'NON BPJS';
                        $jenisPasienFilterValue = $isPasienBpjs ? 'bpjs' : 'non-bpjs';

                        if (! $isPasienBpjs) {
                        $noBpjs = '-';
                        }

                        $tanggalKontrol = data_get($kontrol, 'tanggalreservasi')
                        ?? data_get($kontrol, 'tanggal')
                        ?? data_get($kontrol, 'tglreservasi')
                        ?? data_get($kontrol, 'tgl_rencana_kontrol')
                        ?? data_get($kontrol, 'tglRencanaKontrol')
                        ?? $tanggalAktif;

                        $namaPoli = data_get($kontrol, 'namapoli')
                        ?? data_get($kontrol, 'namaPoli')
                        ?? data_get($kontrol, 'poli')
                        ?? data_get($kontrol, 'kodepoli')
                        ?? '-';

                        $namaDokter = data_get($kontrol, 'namadokter')
                        ?? data_get($kontrol, 'namaDokter')
                        ?? data_get($kontrol, 'dokter')
                        ?? data_get($kontrol, 'kodedokter')
                        ?? '-';

                        $kodeBooking = $row['kodebooking'] ?? '-';
                        $noAntrean = $row['noantrean'] ?? '-';
                        $statusAntrean = $row['status_antrean'] ?? '-';
                        $sumberData = $row['sumberdata_bpjs'] ?? '-';

                        $rowSearch = strtolower(trim(
                        $noReservasi . ' ' .
                        $kodeBooking . ' ' .
                        $noRm . ' ' .
                        $namaPasien . ' ' .
                        $jenisPasien . ' ' .
                        $noBpjs . ' ' .
                        $namaPoli . ' ' .
                        $namaDokter . ' ' .
                        $statusAntrean . ' ' .
                        $sumberData
                        ));
                        @endphp

                        <tr
                            data-status="{{ $sudahAntrean ? 'sudah' : 'belum' }}"
                            data-jenis-pasien="{{ $jenisPasienFilterValue }}"
                            data-search="{{ $rowSearch }}">
                            <td>
                                {{ $list instanceof \Illuminate\Pagination\LengthAwarePaginator ? $list->firstItem() + $i : $i + 1 }}
                            </td>
                            <td>
                                @if ($sudahAntrean)
                                <span class="badge-success-soft">
                                    <i class="fas fa-check-circle"></i>
                                    Sudah
                                </span>
                                @else
                                <span class="badge-warning-soft">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Belum
                                </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-orange-soft">
                                    {{ $noReservasi }}
                                </span>
                            </td>

                            <td>
                                <div class="patient-main">
                                    <div class="patient-name">{{ $namaPasien }}</div>
                                    <div class="patient-sub">No RM: {{ $noRm }}</div>
                                </div>
                            </td>

                            <td>
                                @if ($isPasienBpjs)
                                <span class="badge-success-soft">BPJS</span>
                                @else
                                <span class="badge-muted-soft">NON BPJS</span>
                                @endif
                            </td>

                            <td>{{ $noBpjs }}</td>

                            <td>
                                @if ($tanggalKontrol && $tanggalKontrol !== '-')
                                {{ \Carbon\Carbon::parse($tanggalKontrol)->format('d-m-Y') }}
                                @else
                                -
                                @endif
                            </td>

                            <td>{{ $namaPoli }}</td>
                            <td>{{ $namaDokter }}</td>

                            <td>
                                @if ($sudahAntrean)
                                <span class="badge-success-soft">
                                    {{ $kodeBooking }}
                                </span>
                                @else
                                -
                                @endif
                            </td>

                            <td>{{ $noAntrean }}</td>

                            <td>
                                @if ($sudahAntrean)
                                @php
                                $statusLower = strtolower((string) $statusAntrean);
                                @endphp

                                @if ($statusLower === 'batal')
                                <span class="badge-danger-soft">{{ $statusAntrean }}</span>
                                @elseif (str_contains($statusLower, 'selesai'))
                                <span class="badge-success-soft">{{ $statusAntrean }}</span>
                                @elseif (str_contains($statusLower, 'belum'))
                                <span class="badge-warning-soft">{{ $statusAntrean }}</span>
                                @else
                                <span class="badge-muted-soft">{{ $statusAntrean }}</span>
                                @endif
                                @else
                                <span class="badge-muted-soft">Tidak Ada</span>
                                @endif
                            </td>

                            <td>{{ $sumberData }}</td>

                            <td>
                                @if ($sudahAntrean)
                                <span class="badge-success-soft">
                                    Riwayat kontrol sudah dibuatkan antrean BPJS
                                </span>
                                @else
                                <span class="badge-warning-soft">
                                    Belum dibuatkan antrean BPJS
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        <tr id="emptyFilterRow" style="display:none;">
                            <td colspan="14">
                                <div class="empty-state">
                                    Tidak ada data yang cocok dengan filter pencarian.
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (
            $list instanceof \Illuminate\Pagination\LengthAwarePaginator
            && $list->hasPages()
            )
            @php
            // Pertahankan filter tanggal dan jenis pasien saat pindah halaman.
            $list->appends(request()->except('page'));

            $currentPage = $list->currentPage();
            $lastPage = $list->lastPage();

            // Tampilkan maksimal dua nomor halaman di kiri dan kanan halaman aktif.
            $startPage = max(1, $currentPage - 2);
            $endPage = min($lastPage, $currentPage + 2);
            @endphp

            <nav class="pagination-wrap" aria-label="Navigasi halaman">
                <div class="pagination-info">
                    Menampilkan
                    {{ $list->firstItem() ?? 0 }}
                    –
                    {{ $list->lastItem() ?? 0 }}
                    dari
                    {{ $list->total() }}
                    data
                </div>

                <ul class="pagination-list">
                    {{-- Halaman sebelumnya --}}
                    @if ($list->onFirstPage())
                    <li>
                        <span
                            class="pagination-disabled"
                            aria-disabled="true">
                            ‹
                        </span>
                    </li>
                    @else
                    <li>
                        <a
                            href="{{ $list->previousPageUrl() }}"
                            class="pagination-link"
                            rel="prev"
                            aria-label="Halaman sebelumnya">
                            ‹
                        </a>
                    </li>
                    @endif

                    {{-- Halaman pertama --}}
                    @if ($startPage > 1)
                    <li>
                        <a
                            href="{{ $list->url(1) }}"
                            class="pagination-link">
                            1
                        </a>
                    </li>

                    @if ($startPage > 2)
                    <li>
                        <span class="pagination-dots">...</span>
                    </li>
                    @endif
                    @endif

                    {{-- Nomor halaman --}}
                    @for ($page = $startPage; $page <= $endPage; $page++)
                        <li>
                        @if ($page === $currentPage)
                        <span
                            class="pagination-current"
                            aria-current="page">
                            {{ $page }}
                        </span>
                        @else
                        <a
                            href="{{ $list->url($page) }}"
                            class="pagination-link"
                            aria-label="Ke halaman {{ $page }}">
                            {{ $page }}
                        </a>
                        @endif
                        </li>
                        @endfor

                        {{-- Halaman terakhir --}}
                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                            <li>
                            <span class="pagination-dots">...</span>
                            </li>
                            @endif

                            <li>
                                <a
                                    href="{{ $list->url($lastPage) }}"
                                    class="pagination-link">
                                    {{ $lastPage }}
                                </a>
                            </li>
                            @endif

                            {{-- Halaman berikutnya --}}
                            @if ($list->hasMorePages())
                            <li>
                                <a
                                    href="{{ $list->nextPageUrl() }}"
                                    class="pagination-link"
                                    rel="next"
                                    aria-label="Halaman berikutnya">
                                    ›
                                </a>
                            </li>
                            @else
                            <li>
                                <span
                                    class="pagination-disabled"
                                    aria-disabled="true">
                                    ›
                                </span>
                            </li>
                            @endif
                </ul>
            </nav>
            @endif
            @else
            <div class="empty-state">
                Tidak ada data riwayat kontrol SIMRS pada tanggal tersebut.
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
    const keywordFilter = document.getElementById('keywordFilter');
    const statusFilter = document.getElementById('statusFilter');
    const jenisPasienFilter = document.getElementById('jenisPasienFilter');
    const riwayatTable = document.getElementById('riwayatTable');
    const emptyFilterRow = document.getElementById('emptyFilterRow');

    function applyRiwayatFilter() {
        if (!riwayatTable) {
            return;
        }

        const keyword = (keywordFilter?.value || '').toLowerCase().trim();
        const status = statusFilter?.value || 'all';
        const jenisPasien = jenisPasienFilter?.value || 'all';

        let visibleCount = 0;

        riwayatTable.querySelectorAll('tbody tr[data-status]').forEach(function(row) {
            const rowStatus = row.getAttribute('data-status');
            const rowJenisPasien = row.getAttribute('data-jenis-pasien');
            const rowSearch = row.getAttribute('data-search') || '';

            const matchStatus = status === 'all' || rowStatus === status;
            const matchJenisPasien = jenisPasien === 'all' || rowJenisPasien === jenisPasien;
            const matchKeyword = keyword === '' || rowSearch.includes(keyword);

            if (matchStatus && matchJenisPasien && matchKeyword) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyFilterRow) {
            emptyFilterRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    if (keywordFilter) {
        keywordFilter.addEventListener('input', applyRiwayatFilter);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', applyRiwayatFilter);
    }

    if (jenisPasienFilter) {
        jenisPasienFilter.addEventListener('change', applyRiwayatFilter);
    }
</script>
@endsection