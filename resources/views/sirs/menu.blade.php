<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIRS Online 6.3</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            background: #f5f7fb;
            color: #172033;
        }

        .page {
            min-height: 100vh;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .topbar {
            background: linear-gradient(
                135deg,
                #075985 0%,
                #0369a1 50%,
                #0284c7 100%
            );
            color: #fff;
            padding: 34px 24px 80px;
        }

        .topbar-inner {
            max-width: 1380px;
            margin: auto;
        }

        .top-navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 32px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: rgba(255,255,255,.9);
            font-size: 14px;
            font-weight: 600;
        }

        .back-button:hover {
            color: #fff;
        }

        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-radius: 99px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 12px;
            font-weight: 700;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .header-icon {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 29px;
            flex-shrink: 0;
        }

        h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.2;
            font-weight: 750;
            letter-spacing: -.5px;
        }

        .header-description {
            margin: 8px 0 0;
            color: rgba(255,255,255,.82);
            font-size: 14px;
            line-height: 1.6;
        }

        /*
        |--------------------------------------------------------------------------
        | Main
        |--------------------------------------------------------------------------
        */

        .content {
            max-width: 1380px;
            margin: -48px auto 0;
            padding: 0 24px 50px;
            position: relative;
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #e7edf4;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .summary-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
        }

        .summary-icon.blue {
            background: #e0f2fe;
            color: #0369a1;
        }

        .summary-icon.green {
            background: #dcfce7;
            color: #15803d;
        }

        .summary-icon.orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .summary-icon.purple {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .summary-value {
            font-size: 21px;
            font-weight: 750;
            color: #0f172a;
        }

        .summary-label {
            margin-top: 3px;
            font-size: 12px;
            color: #64748b;
        }

        /*
        |--------------------------------------------------------------------------
        | Toolbar
        |--------------------------------------------------------------------------
        */

        .toolbar {
            background: #fff;
            border: 1px solid #e6ebf2;
            border-radius: 16px;
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 24px;
        }

        .search-box {
            position: relative;
            width: 100%;
            max-width: 440px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-box input {
            width: 100%;
            border: 1px solid #dce3eb;
            background: #f8fafc;
            border-radius: 11px;
            outline: none;
            height: 43px;
            padding: 0 15px 0 42px;
            font-size: 14px;
        }

        .search-box input:focus {
            border-color: #38bdf8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(56,189,248,.12);
        }

        .toolbar-info {
            font-size: 12px;
            color: #64748b;
            text-align: right;
        }

        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        .report-section {
            margin-bottom: 32px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .section-title-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-icon {
            width: 39px;
            height: 39px;
            border-radius: 11px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .section-icon.monthly {
            background: #e0f2fe;
            color: #0369a1;
        }

        .section-icon.morbidity {
            background: #dcfce7;
            color: #15803d;
        }

        .section-icon.yearly {
            background: #fef3c7;
            color: #b45309;
        }

        .section-title {
            margin: 0;
            font-size: 17px;
            font-weight: 750;
        }

        .section-subtitle {
            margin-top: 3px;
            font-size: 12px;
            color: #64748b;
        }

        /*
        |--------------------------------------------------------------------------
        | Report Card
        |--------------------------------------------------------------------------
        */

        .report-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .report-card {
            background: #fff;
            border: 1px solid #e4eaf1;
            border-radius: 15px;
            padding: 17px;
            transition: all .2s ease;
            display: flex;
            flex-direction: column;
            min-height: 175px;
        }

        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
            border-color: #bae6fd;
        }

        .report-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 11px;
        }

        .rl-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 66px;
            height: 31px;
            padding: 0 10px;
            border-radius: 8px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 12px;
            font-weight: 800;
        }

        .status-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 5px 8px;
            border-radius: 99px;
            white-space: nowrap;
        }

        .status-badge.monthly {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-badge.yearly {
            background: #fef3c7;
            color: #a16207;
        }

        .status-badge.auto {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .report-name {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.45;
            color: #1e293b;
            margin-bottom: 7px;
        }

        .report-description {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .report-footer {
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid #f0f3f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .period-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #64748b;
        }

        .open-button {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            border: 0;
            border-radius: 8px;
            background: #0284c7;
            color: #fff;
            padding: 8px 11px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .open-button:hover {
            background: #0369a1;
        }

        .open-button.disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        /*
        |--------------------------------------------------------------------------
        | Information
        |--------------------------------------------------------------------------
        */

        .info-box {
            display: flex;
            gap: 13px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 16px 18px;
            margin-top: 22px;
            color: #1e40af;
        }

        .info-box > i {
            font-size: 19px;
            margin-top: 1px;
        }

        .info-title {
            font-size: 13px;
            font-weight: 750;
            margin-bottom: 5px;
        }

        .info-text {
            font-size: 12px;
            line-height: 1.6;
            color: #475569;
        }

        .empty-search {
            display: none;
            text-align: center;
            padding: 35px;
            background: #fff;
            border-radius: 15px;
            color: #64748b;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1050px) {
            .report-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            .topbar {
                padding: 25px 16px 65px;
            }

            .content {
                padding-left: 14px;
                padding-right: 14px;
            }

            .header-icon {
                width: 52px;
                height: 52px;
            }

            h1 {
                font-size: 23px;
            }

            .header-description {
                font-size: 12px;
            }

            .summary-grid,
            .report-grid {
                grid-template-columns: 1fr;
            }

            .toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .search-box {
                max-width: none;
            }

            .toolbar-info {
                text-align: left;
            }
        }
    </style>
</head>

<body>

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


<div class="page">

    {{-- HEADER --}}
    <header class="topbar">

        <div class="topbar-inner">

            <div class="top-navigation">

                <a href="{{ url('/') }}" class="back-button">
                    <i class="bi bi-arrow-left"></i>
                    Menu Utama
                </a>

                <div class="version-badge">
                    <i class="bi bi-shield-check"></i>
                    SIRS Revisi 6.3
                </div>

            </div>


            <div class="header-content">

                <div class="header-title">

                    <div class="header-icon">
                        <i class="bi bi-hospital"></i>
                    </div>

                    <div>

                        <h1>
                            Monitoring SIRS Online
                        </h1>

                        <p class="header-description">
                            Rekapitulasi dan monitoring pelaporan
                            Sistem Informasi Rumah Sakit
                            sesuai SIRS Revisi 6.3
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </header>


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

</body>
</html>