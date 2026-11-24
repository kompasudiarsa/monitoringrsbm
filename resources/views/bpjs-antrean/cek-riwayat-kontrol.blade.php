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
    /* Jhon | 2026-11-24 12:57 WITA | UBAH: tampilan ringkas selaras halaman surat kontrol. */
    .bpjs-page{padding:16px 24px;background:#f8fafc}.bpjs-container{max-width:none}.bpjs-title{font-size:23px;line-height:1.3;letter-spacing:-.025em}.bpjs-desc{font-size:13px;margin:8px 0 16px}.bpjs-eyebrow{display:none}.bpjs-card{border-radius:18px;padding:18px;box-shadow:0 6px 24px #0f172a08}.summary-box{border-radius:16px;box-shadow:0 4px 16px #0f172a06}.riwayat-check{cursor:pointer;border:0;font:inherit;font-weight:700;max-width:180px;white-space:normal}.riwayat-check:focus-visible{outline:3px solid #7dd3fc;outline-offset:3px}
    .riwayat-dialog{width:min(760px,calc(100vw - 32px));max-height:85vh;border:0;border-radius:20px;padding:0;box-shadow:0 24px 80px #0f172a50}.riwayat-dialog::backdrop{background:#0f172a80}.riwayat-dialog-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px;border-bottom:1px solid #e2e8f0}.riwayat-dialog-header h2{font-size:18px;margin:0}.riwayat-dialog-header button{border:0;border-radius:8px;padding:8px 12px;cursor:pointer}.riwayat-dialog-body{padding:18px;overflow:auto;max-height:65vh}.riwayat-detail-item{padding:12px;background:#f8fafc;border-radius:10px;margin-bottom:10px;overflow-wrap:anywhere}.riwayat-detail-item p{margin:6px 0;font-size:13px}
    @media(max-width:700px){.bpjs-page{padding:12px}.bpjs-card{padding:14px}.bpjs-title{font-size:20px}}
    /* Jhon | 2026-11-24 13:06 WITA | UBAH: lima kontrol filter responsif, tanpa kolom terjepit. */
    .bpjs-page .riwayat-date-grid { grid-template-columns: repeat(4,minmax(0,1fr)); }
    .bpjs-page .riwayat-date-grid .actions-row { grid-column: 1 / -1; }
    @media(max-width:900px){.bpjs-page .riwayat-date-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:540px){.bpjs-page .riwayat-date-grid{grid-template-columns:1fr;}}
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
            Cek Riwayat Kontrol vs Antrean BPJS.
        </h1>

        {{-- Jhon | 2026-11-24 12:57 WITA | UBAH: petunjuk singkat tanpa detail implementasi. --}}
        <p class="bpjs-desc">Klik status untuk mengecek antrean BPJS berdasarkan nomor reservasi.</p>

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

                {{-- Jhon | 2026-11-24 13:06 WITA | TAMBAH: pilihan tanggal SIMRS; akses BPJS mengikuti tanggal pelayanan hasilnya. --}}
                <div class="filter-grid riwayat-date-grid">
                    <div class="form-group">
                        <label class="form-label" for="riwayat-kategori">Jenis Tanggal</label>
                        <select id="riwayat-kategori" name="kategori" class="form-control">
                            <option value="tglentri" {{ $kategori === 'tglentri' ? 'selected' : '' }}>Tanggal Input</option>
                            <option value="tglreservasi" {{ $kategori === 'tglreservasi' ? 'selected' : '' }}>Tanggal Kontrol / Reservasi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Pencarian</label>

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
            {{-- Jhon | 2026-11-24 13:21 WITA | UBAH: hapus informasi respons yang ditandai. --}}
        </div>

        @if ($isSearched)
        <div style="margin-bottom:10px; color:#0f172a; font-weight:950; font-size:15px;">
            Total Semua Data
        </div>

        {{-- Jhon | 2026-11-24 13:06 WITA | TAMBAH: hasil belum lengkap tidak berarti antrean pasti belum dibuat. --}}
        {{-- Jhon | 2026-11-24 13:21 WITA | UBAH: hapus banner peringatan sesuai penandaan pengguna. --}}
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

        {{-- Jhon | 2026-11-24 13:21 WITA | UBAH: hapus ringkasan Data Halaman Ini; ringkasan total tetap. --}}
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
                    {{-- Jhon | 2026-11-24 13:16 WITA | TAMBAH: daftar scroll mempertahankan URL filter server. --}}
                    <tbody id="riwayat-scroll-rows" data-next-url="{{ $list instanceof \Illuminate\Pagination\LengthAwarePaginator ? $list->nextPageUrl() : '' }}">
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
                            data-row-key="{{ hash('sha256', json_encode($row)) }}"
                            data-status="{{ $sudahAntrean ? 'sudah' : 'belum' }}"
                            data-jenis-pasien="{{ $jenisPasienFilterValue }}"
                            data-search="{{ $rowSearch }}">
                            <td>
                                {{ $list instanceof \Illuminate\Pagination\LengthAwarePaginator ? $list->firstItem() + $i : $i + 1 }}
                            </td>
                            <td>
                                {{-- Jhon | 2026-11-24 12:57 WITA | UBAH: status menjadi tombol GET antrean per reservasi. --}}
                                <button type="button" class="riwayat-check {{ $sudahAntrean ? 'badge-success-soft' : 'badge-warning-soft' }}" data-log="{{ $sudahAntrean ? '0' : '1' }}" data-booking="{{ $row['kodebooking'] ?: $noReservasi }}" title="Klik untuk cek antrean BPJS" {{ empty($row['kodebooking']) && empty($noReservasi) ? 'disabled' : '' }}>
                                    {{ $sudahAntrean ? 'Sudah' : 'Belum' }} ⓘ
                                </button>
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
            {{-- Jhon | 2026-11-24 13:16 WITA | TAMBAH: sentinel selalu tersedia, retry hanya ketika gagal. --}}
            <div id="riwayat-scroll-sentinel" style="padding:14px;text-align:center">
                <span id="riwayat-scroll-status" role="status" aria-live="polite"></span>
                <button id="riwayat-scroll-retry" type="button" hidden>Coba lagi</button>
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

            <nav id="riwayat-pagination" class="pagination-wrap" aria-label="Navigasi halaman">
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

{{-- Jhon | 2026-11-24 12:57 WITA | TAMBAH: modal baca-saja detail antrean, tidak memperbarui status daftar tanpa refresh. --}}
{{-- Jhon | 2026-11-24 14:20 WITA | UBAH: modal status antrean memakai drag bersama. --}}
<dialog data-draggable-modal id="riwayat-antrean-modal" class="riwayat-dialog">
    <div class="riwayat-dialog-header" data-drag-handle><h2>Detail Antrean BPJS</h2><button type="button" id="riwayat-antrean-close" aria-label="Tutup">×</button></div>
    <div id="riwayat-antrean-result" class="riwayat-dialog-body" aria-live="polite"></div>
</dialog>
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

// Jhon | 2026-11-24 12:57 WITA | TAMBAH: event delegation, abort request lama, dan render aman memakai textContent.
(function () {
    const modal = document.getElementById('riwayat-antrean-modal');
    const result = document.getElementById('riwayat-antrean-result');
    let activeRequest = null;
    document.getElementById('riwayat-antrean-close').addEventListener('click', () => modal.close());
    modal.addEventListener('close', () => { if (activeRequest) activeRequest.abort(); });
    document.addEventListener('click', async event => {
        const button = event.target.closest('.riwayat-check');
        if (!button || button.disabled) return;
        if (activeRequest) activeRequest.abort();
        const request = new AbortController(); activeRequest = request;
        // Jhon | 2026-11-24 14:04 WITA | UBAH: status Belum membuka data dan response log bridging.
        const isLog = button.dataset.log === '1';
        modal.querySelector('h2').textContent = isLog ? 'Log Bridging Antrean BPJS' : 'Detail Antrean BPJS';
        result.textContent = isLog ? 'Memuat log bridging...' : 'Memuat antrean BPJS...';
        if (!modal.open) modal.showModal();
        try {
            const url = new URL(isLog ? "{{ route('bpjs-antrean.riwayat-antrean-log') }}" : "{{ route('bpjs-antrean.riwayat-antrean-detail') }}");
            const options = {headers: {'Accept': 'application/json'}, signal: request.signal};
            if (isLog) {
                options.method = 'POST';
                options.headers['Content-Type'] = 'application/json';
                options.headers['X-CSRF-TOKEN'] = document.querySelector('#monitoring-logout [name="_token"]').value;
                options.body = JSON.stringify({kodebooking: button.dataset.booking});
            } else url.searchParams.set('kodebooking', button.dataset.booking);
            const response = await fetch(url, options);
            if (response.status === 401) throw new Error('Sesi berakhir. Silakan login kembali.');
            const json = await response.json();
            if (!response.ok) throw new Error(json.message || 'Gagal mengecek antrean BPJS.');
            if (activeRequest !== request) return;
            result.textContent = '';
            if (isLog) {
                if (!Array.isArray(json.data) || !json.data.length) { result.textContent = 'Tidak ada log bridging untuk kode booking ini.'; return; }
                json.data.forEach((item, index) => {
                    const card = document.createElement('section'); card.className = 'riwayat-detail-item';
                    const title = document.createElement('h3'); title.textContent = 'Log ' + (index + 1); card.appendChild(title);
                    ['data', 'response'].forEach(key => {
                        const label = document.createElement('h4'); label.textContent = key === 'data' ? 'Data' : 'Response';
                        const pre = document.createElement('pre'); pre.style.cssText = 'white-space:pre-wrap;overflow-wrap:anywhere;background:#0f172a;color:#e2e8f0;padding:12px;border-radius:8px;max-height:300px;overflow:auto';
                        pre.textContent = JSON.stringify(item[key], null, 2); card.append(label, pre);
                    }); result.appendChild(card);
                });
                if (json.truncated) { const note = document.createElement('p'); note.textContent = 'Menampilkan maksimal 20 log.'; result.appendChild(note); }
                return;
            }
            if (!Array.isArray(json.data) || !json.data.length) { result.textContent = 'Antrean untuk kode booking ini tidak ditemukan pada respons BPJS.'; return; }
            const labels = {kodebooking:'Kode Booking', tanggal:'Tanggal', tanggalperiksa:'Tanggal Periksa', kodepoli:'Kode Poli', namapoli:'Poli', kodedokter:'Kode Dokter', namadokter:'Dokter', noantrean:'No Antrean', nomorantrean:'No Antrean', status:'Status Antrean', sumberdata:'Sumber', jampraktek:'Jam Praktik'};
            json.data.forEach(item => {
                const card = document.createElement('div'); card.className = 'riwayat-detail-item';
                Object.entries(labels).forEach(([key,label]) => {
                    if (!(key in item) || (typeof item[key] === 'object' && item[key] !== null)) return;
                    const line = document.createElement('p'); line.textContent = label + ': ' + (item[key] ?? '-'); card.appendChild(line);
                }); result.appendChild(card);
            });
        } catch (error) {
            if (error.name !== 'AbortError' && activeRequest === request) result.textContent = error.message;
        }
    });
})();

// Jhon | 2026-11-24 13:16 WITA | TAMBAH: infinite scroll, deduplikasi, penghentian akhir, dan retry terkontrol.
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.getElementById('riwayat-scroll-rows');
    const sentinel = document.getElementById('riwayat-scroll-sentinel');
    const status = document.getElementById('riwayat-scroll-status');
    const retry = document.getElementById('riwayat-scroll-retry');
    if (!rows || !sentinel || !('IntersectionObserver' in window)) return;
    const pagination = document.getElementById('riwayat-pagination');
    if (pagination) pagination.hidden = true;
    let next = rows.dataset.nextUrl || '';
    let busy = false;
    let failed = false;
    const seen = new Set(Array.from(rows.querySelectorAll('[data-row-key]'), row => row.dataset.rowKey));
    const observer = new IntersectionObserver(entries => {
        if (entries.some(entry => entry.isIntersecting) && !failed) loadNext();
    }, {rootMargin: '300px'});
    function finish() {
        if (!next) {
            observer.disconnect();
            status.textContent = seen.size ? 'Semua data telah dimuat.' : '';
        }
    }
    async function loadNext() {
        if (busy || !next) return;
        busy = true;
        failed = false;
        retry.hidden = true;
        status.textContent = 'Memuat data berikutnya...';
        const requested = next;
        try {
            const response = await fetch(requested, {headers: {'Accept': 'text/html'}});
            if (!response.ok) throw new Error('HTTP gagal');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            const incoming = doc.getElementById('riwayat-scroll-rows');
            if (!incoming) throw new Error('Respons tidak sesuai');
            const following = incoming.dataset.nextUrl || '';
            if (following === requested) throw new Error('Pagination tidak maju');
            incoming.querySelectorAll('[data-row-key]').forEach(row => {
                if (!seen.has(row.dataset.rowKey)) {
                    seen.add(row.dataset.rowKey);
                    rows.appendChild(document.importNode(row, true));
                }
            });
            if (typeof applyRiwayatFilter === 'function') applyRiwayatFilter();
            next = following;
            status.textContent = seen.size + ' data dimuat.';
            observer.unobserve(sentinel);
            if (next) observer.observe(sentinel);
            finish();
        } catch (error) {
            failed = true;
            status.textContent = 'Gagal memuat data berikutnya.';
            retry.hidden = false;
        } finally {
            busy = false;
        }
    }
    retry.addEventListener('click', loadNext);
    if (next) observer.observe(sentinel);
    finish();
});

</script>
@endsection