@extends('layouts.app', ['title' => 'Monitoring Bridging BPJS'])

@section('content')

<style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen biru BPJS selaras dashboard; warna status semantik dipertahankan. */

    .monitor-stack {
        display: grid;
        gap: 16px;
    }

    .monitor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .monitor-header-left {
        flex: 1;
        min-width: 220px;
    }

    .monitor-header-left h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .monitor-header-left p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .monitor-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .monitor-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .monitor-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .monitor-card h3 {
        margin: 0 0 10px;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .monitor-service-name {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .monitor-value {
        margin-top: 14px;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .monitor-value small {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }

    .monitor-meta {
        margin-top: 10px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .monitor-meta strong {
        color: #334155;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.2px;
    }

    .status-up {
        background: #dcfce7;
        color: #166534;
    }

    .status-degraded {
        background: #ffedd5;
        color: #c2410c;
    }

    .status-down {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-unknown {
        background: #e2e8f0;
        color: #475569;
    }

    .overall {
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .overall-title {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .overall-status {
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .monitor-table {
        width: 100%;
        border-collapse: collapse;
    }

    .monitor-table th,
    .monitor-table td {
        padding: 11px 12px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
        vertical-align: top;
    }

    .monitor-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #64748b;
        background: #f8fafc;
        font-weight: 800;
    }

    .monitor-table td {
        font-size: 13px;
        color: #334155;
    }

    .monitor-table tbody tr:hover {
        background: #f8fafc;
    }

    .message-column {
        white-space: normal !important;
        min-width: 250px;
        max-width: 400px;
        line-height: 1.5;
    }

    .filter-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: grid;
        gap: 5px;
        min-width: 180px;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }

    .form-control {
        min-height: 40px;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        padding: 8px 10px;
        background: #ffffff;
        color: #334155;
        outline: none;
        width: 100%;
    }

    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }

    .btn-monitor,
    .btn-back {
        min-height: 40px;
        border: 0;
        border-radius: 9px;
        padding: 9px 14px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-primary:hover {
        background: #1d4ed8;
        color: #ffffff;
    }

    .btn-secondary,
    .btn-back {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-secondary:hover,
    .btn-back:hover {
        background: #cbd5e1;
        color: #0f172a;
        text-decoration: none;
    }

    .alert-success-monitor {
        padding: 12px 14px;
        border-radius: 10px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        font-size: 13px;
        font-weight: 600;
    }

    .empty-state {
        color: #64748b;
        font-size: 13px;
        text-align: center;
        padding: 24px 10px;
    }

    .pagination-wrapper {
        margin-top: 16px;
    }

    @media (max-width: 1100px) {
        .monitor-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .monitor-header {
            align-items: stretch;
        }

        .monitor-header-actions {
            width: 100%;
        }

        .monitor-header-actions form {
            flex: 1;
        }

        .monitor-header-actions .btn-monitor,
        .monitor-header-actions .btn-back {
            width: 100%;
        }

        .filter-group {
            width: 100%;
        }

        .filter-row .btn-monitor,
        .filter-row .btn-secondary {
            width: 100%;
        }
    }

    @media (max-width: 600px) {
        .monitor-grid {
            grid-template-columns: 1fr;
        }

        .monitor-header-left h1 {
            font-size: 20px;
        }

        .overall {
            align-items: flex-start;
        }
    }
</style>


{{-- =========================================================
    STATUS KESELURUHAN
    Kompatibel PHP 7.4
========================================================= --}}
@php
    $overallClass = 'status-unknown';
    $overallLabel = 'Belum Ada Data';

    switch ($overallStatus) {
        case 'UP':
            $overallClass = 'status-up';
            $overallLabel = 'Bridging Normal';
            break;

        case 'DEGRADED':
            $overallClass = 'status-degraded';
            $overallLabel = 'Bridging Lambat';
            break;

        case 'DOWN':
            $overallClass = 'status-down';
            $overallLabel = 'Bridging Bermasalah';
            break;

        default:
            $overallClass = 'status-unknown';
            $overallLabel = 'Belum Ada Data';
            break;
    }
@endphp


<div class="monitor-stack">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="monitor-header">

        <div class="monitor-header-left">
            <h1>Monitoring Bridging BPJS</h1>

            <p>
                Monitoring koneksi, status layanan,
                dan waktu respons bridging BPJS
            </p>
        </div>

        <div class="monitor-header-actions">

            <a
                href="{{ route('bpjs-antrean.menu') }}"
                class="btn-back">
                Kembali ke Menu
            </a>

            <form
                method="POST"
                action="{{ route('bpjs-monitoring.check-now') }}">

                @csrf

                <button
                    type="submit"
                    class="btn-monitor btn-primary">
                    Cek Sekarang
                </button>

            </form>

        </div>

    </div>


    {{-- =====================================================
        FLASH MESSAGE
    ====================================================== --}}
    @if(session('success'))

        <div class="alert-success-monitor">
            {{ session('success') }}
        </div>

    @endif


    {{-- =====================================================
        STATUS KESELURUHAN
    ====================================================== --}}
    <div class="overall">

        <div>

            <div class="overall-title">
                Status Keseluruhan Bridging
            </div>

            <div>
                <span class="status-badge {{ $overallClass }}">
                    {{ $overallLabel }}
                </span>
            </div>

        </div>

        <div style="font-size:12px; color:#64748b;">
            Status berdasarkan hasil pemeriksaan layanan terbaru
        </div>

    </div>


    {{-- =====================================================
        STATUS TERBARU SETIAP LAYANAN
    ====================================================== --}}
    <div class="monitor-grid">

        @forelse($latest as $item)

            @php
                $statusClass = 'status-unknown';

                switch ($item->status) {
                    case 'UP':
                        $statusClass = 'status-up';
                        break;

                    case 'DEGRADED':
                        $statusClass = 'status-degraded';
                        break;

                    case 'DOWN':
                        $statusClass = 'status-down';
                        break;

                    default:
                        $statusClass = 'status-unknown';
                        break;
                }
            @endphp

            <div class="monitor-card">

                <div class="monitor-service-name">
                    {{ $item->service }}
                </div>

                <div>
                    <span class="status-badge {{ $statusClass }}">
                        {{ $item->status ?: 'UNKNOWN' }}
                    </span>
                </div>

                <div class="monitor-value">
                    {{ number_format($item->response_time_ms ?? 0) }}
                    <small>ms</small>
                </div>

                <div class="monitor-meta">

                    Kode BPJS:
                    <strong>
                        {{ $item->bpjs_code ?? '-' }}
                    </strong>

                    <br>

                    Data:
                    <strong>
                        {{ $item->payload_count ?? '-' }}
                    </strong>

                    <br>

                    Diperiksa:
                    <strong>
                        @if($item->checked_at)
                            {{ optional($item->checked_at)->format('d-m-Y H:i:s') }}
                        @else
                            -
                        @endif
                    </strong>

                    <br>

                    Pesan:
                    <strong>
                        {{ $item->message ?? '-' }}
                    </strong>

                </div>

            </div>

        @empty

            <div
                class="monitor-card"
                style="grid-column:1/-1;">

                <div class="empty-state">
                    Belum ada hasil monitoring.
                    Tekan tombol
                    <strong>Cek Sekarang</strong>
                    untuk melakukan pemeriksaan layanan BPJS.
                </div>

            </div>

        @endforelse

    </div>


    {{-- =====================================================
        FILTER HISTORI
    ====================================================== --}}
    <div class="monitor-card">

        <h3>Filter Histori</h3>

        <form
            method="GET"
            action="{{ route('bpjs-monitoring.index') }}"
            class="filter-row">

            {{-- Layanan --}}
            <div class="filter-group">

                <label for="service">
                    Layanan
                </label>

                <select
                    name="service"
                    id="service"
                    class="form-control">

                    <option value="">
                        Semua layanan
                    </option>

                    @foreach($services as $service)

                        <option
                            value="{{ $service }}"
                            {{ request('service') === $service ? 'selected' : '' }}>
                            {{ $service }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Status --}}
            <div class="filter-group">

                <label for="status">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    class="form-control">

                    <option value="">
                        Semua status
                    </option>

                    <option
                        value="UP"
                        {{ request('status') === 'UP' ? 'selected' : '' }}>
                        UP
                    </option>

                    <option
                        value="DEGRADED"
                        {{ request('status') === 'DEGRADED' ? 'selected' : '' }}>
                        DEGRADED
                    </option>

                    <option
                        value="DOWN"
                        {{ request('status') === 'DOWN' ? 'selected' : '' }}>
                        DOWN
                    </option>

                </select>

            </div>


            {{-- Tombol Filter --}}
            <button
                type="submit"
                class="btn-monitor btn-primary">
                Filter
            </button>


            {{-- Tombol Reset --}}
            <a
                href="{{ route('bpjs-monitoring.index') }}"
                class="btn-monitor btn-secondary">
                Reset
            </a>

        </form>

    </div>


    {{-- =====================================================
        HISTORI MONITORING
    ====================================================== --}}
    <div class="monitor-card">

        <h3>
            Histori Monitoring
        </h3>

        <div class="table-wrapper">

            <table class="monitor-table">

                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Layanan</th>
                        <th>Status</th>
                        <th>Kode BPJS</th>
                        <th>Respons</th>
                        <th>Jumlah Data</th>
                        <th>Pesan</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($history as $item)

                        @php
                            $statusClass = 'status-unknown';

                            switch ($item->status) {
                                case 'UP':
                                    $statusClass = 'status-up';
                                    break;

                                case 'DEGRADED':
                                    $statusClass = 'status-degraded';
                                    break;

                                case 'DOWN':
                                    $statusClass = 'status-down';
                                    break;

                                default:
                                    $statusClass = 'status-unknown';
                                    break;
                            }
                        @endphp

                        <tr>

                            {{-- WAKTU --}}
                            <td>
                                @if($item->checked_at)
                                    {{ optional($item->checked_at)->format('d-m-Y H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>


                            {{-- LAYANAN --}}
                            <td>
                                <strong>
                                    {{ $item->service }}
                                </strong>
                            </td>


                            {{-- STATUS --}}
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $item->status ?: 'UNKNOWN' }}
                                </span>
                            </td>


                            {{-- KODE BPJS --}}
                            <td>
                                {{ $item->bpjs_code ?? '-' }}
                            </td>


                            {{-- RESPONSE TIME --}}
                            <td>
                                {{ number_format($item->response_time_ms ?? 0) }}
                                ms
                            </td>


                            {{-- JUMLAH DATA --}}
                            <td>
                                {{ $item->payload_count ?? '-' }}
                            </td>


                            {{-- PESAN --}}
                            <td class="message-column">
                                {{ $item->message ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7">

                                <div class="empty-state">
                                    Belum ada histori monitoring.
                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =================================================
            PAGINATION
        ================================================== --}}
        @if(method_exists($history, 'links'))

            <div class="pagination-wrapper">
                {{ $history->appends(request()->query())->links() }}
            </div>

        @endif

    </div>

</div>


{{-- =========================================================
    AUTO REFRESH
    Refresh setiap 60 detik
========================================================= --}}
<script>
    setTimeout(function () {
        window.location.reload();
    }, 60000);
</script>

@endsection