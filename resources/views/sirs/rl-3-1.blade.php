<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        RL 3.1 - Indikator Pelayanan
    </title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

            background: #f4f7fb;

            color: #172033;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .header {
            background:
                linear-gradient(135deg,
                    #075985,
                    #0284c7);

            color: #fff;

            padding:
                28px 24px 70px;
        }

        .header-inner {
            max-width: 1380px;

            margin: auto;
        }

        .back {
            display: inline-flex;

            align-items: center;

            gap: 8px;

            color: rgba(255,
                    255,
                    255,
                    .9);

            text-decoration: none;

            font-size: 13px;

            font-weight: 650;

            margin-bottom: 27px;
        }

        .title-row {
            display: flex;

            align-items: center;

            gap: 17px;
        }

        .title-icon {
            width: 58px;

            height: 58px;

            border-radius: 16px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: rgba(255,
                    255,
                    255,
                    .15);

            font-size: 26px;
        }

        h1 {
            margin: 0;

            font-size: 27px;
        }

        .subtitle {
            margin-top: 6px;

            color: rgba(255,
                    255,
                    255,
                    .8);

            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        .container {
            max-width: 1380px;

            margin:
                -42px auto 50px;

            padding:
                0 24px;
        }


        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        .filter-card {
            background: #fff;

            border:
                1px solid #e4eaf1;

            border-radius: 16px;

            padding: 17px;

            box-shadow:
                0 7px 22px rgba(15,
                    23,
                    42,
                    .06);

            display: flex;

            align-items: end;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 20px;
        }

        .filter-left {
            display: flex;

            align-items: end;

            gap: 12px;
        }

        .form-group label {
            display: block;

            color: #475569;

            font-size: 11px;

            font-weight: 700;

            margin-bottom: 6px;
        }

        .form-control {
            height: 41px;

            border:
                1px solid #dce3eb;

            border-radius: 9px;

            background: #f8fafc;

            padding:
                0 12px;

            font-size: 13px;

            outline: none;
        }

        .form-control:focus {
            border-color: #38bdf8;

            background: #fff;

            box-shadow:
                0 0 0 3px rgba(56,
                    189,
                    248,
                    .12);
        }

        .btn {
            height: 41px;

            border: 0;

            border-radius: 9px;

            padding:
                0 17px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            cursor: pointer;

            font-size: 12px;

            font-weight: 700;
        }

        .btn-primary {
            background: #0284c7;

            color: #fff;
        }

        .period-info {
            color: #64748b;

            font-size: 12px;

            text-align: right;
        }

        .period-info strong {
            display: block;

            color: #1e293b;

            font-size: 13px;

            margin-top: 4px;
        }


        /*
        |--------------------------------------------------------------------------
        | Alert
        |--------------------------------------------------------------------------
        */

        .alert {
            border-radius: 12px;

            padding:
                14px 16px;

            margin-bottom: 20px;

            display: flex;

            gap: 10px;

            align-items: flex-start;
        }

        .alert-danger {
            background: #fef2f2;

            border:
                1px solid #fecaca;

            color: #991b1b;
        }


        /*
        |--------------------------------------------------------------------------
        | Indicator
        |--------------------------------------------------------------------------
        */

        .indicator-grid {
            display: grid;

            grid-template-columns:
                repeat(6,
                    minmax(0, 1fr));

            gap: 13px;

            margin-bottom: 21px;
        }

        .indicator-card {
            background: #fff;

            border:
                1px solid #e5eaf0;

            border-radius: 14px;

            padding: 17px;

            position: relative;

            min-height: 135px;

            transition: .2s;
        }

        .indicator-card:hover {
            transform:
                translateY(-2px);

            box-shadow:
                0 10px 24px rgba(15,
                    23,
                    42,
                    .07);
        }

        .indicator-header {
            display: flex;

            align-items: center;

            justify-content: space-between;
        }

        .indicator-code {
            font-size: 12px;

            font-weight: 800;

            color: #64748b;
        }

        .indicator-icon {
            width: 34px;

            height: 34px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #e0f2fe;

            color: #0369a1;
        }

        .indicator-value {
            margin-top: 17px;

            font-size: 26px;

            line-height: 1;

            font-weight: 800;

            color: #0f172a;
        }

        .indicator-unit {
            font-size: 12px;

            font-weight: 650;

            color: #64748b;

            margin-left: 2px;
        }

        .indicator-label {
            margin-top: 8px;

            color: #64748b;

            font-size: 11px;
        }


        /*
        |--------------------------------------------------------------------------
        | Detail
        |--------------------------------------------------------------------------
        */

        .detail-card {
            background: #fff;

            border:
                1px solid #e4eaf1;

            border-radius: 15px;

            overflow: hidden;
        }

        .detail-header {
            padding:
                16px 18px;

            border-bottom:
                1px solid #edf1f5;

            display: flex;

            align-items: center;

            gap: 9px;

            font-size: 14px;

            font-weight: 750;
        }

        .detail-grid {
            display: grid;

            grid-template-columns:
                repeat(6,
                    minmax(0, 1fr));
        }

        .detail-item {
            padding:
                18px;

            border-right:
                1px solid #edf1f5;
        }

        .detail-item:last-child {
            border-right: 0;
        }

        .detail-label {
            font-size: 11px;

            color: #64748b;

            margin-bottom: 7px;
        }

        .detail-value {
            color: #0f172a;

            font-size: 20px;

            font-weight: 750;
        }


        /*
        |--------------------------------------------------------------------------
        | Explanation
        |--------------------------------------------------------------------------
        */

        .formula-card {
            margin-top: 20px;

            background: #fff;

            border:
                1px solid #e4eaf1;

            border-radius: 15px;

            padding: 18px;
        }

        .formula-title {
            display: flex;

            align-items: center;

            gap: 8px;

            font-size: 14px;

            font-weight: 750;

            margin-bottom: 15px;
        }

        .formula-grid {
            display: grid;

            grid-template-columns:
                repeat(3,
                    minmax(0, 1fr));

            gap: 11px;
        }

        .formula-item {
            padding: 13px;

            background: #f8fafc;

            border-radius: 10px;
        }

        .formula-name {
            font-size: 12px;

            font-weight: 750;

            color: #334155;

            margin-bottom: 5px;
        }

        .formula-text {
            color: #64748b;

            font-size: 11px;

            line-height: 1.55;
        }

        .filter-group-inline {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        @media (max-width: 700px) {

            .filter-group-inline {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1150px) {

            .indicator-grid {
                grid-template-columns:
                    repeat(3,
                        minmax(0, 1fr));
            }

            .detail-grid {
                grid-template-columns:
                    repeat(3,
                        minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {

            .container {
                padding:
                    0 13px;
            }

            .filter-card {
                align-items: stretch;

                flex-direction: column;
            }

            .filter-left {
                align-items: stretch;

                flex-direction: column;
            }

            .form-control,
            .btn {
                width: 100%;
            }

            .period-info {
                text-align: left;
            }

            .indicator-grid {
                grid-template-columns:
                    repeat(2,
                        minmax(0, 1fr));
            }

            .detail-grid,
            .formula-grid {
                grid-template-columns: 1fr;
            }

            .detail-item {
                border-right: 0;

                border-bottom:
                    1px solid #edf1f5;
            }
        }
    </style>

</head>

<body>


    {{-- ================================================================ --}}
    {{-- HEADER --}}
    {{-- ================================================================ --}}

    <header class="header">

        <div class="header-inner">

            <a
                href="{{ route('sirs.menu') }}"
                class="back">

                <i class="bi bi-arrow-left"></i>

                Kembali ke SIRS Online

            </a>


            <div class="title-row">

                <div class="title-icon">

                    <i class="bi bi-graph-up-arrow"></i>

                </div>


                <div>

                    <h1>
                        RL 3.1 - Indikator Pelayanan
                    </h1>

                    <div class="subtitle">
                        Monitoring BOR, ALOS, BTO, TOI,
                        GDR dan NDR RSUD Bali Mandara
                    </div>

                </div>

            </div>

        </div>

    </header>



    <main class="container">


        {{-- ============================================================ --}}
        {{-- FILTER --}}
        {{-- ============================================================ --}}

        <form
            method="GET"
            action="{{ route('sirs.rl3-1') }}"
            class="filter-card"
            id="formFilter">

            <div class="filter-left">

                {{-- JENIS PERIODE --}}
                <div class="form-group">

                    <label>
                        JENIS PERIODE
                    </label>

                    <select
                        name="jenis_periode"
                        id="jenisPeriode"
                        class="form-control">

                        <option
                            value="tanggal"
                            {{
                        $jenisPeriode === 'tanggal'
                            ? 'selected'
                            : ''
                    }}>
                            Periode Tanggal
                        </option>

                        <option
                            value="bulan"
                            {{
                        $jenisPeriode === 'bulan'
                            ? 'selected'
                            : ''
                    }}>
                            Bulanan
                        </option>

                    </select>

                </div>


                {{-- ====================================================== --}}
                {{-- FILTER TANGGAL --}}
                {{-- ====================================================== --}}

                <div
                    id="filterTanggal"
                    class="filter-group-inline">

                    <div class="form-group">

                        <label>
                            TANGGAL AWAL
                        </label>

                        <input
                            type="date"
                            name="tgl_awal"
                            value="{{ $tglAwal }}"
                            class="form-control">

                    </div>


                    <div class="form-group">

                        <label>
                            TANGGAL AKHIR
                        </label>

                        <input
                            type="date"
                            name="tgl_akhir"
                            value="{{ $tglAkhir }}"
                            class="form-control">

                    </div>

                </div>


                {{-- ====================================================== --}}
                {{-- FILTER BULAN --}}
                {{-- ====================================================== --}}

                <div
                    id="filterBulan"
                    class="form-group">

                    <label>
                        BULAN LAPORAN
                    </label>

                    <input
                        type="month"
                        name="periode"
                        value="{{
                    $periode
                    ?: now()->format('Y-m')
                }}"
                        class="form-control">

                </div>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-search"></i>

                    Tampilkan

                </button>

            </div>


            {{-- INFORMASI PERIODE --}}

            <div class="period-info">

                Periode data

                <strong>

                    {{
                \Carbon\Carbon::parse(
                    $tglAwal
                )->format('d M Y')
            }}

                    @if($tglAwal !== $tglAkhir)

                    –

                    {{
                    \Carbon\Carbon::parse(
                        $tglAkhir
                    )->format('d M Y')
                }}

                    @endif

                </strong>

            </div>

        </form>

     


      


        {{-- ============================================================ --}}
        {{-- ERROR --}}
        {{-- ============================================================ --}}

        @if($error)

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-triangle-fill"></i>

            <div>

                <strong>
                    Data belum dapat ditampilkan
                </strong>

                <div
                    style="
                        margin-top:4px;
                        font-size:12px;
                    ">

                    {{ $error }}

                </div>

            </div>

        </div>

        @endif



        @if($data)


        {{-- ======================================================== --}}
        {{-- INDICATOR --}}
        {{-- ======================================================== --}}

        <section class="indicator-grid">


            {{-- BOR --}}

            <div class="indicator-card">

                <div class="indicator-header">

                    <span class="indicator-code">
                        BOR
                    </span>

                    <span class="indicator-icon">
                        <i class="bi bi-hospital"></i>
                    </span>

                </div>

                <div class="indicator-value">

                    {{
                        number_format(
                            (float) data_get(
                                $data,
                                'bor',
                                0
                            ),
                            2,
                            ',',
                            '.'
                        )
                    }}

                    <span class="indicator-unit">
                        %
                    </span>

                </div>

                <div class="indicator-label">
                    Bed Occupancy Rate
                </div>

            </div>


            {{-- ALOS --}}

            <div class="indicator-card">

                <div class="indicator-header">

                    <span class="indicator-code">
                        ALOS
                    </span>

                    <span class="indicator-icon">
                        <i class="bi bi-calendar2-week"></i>
                    </span>

                </div>

                <div class="indicator-value">

                    {{
                        number_format(
                            (float) data_get(
                                $data,
                                'alos',
                                0
                            ),
                            2,
                            ',',
                            '.'
                        )
                    }}

                    <span class="indicator-unit">
                        hari
                    </span>

                </div>

                <div class="indicator-label">
                    Average Length of Stay
                </div>

            </div>


            {{-- BTO --}}

            <div class="indicator-card">

                <div class="indicator-header">

                    <span class="indicator-code">
                        BTO
                    </span>

                    <span class="indicator-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </span>

                </div>

                <div class="indicator-value">

                    {{
                        number_format(
                            (float) data_get(
                                $data,
                                'bto',
                                0
                            ),
                            2,
                            ',',
                            '.'
                        )
                    }}

                    <span class="indicator-unit">
                        kali
                    </span>

                </div>

                <div class="indicator-label">
                    Bed Turn Over
                </div>

            </div>


            {{-- TOI --}}

            <div class="indicator-card">

                <div class="indicator-header">

                    <span class="indicator-code">
                        TOI
                    </span>

                    <span class="indicator-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </span>

                </div>

                <div class="indicator-value">

                    {{
                        number_format(
                            (float) data_get(
                                $data,
                                'toi',
                                0
                            ),
                            2,
                            ',',
                            '.'
                        )
                    }}

                    <span class="indicator-unit">
                        hari
                    </span>

                </div>

                <div class="indicator-label">
                    Turn Over Interval
                </div>

            </div>


            {{-- GDR --}}

            <div class="indicator-card">

                <div class="indicator-header">

                    <span class="indicator-code">
                        GDR
                    </span>

                    <span class="indicator-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </span>

                </div>

                <div class="indicator-value">

                    {{
                        number_format(
                            (float) data_get(
                                $data,
                                'gdr',
                                0
                            ),
                            2,
                            ',',
                            '.'
                        )
                    }}

                    <span class="indicator-unit">
                        ‰
                    </span>

                </div>

                <div class="indicator-label">
                    Gross Death Rate
                </div>

            </div>


            {{-- NDR --}}

            <div class="indicator-card">

                <div class="indicator-header">

                    <span class="indicator-code">
                        NDR
                    </span>

                    <span class="indicator-icon">
                        <i class="bi bi-activity"></i>
                    </span>

                </div>

                <div class="indicator-value">

                    {{
                        number_format(
                            (float) data_get(
                                $data,
                                'ndr',
                                0
                            ),
                            2,
                            ',',
                            '.'
                        )
                    }}

                    <span class="indicator-unit">
                        ‰
                    </span>

                </div>

                <div class="indicator-label">
                    Net Death Rate
                </div>

            </div>


        </section>



        {{-- ======================================================== --}}
        {{-- DATA DASAR --}}
        {{-- ======================================================== --}}

        <section class="detail-card">

            <div class="detail-header">

                <i class="bi bi-database"></i>

                Data Dasar Perhitungan

            </div>


            <div class="detail-grid">


                <div class="detail-item">

                    <div class="detail-label">
                        Lama Rawat
                    </div>

                    <div class="detail-value">

                        {{
                            number_format(
                                (float) data_get(
                                    $data,
                                    'lamarawat',
                                    0
                                ),
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </div>

                </div>


                <div class="detail-item">

                    <div class="detail-label">
                        Hari Perawatan
                    </div>

                    <div class="detail-value">

                        {{
                            number_format(
                                (float) data_get(
                                    $data,
                                    'hariperawatan',
                                    0
                                ),
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </div>

                </div>


                <div class="detail-item">

                    <div class="detail-label">
                        Pasien Keluar
                    </div>

                    <div class="detail-value">

                        {{
                            number_format(
                                (float) data_get(
                                    $data,
                                    'pasienpulang',
                                    0
                                ),
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </div>

                </div>


                <div class="detail-item">

                    <div class="detail-label">
                        Meninggal
                    </div>

                    <div class="detail-value">

                        {{
                            number_format(
                                (float) data_get(
                                    $data,
                                    'meninggal',
                                    0
                                ),
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </div>

                </div>


                <div class="detail-item">

                    <div class="detail-label">
                        Meninggal ≥ 48 Jam
                    </div>

                    <div class="detail-value">

                        {{
                            number_format(
                                (float) data_get(
                                    $data,
                                    'matilebih48',
                                    0
                                ),
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </div>

                </div>

                <div class="detail-item">

                    <div class="detail-label">
                        Jumlah Tempat Tidur
                    </div>

                    <div class="detail-value">

                        {{
            number_format(
                (int) data_get(
                    $data,
                    'jumlah_tt',
                    0
                ),
                0,
                ',',
                '.'
            )
        }}

                    </div>

                    <div
                        style="
            font-size:10px;
            color:#94a3b8;
            margin-top:4px;
        ">
                        Dasar perhitungan BOR
                    </div>

                </div>
            </div>

        </section>



        {{-- ======================================================== --}}
        {{-- FORMULA --}}
        {{-- ======================================================== --}}

        <section class="formula-card">

            <div class="formula-title">

                <i class="bi bi-calculator"></i>

                Indikator Pelayanan Rumah Sakit

            </div>


            <div class="formula-grid">

                <div class="formula-item">

                    <div class="formula-name">
                        BOR
                    </div>

                    <div class="formula-text">
                        Persentase penggunaan tempat tidur
                        dalam periode tertentu.
                    </div>

                </div>


                <div class="formula-item">

                    <div class="formula-name">
                        ALOS
                    </div>

                    <div class="formula-text">
                        Rata-rata lama pasien dirawat
                        sebelum keluar rumah sakit.
                    </div>

                </div>


                <div class="formula-item">

                    <div class="formula-name">
                        BTO
                    </div>

                    <div class="formula-text">
                        Frekuensi pemakaian satu tempat tidur
                        dalam periode laporan.
                    </div>

                </div>


                <div class="formula-item">

                    <div class="formula-name">
                        TOI
                    </div>

                    <div class="formula-text">
                        Rata-rata hari tempat tidur tidak
                        terisi setelah digunakan pasien.
                    </div>

                </div>


                <div class="formula-item">

                    <div class="formula-name">
                        GDR
                    </div>

                    <div class="formula-text">
                        Angka kematian kasar dari seluruh
                        pasien keluar rumah sakit.
                    </div>

                </div>


                <div class="formula-item">

                    <div class="formula-name">
                        NDR
                    </div>

                    <div class="formula-text">
                        Angka kematian pasien setelah
                        menjalani perawatan ≥ 48 jam.
                    </div>

                </div>

            </div>

        </section>


        @elseif(!$error)

        <div class="alert alert-danger">

            Data API tidak ditemukan untuk
            periode yang dipilih.

        </div>

        @endif


    </main>

</body>
<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const jenisPeriode =
                document.getElementById(
                    'jenisPeriode'
                );

            const filterTanggal =
                document.getElementById(
                    'filterTanggal'
                );

            const filterBulan =
                document.getElementById(
                    'filterBulan'
                );


            function updateFilter() {

                if (
                    jenisPeriode.value === 'bulan'
                ) {

                    filterTanggal.style.display =
                        'none';

                    filterBulan.style.display =
                        'block';

                } else {

                    filterTanggal.style.display =
                        'flex';

                    filterBulan.style.display =
                        'block';

                    /*
                     * Sembunyikan filter bulan.
                     */
                    filterBulan.style.display =
                        'none';
                }
            }


            jenisPeriode.addEventListener(
                'change',
                updateFilter
            );


            updateFilter();

        }
    );
</script>

</html>