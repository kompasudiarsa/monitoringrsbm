@extends('layouts.app', ['title' => 'List Pasien Registrasi'])

@section('content')
<style>
    .sep-box {
    display: grid;
    gap: 7px;
    min-width: 230px;
}

.sep-simrs {
    color: #166534;
    font-size: 12px;
    font-weight: 800;
    word-break: break-all;
}

.btn-check-sep {
    display: inline-flex;
    width: fit-content;
    min-height: 34px;
    align-items: center;
    justify-content: center;
    padding: 7px 11px;
    border: 1px solid #2563eb;
    border-radius: 8px;
    background: #ffffff;
    color: #2563eb;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
}

.btn-check-sep:hover {
    background: #eff6ff;
}

.btn-check-sep:disabled {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #94a3b8;
    cursor: not-allowed;
}

.sep-result {
    font-size: 12px;
    line-height: 1.5;
}

.sep-result-loading {
    color: #2563eb;
}

.sep-result-success {
    padding: 9px;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    background: #f0fdf4;
    color: #166534;
}

.sep-result-empty {
    padding: 9px;
    border: 1px solid #fed7aa;
    border-radius: 8px;
    background: #fff7ed;
    color: #9a3412;
}

.sep-result-error {
    padding: 9px;
    border: 1px solid #fecaca;
    border-radius: 8px;
    background: #fef2f2;
    color: #b91c1c;
}

.sep-result strong {
    display: block;
    word-break: break-all;
}

.sep-detail {
    display: block;
    margin-top: 3px;
    color: inherit;
    font-size: 11px;
    opacity: .85;
}
    .badge-sep-found {
    display: inline-flex;
    padding: 4px 8px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.badge-sep-warning {
    display: inline-flex;
    margin-top: 5px;
    padding: 4px 8px;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 11px;
    font-weight: 700;
}

.sep-number {
    color: #166534;
    font-weight: 800;
}
    .registrasi-page {
        display: grid;
        gap: 18px;
    }

    .registrasi-panel {
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
    }

    .registrasi-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .registrasi-header h1 {
        margin: 0 0 5px;
        color: #0f172a;
        font-size: clamp(21px, 2vw, 28px);
    }

    .registrasi-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit,
                minmax(170px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .filter-field {
        display: grid;
        gap: 6px;
    }

    .filter-field label {
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .filter-control {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        outline: none;
    }

    .filter-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .btn-filter,
    .btn-reset {
        min-height: 42px;
        padding: 9px 15px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-filter {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .btn-reset {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .summary-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }

    .summary-count {
        color: #475569;
        font-size: 14px;
    }

    .summary-count strong {
        color: #0f172a;
    }

    .table-wrapper {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .registrasi-table {
        width: 100%;
        min-width: 1380px;
        border-collapse: collapse;
    }

    .registrasi-table th {
        padding: 12px 11px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .registrasi-table td {
        padding: 12px 11px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 13px;
        vertical-align: top;
    }

    .registrasi-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .registrasi-table tbody tr:hover {
        background: #f8fafc;
    }

    .patient-name {
        color: #0f172a;
        font-weight: 750;
    }

    .sub-value {
        display: block;
        margin-top: 3px;
        color: #94a3b8;
        font-size: 11px;
    }

    .badge-payment {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-sep-empty {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .alert-error {
        padding: 13px 15px;
        border: 1px solid #fecaca;
        border-radius: 11px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 14px;
    }

    .empty-state {
        padding: 40px 20px !important;
        color: #64748b !important;
        text-align: center;
    }

    .pagination-wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
    }

    .pagination-info {
        color: #64748b;
        font-size: 13px;
    }

    .pagination-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .pagination-item {
        display: inline-flex;
        min-width: 36px;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .pagination-item:hover {
        border-color: #2563eb;
        color: #2563eb;
    }

    .pagination-item.active {
        border-color: #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .pagination-item.disabled {
        cursor: not-allowed;
        opacity: .45;
    }


    .sep-bpjs-box {
        display: grid;
        gap: 7px;
        min-width: 245px;
    }

    .sep-bpjs-number {
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 850;
        word-break: break-all;
    }

    .sep-status-badge {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .sep-status-success {
        background: #dcfce7;
        color: #166534;
    }

    .sep-status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .sep-status-error {
        background: #fee2e2;
        color: #b91c1c;
    }

    .sep-status-neutral {
        background: #e2e8f0;
        color: #475569;
    }

    .sep-action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .btn-detail-sep {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border: 1px solid #64748b;
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
    }

    .btn-detail-sep:hover {
        background: #f8fafc;
        border-color: #334155;
    }

    .sep-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, .56);
    }

    .sep-modal.show {
        display: flex;
    }

    .sep-modal-dialog {
        width: min(760px, 100%);
        max-height: calc(100vh - 36px);
        overflow-y: auto;
        border-radius: 16px;
        background: #ffffff;
    }

    .sep-modal-header {
        position: sticky;
        top: 0;
        z-index: 1;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 17px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .sep-modal-header h2 {
        margin: 0 0 4px;
        color: #0f172a;
        font-size: 19px;
    }

    .sep-modal-header p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
    }

    .sep-modal-close {
        display: inline-flex;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        background: #ffffff;
        color: #334155;
        font-size: 20px;
        cursor: pointer;
    }

    .sep-modal-body {
        display: grid;
        gap: 14px;
        padding: 20px;
    }

    .sep-comparison {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .sep-comparison-card,
    .sep-detail-card {
        padding: 13px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .sep-detail-label {
        display: block;
        margin-bottom: 4px;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
    }

    .sep-detail-value {
        color: #0f172a;
        font-size: 13px;
        font-weight: 800;
        word-break: break-word;
    }

    .sep-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .sep-list-title {
        margin: 2px 0 0;
        color: #0f172a;
        font-size: 14px;
    }

    .sep-list-item {
        display: grid;
        gap: 5px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #ffffff;
    }

    body.sep-modal-open {
        overflow: hidden;
    }

    @media (max-width: 900px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .registrasi-panel {
            padding: 14px;
        }

        .registrasi-header {
            display: block;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            width: 100%;
        }

        .btn-filter,
        .btn-reset {
            flex: 1;
            text-align: center;
        }

        .pagination-wrapper {
            align-items: flex-start;
            flex-direction: column;
        }

        .sep-comparison,
        .sep-detail-grid {
            grid-template-columns: 1fr;
        }

        .sep-modal {
            padding: 8px;
        }

        .sep-modal-body {
            padding: 14px;
        }
    }
</style>

<section class="registrasi-page">
    <div class="registrasi-panel">
        <div class="registrasi-header">
            <div>
                <h1>List Pasien Registrasi</h1>
                  <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
            Kembali ke Menu
        </a>
                <p>
                    Data pasien yang melakukan registrasi pada periode yang dipilih.
                </p>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('registrasi-pasien.index') }}"
            class="filter-grid">
            <div class="filter-field">
                <label for="dari">Tanggal Dari</label>

                <input
                    type="date"
                    id="dari"
                    name="dari"
                    class="filter-control"
                    value="{{ old('dari', $tanggalDari) }}"
                    required>

                @error('dari')
                <small style="color:#b91c1c">
                    {{ $message }}
                </small>
                @enderror
            </div>

            <div class="filter-field">
                <label for="sampai">Tanggal Sampai</label>

                <input
                    type="date"
                    id="sampai"
                    name="sampai"
                    class="filter-control"
                    value="{{ old('sampai', $tanggalSampai) }}"
                    required>

                @error('sampai')
                <small style="color:#b91c1c">
                    {{ $message }}
                </small>
                @enderror
            </div>

            <div class="filter-field">
                <label for="poli">Poli / Ruangan</label>

                <select
                    id="poli"
                    name="poli"
                    class="filter-control">
                    <option value="ALL">
                        Semua Poli / Ruangan
                    </option>

                    @foreach($poliOptions as $option)
                    <option
                        value="{{ $option['id'] }}"
                        {{ (string) $poliFilter === (string) $option['id'] ? 'selected' : '' }}>
                        {{ $option['nama'] }}
                    </option>
                    @endforeach
                </select>

                @error('poli')
                <small style="color:#b91c1c">
                    {{ $message }}
                </small>
                @enderror
            </div>

            <div class="filter-field">
                <label for="bpjs">Cara Bayar</label>

                <select
                    id="bpjs"
                    name="bpjs"
                    class="filter-control">
                    <option
                        value="ALL"
                        {{ $bpjsFilter === 'ALL' ? 'selected' : '' }}>
                        Semua Cara Bayar
                    </option>

                    <option
                        value="BPJS"
                        {{ $bpjsFilter === 'BPJS' ? 'selected' : '' }}>
                        BPJS
                    </option>

                    <option
                        value="NON_BPJS"
                        {{ $bpjsFilter === 'NON_BPJS' ? 'selected' : '' }}>
                        Non-BPJS
                    </option>
                </select>

                @error('bpjs')
                <small style="color:#b91c1c">
                    {{ $message }}
                </small>
                @enderror
            </div>
            <div class="filter-field">
                <label for="status_sep">Status SEP SIMRS</label>

                <select
                    id="status_sep"
                    name="status_sep"
                    class="filter-control">
                    <option
                        value="ALL"
                        {{ $statusSepFilter === 'ALL' ? 'selected' : '' }}>
                        Semua Status SEP
                    </option>

                    <option
                        value="SUDAH"
                        {{ $statusSepFilter === 'SUDAH' ? 'selected' : '' }}>
                        Sudah Ada SEP
                    </option>

                    <option
                        value="BELUM"
                        {{ $statusSepFilter === 'BELUM' ? 'selected' : '' }}>
                        Belum Ada SEP
                    </option>
                </select>

                @error('status_sep')
                <small style="color:#b91c1c">
                    {{ $message }}
                </small>
                @enderror
            </div>
            <div class="filter-field">
                <label for="limit">Data per Halaman</label>

                <select
                    id="limit"
                    name="limit"
                    class="filter-control">
                    @foreach([10, 25, 50, 100, 250, 500, 1000] as $option)
                    <option
                        value="{{ $option }}"
                        {{ (int) $limit === $option ? 'selected' : '' }}>
                        {{ $option }} data
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button
                    type="submit"
                    class="btn-filter">
                    Tampilkan
                </button>

                <a
                    href="{{ route('registrasi-pasien.index') }}"
                    class="btn-reset">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if($errorApi)
    <div class="alert-error">
        {{ $errorApi }}
    </div>
    @endif

    <div class="registrasi-panel">
        <div class="summary-row">
            <div class="summary-count">
                Total registrasi:
                <strong>{{ number_format($pasien->total()) }}</strong>
            </div>

            <div class="summary-count">
                Periode:
                <strong>
                    {{ \Carbon\Carbon::parse($tanggalDari)->format('d-m-Y') }}
                    sampai
                    {{ \Carbon\Carbon::parse($tanggalSampai)->format('d-m-Y') }}
                </strong>
            </div>

            @if($statusSepFilter !== 'ALL')
            <div class="summary-count">
                Status SEP SIMRS:
                <strong>
                    {{ $statusSepFilter === 'SUDAH'
                    ? 'Sudah Ada SEP'
                    : 'Belum Ada SEP' }}
                </strong>
            </div>
            @endif
        </div>

        <div class="table-wrapper">
            <table class="registrasi-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. Registrasi</th>
                        <th>SEP SIMRS</th>
                        <th>SEP BPJS</th>
                        <th>Nama Pasien</th>
                        <th>No. RM</th>
                        <th>No. BPJS</th>
                        <th>Tanggal Registrasi</th>
                        <th>Poli / Ruangan</th>
                        <th>Cara Bayar</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pasien as $index => $item)
                    <tr>
                        <td>
                            {{ ($pasien->firstItem() ?? 1) + $index }}
                        </td>

                        <td>
                            <strong>
                                {{ data_get($item, 'noregistrasi', '-') }}
                            </strong>

                            @if(data_get($item, 'noreservasi'))
                            <span class="sub-value">
                                Booking:
                                {{ data_get($item, 'noreservasi') }}
                            </span>
                            @endif
                        </td>

                        @php
                            $noSepSimrs = trim(
                                (string) data_get(
                                    $item,
                                    'nosep',
                                    ''
                                )
                            );

                            $noKartuBpjs = preg_replace(
                                '/[^0-9]/',
                                '',
                                trim(
                                    (string) data_get(
                                        $item,
                                        'nobpjs',
                                        ''
                                    )
                                )
                            ) ?? '';

                            $tanggalRegistrasiSep = null;

                            try {
                                if (data_get($item, 'tglregistrasi')) {
                                    $tanggalRegistrasiSep =
                                        \Carbon\Carbon::parse(
                                            data_get(
                                                $item,
                                                'tglregistrasi'
                                            )
                                        )->format('Y-m-d');
                                }
                            } catch (\Throwable $e) {
                                $tanggalRegistrasiSep = null;
                            }

                            $nomorBaris =
                                ($pasien->firstItem() ?? 1) + $index;

                            $targetSepId =
                                'hasil-sep-bpjs-' . $nomorBaris;
                        @endphp

                        <td>
                            @if($noSepSimrs !== '')
                                <span class="sep-simrs">
                                    {{ $noSepSimrs }}
                                </span>
                            @else
                                <span class="badge-sep-empty">
                                    Belum ada SEP
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="sep-bpjs-box">
                                <button
                                    type="button"
                                    class="btn-check-sep"
                                    data-target="{{ $targetSepId }}"
                                    data-no-kartu="{{ $noKartuBpjs }}"
                                    data-tanggal="{{ $tanggalRegistrasiSep }}"
                                    data-no-sep-simrs="{{ $noSepSimrs }}"
                                    data-jns-pelayanan="2"
                                    {{ $noKartuBpjs === '' ||
                                        !$tanggalRegistrasiSep
                                        ? 'disabled'
                                        : '' }}>
                                    Cek SEP BPJS
                                </button>

                                @if($noKartuBpjs === '')
                                    <span class="sub-value">
                                        Nomor kartu BPJS tidak tersedia.
                                    </span>
                                @elseif(!$tanggalRegistrasiSep)
                                    <span class="sub-value">
                                        Tanggal registrasi tidak tersedia.
                                    </span>
                                @else
                                    <span class="sub-value">
                                        Belum dilakukan pengecekan.
                                    </span>
                                @endif

                                <div
                                    id="{{ $targetSepId }}"
                                    class="sep-result">
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="patient-name">
                                {{ data_get($item, 'namapasien', '-') }}
                            </span>

                            <span class="sub-value">
                                {{ data_get($item, 'jeniskelamin', '-') }}
                            </span>
                        </td>

                        <td>
                            {{ data_get($item, 'nocm', '-') }}
                        </td>

                        <td>
                            {{ data_get($item, 'nobpjs', '-') ?: '-' }}
                        </td>

                        <td>
                            @php
                            $tglRegistrasi = data_get(
                            $item,
                            'tglregistrasi'
                            );
                            @endphp

                            @if($tglRegistrasi)
                            {{ \Carbon\Carbon::parse($tglRegistrasi)->format('d-m-Y H:i') }}
                            @else
                            -
                            @endif
                        </td>

                        <td>
                            <strong>
                                {{ data_get($item, 'namaruangan', '-') }}
                            </strong>

                            @if(data_get($item, 'antrianloket'))
                            <span class="sub-value">
                                Antrean:
                                {{ data_get($item, 'antrianloket') }}
                            </span>
                            @endif
                        </td>

                        <td>
                            <span class="badge-payment">
                                {{ data_get($item, 'kelompokpasien', '-') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="empty-state">
                            Tidak ada data registrasi pada periode tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pasien->hasPages())
        @php
        $startPage = max(1, $pasien->currentPage() - 2);
        $endPage = min(
        $pasien->lastPage(),
        $pasien->currentPage() + 2
        );
        @endphp

        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan
                {{ number_format($pasien->firstItem() ?? 0) }}
                sampai
                {{ number_format($pasien->lastItem() ?? 0) }}
                dari
                {{ number_format($pasien->total()) }}
                data
            </div>

            <nav class="pagination-list">
                @if($pasien->onFirstPage())
                <span class="pagination-item disabled">
                    ‹
                </span>
                @else
                <a
                    href="{{ $pasien->previousPageUrl() }}"
                    class="pagination-item">
                    ‹
                </a>
                @endif

                @if($startPage > 1)
                <a
                    href="{{ $pasien->url(1) }}"
                    class="pagination-item">
                    1
                </a>

                @if($startPage > 2)
                <span class="pagination-item disabled">
                    ...
                </span>
                @endif
                @endif

                @for($pageNumber = $startPage; $pageNumber <= $endPage; $pageNumber++)
                    <a
                    href="{{ $pasien->url($pageNumber) }}"
                    class="pagination-item {{ $pageNumber === $pasien->currentPage() ? 'active' : '' }}">
                    {{ $pageNumber }}
                    </a>
                    @endfor

                    @if($endPage < $pasien->lastPage())
                        @if($endPage < $pasien->lastPage() - 1)
                            <span class="pagination-item disabled">
                                ...
                            </span>
                            @endif

                            <a
                                href="{{ $pasien->url($pasien->lastPage()) }}"
                                class="pagination-item">
                                {{ $pasien->lastPage() }}
                            </a>
                            @endif

                            @if($pasien->hasMorePages())
                            <a
                                href="{{ $pasien->nextPageUrl() }}"
                                class="pagination-item">
                                ›
                            </a>
                            @else
                            <span class="pagination-item disabled">
                                ›
                            </span>
                            @endif
            </nav>
        </div>
        @endif
    </div>
</section>

<div
    id="sepDetailModal"
    class="sep-modal"
    aria-hidden="true">
    <div
        class="sep-modal-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="sepDetailTitle">
        <div class="sep-modal-header">
            <div>
                <h2 id="sepDetailTitle">
                    Detail SEP BPJS
                </h2>
                <p>
                    Perbandingan SEP SIMRS dengan SEP yang diterbitkan BPJS.
                </p>
            </div>

            <button
                type="button"
                class="sep-modal-close"
                data-close-sep-modal
                aria-label="Tutup">
                &times;
            </button>
        </div>

        <div
            id="sepDetailContent"
            class="sep-modal-body">
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkSepUrl = @json(
        route('registrasi-pasien.check-sep')
    );

    const csrfToken = @json(csrf_token());
    const modal = document.getElementById('sepDetailModal');
    const modalContent = document.getElementById(
        'sepDetailContent'
    );

    /*
     * Menyimpan hasil pengecekan tiap baris agar tombol
     * Detail tidak perlu memanggil API kembali.
     */
    const detailSepCache = new Map();

    function escapeHtml(value) {
        const div = document.createElement('div');

        div.textContent = value === null ||
            value === undefined ||
            value === ''
                ? '-'
                : String(value);

        return div.innerHTML;
    }

    function formatTanggal(value) {
        if (!value) {
            return '-';
        }

        const tanggal = String(value).substring(0, 10);
        const parts = tanggal.split('-');

        if (parts.length !== 3) {
            return escapeHtml(value);
        }

        return [
            parts[2],
            parts[1],
            parts[0]
        ].join('-');
    }

    function statusLabel(status) {
        const labels = {
            COCOK: 'SEP Cocok',
            BERBEDA: 'SEP Berbeda',
            BELUM_TERSIMPAN_SIMRS:
                'Belum Tersimpan di SIMRS',
            TIDAK_DITEMUKAN_BPJS:
                'Tidak Ditemukan di BPJS',
            BELUM_ADA_SEP: 'Belum Ada SEP',
            SEP_DITEMUKAN: 'SEP BPJS Ditemukan',
            SEP_TIDAK_DITEMUKAN:
                'SEP BPJS Tidak Ditemukan',
            TIDAK_ADA_KARTU:
                'Nomor Kartu Tidak Tersedia',
            ERROR: 'Pengecekan Gagal'
        };

        return labels[status] || status || 'Selesai';
    }

    function statusClass(status) {
        if (
            status === 'COCOK' ||
            status === 'SEP_DITEMUKAN'
        ) {
            return 'sep-status-success';
        }

        if (
            status === 'BELUM_TERSIMPAN_SIMRS' ||
            status === 'BELUM_ADA_SEP'
        ) {
            return 'sep-status-warning';
        }

        if (
            status === 'BERBEDA' ||
            status === 'TIDAK_DITEMUKAN_BPJS' ||
            status === 'ERROR'
        ) {
            return 'sep-status-error';
        }

        return 'sep-status-neutral';
    }

    function getSepBpjs(result) {
        return result.sep_bpjs || result.sep || {};
    }

    function getSepList(result) {
        const list =
            result.sep_bpjs_list ||
            result.sep_list ||
            [];

        return Array.isArray(list) ? list : [];
    }

    function renderInlineResult(
        resultBox,
        result,
        detailKey
    ) {
        const sepBpjs = getSepBpjs(result);

        const noSepBpjs =
            result.no_sep_bpjs ||
            sepBpjs.no_sep ||
            sepBpjs.noSep ||
            '';

        const hasSep = Boolean(noSepBpjs);

        let html = `
            <div class="${
                hasSep
                    ? 'sep-result-success'
                    : (
                        result.status === 'ERROR' ||
                        result.status === 'BERBEDA' ||
                        result.status ===
                            'TIDAK_DITEMUKAN_BPJS'
                            ? 'sep-result-error'
                            : 'sep-result-empty'
                    )
            }">
                <span class="sep-status-badge ${
                    statusClass(result.status)
                }">
                    ${escapeHtml(
                        statusLabel(result.status)
                    )}
                </span>
        `;

        if (hasSep) {
            html += `
                <strong class="sep-bpjs-number">
                    ${escapeHtml(noSepBpjs)}
                </strong>

                <span class="sep-detail">
                    Tanggal SEP:
                    ${formatTanggal(
                        sepBpjs.tgl_sep ||
                        sepBpjs.tglSep
                    )}
                </span>
            `;
        } else {
            html += `
                <span class="sep-detail">
                    ${escapeHtml(
                        result.message ||
                        'SEP BPJS tidak ditemukan.'
                    )}
                </span>
            `;
        }

        html += `
                <div class="sep-action-row">
                    <button
                        type="button"
                        class="btn-detail-sep"
                        data-detail-key="${escapeHtml(detailKey)}">
                        Lihat Detail
                    </button>
                </div>
            </div>
        `;

        resultBox.innerHTML = html;
    }

    function detailField(label, value) {
        return `
            <div class="sep-detail-card">
                <span class="sep-detail-label">
                    ${escapeHtml(label)}
                </span>

                <span class="sep-detail-value">
                    ${escapeHtml(value)}
                </span>
            </div>
        `;
    }

    function renderDetail(result) {
        const sepBpjs = getSepBpjs(result);
        const sepList = getSepList(result);

        const noSepSimrs =
            result.no_sep_simrs || '-';

        const noSepBpjs =
            result.no_sep_bpjs ||
            sepBpjs.no_sep ||
            sepBpjs.noSep ||
            '-';

        let html = `
            <span class="sep-status-badge ${
                statusClass(result.status)
            }">
                ${escapeHtml(statusLabel(result.status))}
            </span>

            <div class="sep-comparison">
                <div class="sep-comparison-card">
                    <span class="sep-detail-label">
                        SEP tersimpan di SIMRS
                    </span>

                    <span class="sep-detail-value">
                        ${escapeHtml(noSepSimrs)}
                    </span>
                </div>

                <div class="sep-comparison-card">
                    <span class="sep-detail-label">
                        SEP diterbitkan BPJS
                    </span>

                    <span class="sep-detail-value">
                        ${escapeHtml(noSepBpjs)}
                    </span>
                </div>
            </div>

            <div class="sep-detail-grid">
                ${detailField(
                    'Tanggal registrasi',
                    formatTanggal(
                        result.tanggal_registrasi ||
                        result.tanggal_pelayanan
                    )
                )}

                ${detailField(
                    'Tanggal SEP',
                    formatTanggal(
                        sepBpjs.tgl_sep ||
                        sepBpjs.tglSep
                    )
                )}

                ${detailField(
                    'Nomor rujukan',
                    sepBpjs.no_rujukan ||
                    sepBpjs.noRujukan ||
                    '-'
                )}

                ${detailField(
                    'Asal rujukan',
                    sepBpjs.asal_rujukan ||
                    sepBpjs.asalRujukan ||
                    '-'
                )}

                ${detailField(
                    'Poli',
                    sepBpjs.poli || '-'
                )}

                ${detailField(
                    'Diagnosis',
                    sepBpjs.diagnosa ||
                    sepBpjs.diagnosis ||
                    '-'
                )}

                ${detailField(
                    'Jenis pelayanan',
                    Number(
                        result.jns_pelayanan ||
                        sepBpjs.jns_pelayanan ||
                        sepBpjs.jnsPelayanan ||
                        0
                    ) === 1
                        ? 'Rawat Inap'
                        : 'Rawat Jalan'
                )}

                ${detailField(
                    'Sumber data BPJS',
                    result.sumber_bpjs ||
                    'MONITORING_KUNJUNGAN'
                )}
            </div>

            <div class="sep-detail-card">
                <span class="sep-detail-label">
                    Keterangan
                </span>

                <span class="sep-detail-value">
                    ${escapeHtml(
                        result.message ||
                        'Pengecekan selesai.'
                    )}
                </span>
            </div>
        `;

        if (sepList.length > 1) {
            html += `
                <h3 class="sep-list-title">
                    Seluruh SEP BPJS pada tanggal tersebut
                    (${sepList.length})
                </h3>
            `;

            sepList.forEach(function (sep, index) {
                html += `
                    <div class="sep-list-item">
                        <strong>
                            ${index + 1}.
                            ${escapeHtml(
                                sep.no_sep ||
                                sep.noSep ||
                                '-'
                            )}
                        </strong>

                        <span class="sep-detail">
                            Tanggal:
                            ${formatTanggal(
                                sep.tgl_sep ||
                                sep.tglSep
                            )}
                        </span>

                        <span class="sep-detail">
                            Nomor rujukan:
                            ${escapeHtml(
                                sep.no_rujukan ||
                                sep.noRujukan ||
                                '-'
                            )}
                        </span>

                        <span class="sep-detail">
                            Poli:
                            ${escapeHtml(
                                sep.poli || '-'
                            )}
                        </span>
                    </div>
                `;
            });
        }

        const errors = Array.isArray(result.errors)
            ? result.errors.filter(Boolean)
            : [];

        if (errors.length > 0) {
            html += `
                <div class="sep-result-error">
                    <strong>
                        Catatan dari proses pengecekan
                    </strong>

                    ${errors.map(function (error) {
                        return `
                            <span class="sep-detail">
                                ${escapeHtml(error)}
                            </span>
                        `;
                    }).join('')}
                </div>
            `;
        }

        return html;
    }

    function openModal(result) {
        modalContent.innerHTML = renderDetail(result);
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('sep-modal-open');
    }

    function closeModal() {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('sep-modal-open');
    }

    document.addEventListener(
        'click',
        async function (event) {
            const detailButton = event.target.closest(
                '.btn-detail-sep'
            );

            if (detailButton) {
                const detailKey =
                    detailButton.dataset.detailKey;

                const detail = detailSepCache.get(
                    detailKey
                );

                if (detail) {
                    openModal(detail);
                }

                return;
            }

            if (
                event.target.closest(
                    '[data-close-sep-modal]'
                ) ||
                event.target === modal
            ) {
                closeModal();

                return;
            }

            const button = event.target.closest(
                '.btn-check-sep'
            );

            if (!button) {
                return;
            }

            const targetId = button.dataset.target;
            const noKartu = button.dataset.noKartu;
            const tanggal = button.dataset.tanggal;
            const noSepSimrs =
                button.dataset.noSepSimrs || '';

            const jnsPelayanan = Number(
                button.dataset.jnsPelayanan || 2
            );

            const resultBox = document.getElementById(
                targetId
            );

            if (!resultBox) {
                return;
            }

            if (!noKartu || !tanggal) {
                resultBox.innerHTML = `
                    <div class="sep-result-error">
                        Nomor kartu atau tanggal registrasi
                        tidak tersedia.
                    </div>
                `;

                return;
            }

            const originalText =
                button.textContent.trim();

            button.disabled = true;
            button.textContent = 'Memeriksa...';

            resultBox.innerHTML = `
                <div class="sep-result-loading">
                    Memeriksa SEP pada Monitoring
                    Kunjungan BPJS...
                </div>
            `;

            try {
                const response = await fetch(
                    checkSepUrl,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',
                            'Accept':
                                'application/json',
                            'X-CSRF-TOKEN':
                                csrfToken
                        },

                        body: JSON.stringify({
                            no_kartu: noKartu,
                            tanggal_registrasi:
                                tanggal,
                            no_sep_simrs:
                                noSepSimrs,
                            jns_pelayanan:
                                jnsPelayanan
                        })
                    }
                );

                let result;

                try {
                    result = await response.json();
                } catch (jsonError) {
                    throw new Error(
                        'Respons server bukan JSON valid.'
                    );
                }

                if (
                    !response.ok ||
                    result.success === false
                ) {
                    throw new Error(
                        result.message ||
                        'Gagal memeriksa SEP BPJS.'
                    );
                }

                const detailKey =
                    targetId + '-' + Date.now();

                detailSepCache.set(
                    detailKey,
                    result
                );

                renderInlineResult(
                    resultBox,
                    result,
                    detailKey
                );
            } catch (error) {
                resultBox.innerHTML = `
                    <div class="sep-result-error">
                        <strong>
                            Pengecekan gagal
                        </strong>

                        <span class="sep-detail">
                            ${escapeHtml(error.message)}
                        </span>
                    </div>
                `;
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        }
    );

    document.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        }
    );
});
</script>
@endsection