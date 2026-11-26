@extends('layouts.app', ['title' => 'SIRS Online'])
@section('content')
{{-- Jhon | 2026-11-26 13:17 WITA | UBAH: layout bersama menjaga header akun, Logout, dan idle session. --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen amber SIRS selaras dashboard; warna status semantik dipertahankan. */

/* Jhon | 2026-11-26 13:17 WITA | UBAH: menu SIRS memakai kartu dan spacing yang sama dengan halaman hasil RL. */
.sirs-menu{min-height:100vh;padding:20px 24px;background:radial-gradient(circle at top left,rgba(217,119,6,.045),transparent 28%),#f8fafc;color:#0f172a;font-size:14px}
.sirs-menu .topbar,.sirs-menu .search-box,.sirs-menu .summary-card,.sirs-menu .report-card,.sirs-menu .info-box{background:#fff;border:1px solid #e2e8f0;border-radius:18px;box-shadow:0 10px 28px #0f172a0a}
.sirs-menu .topbar{padding:18px;margin-bottom:16px;background:linear-gradient(120deg,#fff,#fffbeb);display:flex;justify-content:space-between;align-items:center;gap:18px}
.sirs-menu h1{font-size:20px;font-weight:750;margin:0 0 7px;letter-spacing:-.025em}.sirs-menu .header-description{font-size:12px;color:#64748b;margin:0;line-height:1.6}
.sirs-menu .back-button{background:#0f172a;color:white;text-decoration:none;border-radius:10px;padding:9px 13px;white-space:nowrap;font-size:12px;font-weight:700}
.sirs-menu .content{width:100%;max-width:none;margin:0;padding:0}/* Jhon | 2026-11-26 13:22 WITA | UBAH: empat kartu ringkasan sejajar satu baris desktop. */
.sirs-menu .summary-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:16px}.sirs-menu .summary-card{padding:15px;display:flex;align-items:center;gap:12px}
.sirs-menu .summary-icon,.sirs-menu .section-icon{display:flex;align-items:center;justify-content:center;background:#fef3c7;color:#b45309;border-radius:11px;width:40px;height:40px;flex-shrink:0}.sirs-menu .summary-value{font-size:23px;font-weight:800}.sirs-menu .summary-label{font-size:12px;color:#64748b;margin-top:4px}
.sirs-menu .search-container,.sirs-menu .search-section{margin:16px 0}.sirs-menu .search-box{display:flex;align-items:center;gap:10px;padding:10px 14px;margin-bottom:18px}.sirs-menu input{width:100%;border:1px solid #dbe2ea;border-radius:11px;padding:11px 12px;font:inherit;background:white;min-width:0}.sirs-menu input:focus{outline:2px solid #fde68a;border-color:#d97706}
.sirs-menu .report-section{margin-bottom:22px}.sirs-menu .section-header{display:flex;justify-content:space-between;align-items:center;margin:16px 0 12px;gap:12px}.sirs-menu .section-title-box{display:flex;align-items:center;gap:10px}.sirs-menu .section-title{font-size:17px;margin:0 0 4px}.sirs-menu .section-subtitle{font-size:12px;color:#64748b}.sirs-menu .report-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.sirs-menu .report-card{padding:17px;display:flex;flex-direction:column;min-width:0}.sirs-menu .report-card:hover{border-color:#fcd34d;box-shadow:0 10px 25px #b4530910}.sirs-menu .report-card-header{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:12px}.sirs-menu .rl-code{font-weight:800;color:#0f172a;font-size:13px}.sirs-menu .status-badge,.sirs-menu .section-count{padding:4px 8px;border-radius:7px;background:#f1f5f9;color:#475569;font-size:11px}.sirs-menu .report-name{font-size:15px;font-weight:700;margin-bottom:7px}.sirs-menu .report-description{font-size:12px;color:#64748b;line-height:1.6;flex:1}.sirs-menu .report-footer{display:flex;justify-content:space-between;align-items:center;gap:10px;border-top:1px solid #f1f5f9;padding-top:12px;margin-top:14px}.sirs-menu .period-info{font-size:11px;color:#64748b}.sirs-menu .open-button{background:#b45309;color:white;border-radius:10px;padding:8px 12px;text-decoration:none;font-size:12px;font-weight:700}.sirs-menu .open-button.disabled{background:#f1f5f9;color:#94a3b8}.sirs-menu .info-box{display:flex;gap:12px;padding:16px;font-size:12px;color:#64748b;line-height:1.6}.sirs-menu .info-title{font-weight:700;color:#334155}.sirs-menu .empty-search{display:none;text-align:center;padding:30px;color:#64748b}
@media(max-width:1000px){.sirs-menu .report-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.sirs-menu{padding:12px}.sirs-menu .topbar{padding:14px;align-items:flex-start}.sirs-menu h1{font-size:18px}.sirs-menu .report-grid,.sirs-menu .summary-grid{grid-template-columns:1fr}.sirs-menu .summary-card{padding:12px}.sirs-menu .back-button{font-size:11px;padding:8px}}

/* Jhon | 2026-11-26 13:24 WITA | UBAH: aksen SIRS #7c3aed dan soft #ede9fe mengikuti kartu Dashboard Monitoring. */
.sirs-menu .btn-back,.sirs-menu .back-button{background:#b45309;color:#fff}
 .sirs-menu .report-card{border-top:3px solid #b45309}.sirs-menu .rl-code{color:#b45309}
</style>




@php

    /*
    |--------------------------------------------------------------------------
    | Rekapitulasi Kegiatan - Bulanan
    |--------------------------------------------------------------------------
    */

    $monthlyReports = [

        [
            'code' => 'RL 3.1',
            'name' => 'Indikator Pelayanan',
            'description' =>
                'Indikator BOR, ALOS, BTO, TOI, NDR dan GDR.',
            'period' => 'Bulanan',
            'auto' => true,
        ],

        [
            'code' => 'RL 3.2',
            'name' => 'Pelayanan Rawat Inap',
            'description' =>
                'Rekapitulasi kegiatan pelayanan rawat inap.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.3',
            'name' => 'Pelayanan Rawat Darurat',
            'description' =>
                'Rekapitulasi pelayanan Instalasi Gawat Darurat.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.4',
            'name' => 'Pengunjung',
            'description' =>
                'Rekapitulasi jumlah pengunjung rumah sakit.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.5',
            'name' => 'Kunjungan',
            'description' =>
                'Rekapitulasi kunjungan berdasarkan jenis pelayanan.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.6',
            'name' => 'Pelayanan Kebidanan',
            'description' =>
                'Rekapitulasi kegiatan pelayanan kebidanan.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.7',
            'name' => 'Neonatal, Bayi dan Balita',
            'description' =>
                'Kegiatan pelayanan neonatal, bayi dan balita.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.8',
            'name' => 'Pelayanan Laboratorium',
            'description' =>
                'Rekapitulasi kegiatan pemeriksaan laboratorium.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.9',
            'name' => 'Pelayanan Radiologi',
            'description' =>
                'Rekapitulasi kegiatan radiologi dan pencitraan.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.10',
            'name' => 'Pelayanan Rujukan',
            'description' =>
                'Rekapitulasi rujukan masuk dan rujukan keluar.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.12',
            'name' => 'Pelayanan Pembedahan',
            'description' =>
                'Rekapitulasi kegiatan pelayanan pembedahan.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 3.14',
            'name' => 'Pelayanan Khusus',
            'description' =>
                'Rekapitulasi berbagai kegiatan pelayanan khusus.',
            'period' => 'Bulanan',
        ],

    ];


    /*
    |--------------------------------------------------------------------------
    | Morbiditas - Bulanan
    |--------------------------------------------------------------------------
    */

    $morbidityReports = [

        [
            'code' => 'RL 4.1',
            'name' => 'Morbiditas Pasien Rawat Inap',
            'description' =>
                'Kompilasi diagnosis utama pasien rawat inap berdasarkan ICD-10.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 4.2',
            'name' => '10 Besar Penyakit Rawat Inap',
            'description' =>
                '10 besar penyakit rawat inap berdasarkan data RL 4.1.',
            'period' => 'Bulanan',
            'auto' => true,
        ],

        [
            'code' => 'RL 4.3',
            'name' => '10 Besar Kematian Rawat Inap',
            'description' =>
                '10 besar kematian penyakit rawat inap dari RL 4.1.',
            'period' => 'Bulanan',
            'auto' => true,
        ],

        [
            'code' => 'RL 5.1',
            'name' => 'Morbiditas Pasien Rawat Jalan',
            'description' =>
                'Kompilasi diagnosis pasien rawat jalan berdasarkan ICD-10.',
            'period' => 'Bulanan',
        ],

        [
            'code' => 'RL 5.2',
            'name' => '10 Besar Kasus Baru Rawat Jalan',
            'description' =>
                '10 besar kasus baru penyakit rawat jalan dari RL 5.1.',
            'period' => 'Bulanan',
            'auto' => true,
        ],

        [
            'code' => 'RL 5.3',
            'name' => '10 Besar Kunjungan Rawat Jalan',
            'description' =>
                '10 besar kunjungan penyakit rawat jalan dari RL 5.1.',
            'period' => 'Bulanan',
            'auto' => true,
        ],

    ];


    /*
    |--------------------------------------------------------------------------
    | Tahunan
    |--------------------------------------------------------------------------
    */

    $yearlyReports = [

        [
            'code' => 'RL 3.11',
            'name' => 'Pelayanan Gigi dan Mulut',
            'description' =>
                'Rekapitulasi kegiatan pelayanan kesehatan gigi dan mulut.',
            'period' => 'Tahunan',
        ],

        [
            'code' => 'RL 3.13',
            'name' => 'Pelayanan Rehabilitasi Medik',
            'description' =>
                'Rekapitulasi kegiatan pelayanan rehabilitasi medik.',
            'period' => 'Tahunan',
        ],

        [
            'code' => 'RL 3.15',
            'name' => 'Pelayanan Kesehatan Jiwa',
            'description' =>
                'Rekapitulasi kegiatan pelayanan kesehatan jiwa.',
            'period' => 'Tahunan',
        ],

        [
            'code' => 'RL 3.16',
            'name' => 'Pelayanan Keluarga Berencana',
            'description' =>
                'Rekapitulasi kegiatan pelayanan keluarga berencana.',
            'period' => 'Tahunan',
        ],

        [
            'code' => 'RL 3.17',
            'name' => 'Farmasi RS - Pengadaan Obat',
            'description' =>
                'Rekapitulasi kegiatan pengadaan obat rumah sakit.',
            'period' => 'Tahunan',
        ],

        [
            'code' => 'RL 3.18',
            'name' => 'Farmasi RS - Resep',
            'description' =>
                'Rekapitulasi kegiatan pelayanan resep farmasi rumah sakit.',
            'period' => 'Tahunan',
        ],

        [
            'code' => 'RL 3.19',
            'name' => 'Cara Bayar',
            'description' =>
                'Rekapitulasi pasien berdasarkan kelompok cara pembayaran.',
            'period' => 'Tahunan',
        ],

    ];


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    $routeName = function ($code) {

        return 'sirs.' .
            strtolower(
                str_replace(
                    [' ', '.'],
                    ['', '-'],
                    $code
                )
            );

    };

@endphp


<div class="sirs-menu">

    {{-- HEADER --}}
    {{-- Jhon | 2026-11-26 13:17 WITA | UBAH: header ringkas selaras kartu judul hasil laporan. --}}
    <header class="topbar"><div><h1>Monitoring SIRS Online</h1><p class="header-description">Pilih laporan RL yang ingin ditampilkan.</p></div><a href="{{ route('menu') }}" class="back-button">Kembali ke Menu</a></header>


    {{-- CONTENT --}}
    <main class="content">


        {{-- SUMMARY --}}
        <div class="summary-grid">

            <div class="summary-card">

                <div class="summary-icon blue">
                    <i class="bi bi-calendar-month"></i>
                </div>

                <div>
                    <div class="summary-value">
                        {{ count($monthlyReports) }}
                    </div>

                    <div class="summary-label">
                        RL Kegiatan Bulanan
                    </div>
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon green">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>

                <div>
                    <div class="summary-value">
                        {{ count($morbidityReports) }}
                    </div>

                    <div class="summary-label">
                        RL Morbiditas
                    </div>
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon orange">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <div>
                    <div class="summary-value">
                        {{ count($yearlyReports) }}
                    </div>

                    <div class="summary-label">
                        RL Tahunan
                    </div>
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon purple">
                    <i class="bi bi-files"></i>
                </div>

                <div>
                    <div class="summary-value">
                        {{
                            count($monthlyReports)
                            + count($morbidityReports)
                            + count($yearlyReports)
                        }}
                    </div>

                    <div class="summary-label">
                        Total Formulir
                    </div>
                </div>

            </div>

        </div>


        {{-- SEARCH --}}
        <div class="toolbar">

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchReport"
                    placeholder="Cari RL atau nama laporan..."
                    autocomplete="off"
                >

            </div>

            <div class="toolbar-info">
                Pelaporan SIRS Online Revisi 6.3
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- BULANAN --}}
        {{-- ========================================================= --}}

        <section class="report-section">

            <div class="section-header">

                <div class="section-title-box">

                    <div class="section-icon monthly">
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <div>

                        <h2 class="section-title">
                            Rekapitulasi Kegiatan
                        </h2>

                        <div class="section-subtitle">
                            Laporan pelayanan periode bulanan
                        </div>

                    </div>

                </div>

            </div>


            <div class="report-grid">

                @foreach($monthlyReports as $report)

                    @php
                        $name = $routeName($report['code']);
                        $hasRoute = \Illuminate\Support\Facades\Route::has($name);
                    @endphp

                    <div
                        class="report-card report-item"
                        data-search="{{
                            strtolower(
                                $report['code'] . ' ' .
                                $report['name']
                            )
                        }}"
                    >

                        <div class="report-card-header">

                            <span class="rl-code">
                                {{ $report['code'] }}
                            </span>

                            @if(!empty($report['auto']))

                                <span class="status-badge auto">
                                    Otomatis
                                </span>

                            @else

                                <span class="status-badge monthly">
                                    Bulanan
                                </span>

                            @endif

                        </div>


                        <div class="report-name">
                            {{ $report['name'] }}
                        </div>


                        <div class="report-description">
                            {{ $report['description'] }}
                        </div>


                        <div class="report-footer">

                            <div class="period-info">
                                <i class="bi bi-calendar2-week"></i>
                                {{ $report['period'] }}
                            </div>


                            @if($hasRoute)

                                <a
                                    href="{{ route($name) }}"
                                    class="open-button"
                                >
                                    Buka
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                            @else

                                <span
                                    class="open-button disabled"
                                    title="Route modul belum dibuat"
                                >
                                    Belum dibuat
                                </span>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- MORBIDITAS --}}
        {{-- ========================================================= --}}

        <section class="report-section">

            <div class="section-header">

                <div class="section-title-box">

                    <div class="section-icon morbidity">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div>

                        <h2 class="section-title">
                            Morbiditas Pasien
                        </h2>

                        <div class="section-subtitle">
                            Rawat inap dan rawat jalan
                        </div>

                    </div>

                </div>

            </div>


            <div class="report-grid">

                @foreach($morbidityReports as $report)

                    @php
                        $name = $routeName($report['code']);
                        $hasRoute = \Illuminate\Support\Facades\Route::has($name);
                    @endphp

                    <div
                        class="report-card report-item"
                        data-search="{{
                            strtolower(
                                $report['code'] . ' ' .
                                $report['name']
                            )
                        }}"
                    >

                        <div class="report-card-header">

                            <span class="rl-code">
                                {{ $report['code'] }}
                            </span>

                            @if(!empty($report['auto']))

                                <span class="status-badge auto">
                                    View Otomatis
                                </span>

                            @else

                                <span class="status-badge monthly">
                                    Bulanan
                                </span>

                            @endif

                        </div>


                        <div class="report-name">
                            {{ $report['name'] }}
                        </div>


                        <div class="report-description">
                            {{ $report['description'] }}
                        </div>


                        <div class="report-footer">

                            <div class="period-info">
                                <i class="bi bi-calendar2-week"></i>
                                {{ $report['period'] }}
                            </div>


                            @if($hasRoute)

                                <a
                                    href="{{ route($name) }}"
                                    class="open-button"
                                >
                                    Buka
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                            @else

                                <span class="open-button disabled">
                                    Belum dibuat
                                </span>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- TAHUNAN --}}
        {{-- ========================================================= --}}

        <section class="report-section">

            <div class="section-header">

                <div class="section-title-box">

                    <div class="section-icon yearly">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <div>

                        <h2 class="section-title">
                            Rekapitulasi Tahunan
                        </h2>

                        <div class="section-subtitle">
                            Laporan yang direkapitulasi satu tahun
                        </div>

                    </div>

                </div>

            </div>


            <div class="report-grid">

                @foreach($yearlyReports as $report)

                    @php
                        $name = $routeName($report['code']);
                        $hasRoute = \Illuminate\Support\Facades\Route::has($name);
                    @endphp

                    <div
                        class="report-card report-item"
                        data-search="{{
                            strtolower(
                                $report['code'] . ' ' .
                                $report['name']
                            )
                        }}"
                    >

                        <div class="report-card-header">

                            <span class="rl-code">
                                {{ $report['code'] }}
                            </span>

                            <span class="status-badge yearly">
                                Tahunan
                            </span>

                        </div>


                        <div class="report-name">
                            {{ $report['name'] }}
                        </div>


                        <div class="report-description">
                            {{ $report['description'] }}
                        </div>


                        <div class="report-footer">

                            <div class="period-info">
                                <i class="bi bi-calendar2-check"></i>
                                {{ $report['period'] }}
                            </div>


                            @if($hasRoute)

                                <a
                                    href="{{ route($name) }}"
                                    class="open-button"
                                >
                                    Buka
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                            @else

                                <span class="open-button disabled">
                                    Belum dibuat
                                </span>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </section>


        <div class="empty-search" id="emptySearch">

            <i
                class="bi bi-search"
                style="font-size:28px;"
            ></i>

            <div style="margin-top:10px;font-weight:700;">
                Laporan tidak ditemukan
            </div>

        </div>


        {{-- INFORMATION --}}
        <div class="info-box">

            <i class="bi bi-info-circle-fill"></i>

            <div>

                <div class="info-title">
                    Informasi Pelaporan SIRS 6.3
                </div>

                <div class="info-text">
                    Pelaporan bulanan disampaikan paling lambat
                    tanggal 10 bulan berikutnya. Pelaporan tahunan
                    disampaikan paling lambat tanggal 31 Maret
                    tahun berikutnya.
                </div>

            </div>

        </div>

    </main>

</div>


<script>

    const searchInput = document.getElementById('searchReport');
    const items = document.querySelectorAll('.report-item');
    const sections = document.querySelectorAll('.report-section');
    const emptySearch = document.getElementById('emptySearch');

    searchInput.addEventListener('input', function () {

        const keyword = this.value
            .toLowerCase()
            .trim();

        let totalVisible = 0;

        items.forEach(function (item) {

            const text = item.dataset.search || '';

            const visible = text.includes(keyword);

            item.style.display = visible
                ? ''
                : 'none';

            if (visible) {
                totalVisible++;
            }

        });


        sections.forEach(function (section) {

            const visibleItems =
                section.querySelectorAll(
                    '.report-item:not([style*="display: none"])'
                );

            section.style.display =
                visibleItems.length > 0
                    ? ''
                    : 'none';

        });


        emptySearch.style.display =
            totalVisible === 0
                ? 'block'
                : 'none';

    });

</script>



@endsection
