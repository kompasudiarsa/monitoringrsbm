@extends('layouts.app', ['title' => 'Menu Monitoring'])

@section('content')

<style>
    .portal-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px 40px;
    }

    .portal-header {
        text-align: center;
        margin-bottom: 36px;
    }

    .portal-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 7px 14px;
        margin-bottom: 14px;

        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;

        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .portal-header h1 {
        margin: 0;
        color: #0f172a;
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .portal-header p {
        max-width: 650px;
        margin: 12px auto 0;

        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .menu-card {
        position: relative;
        display: flex;
        flex-direction: column;

        min-height: 270px;
        padding: 26px;

        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;

        text-decoration: none;
        color: inherit;

        overflow: hidden;

        transition:
            transform .2s ease,
            box-shadow .2s ease,
            border-color .2s ease;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        border-color: #cbd5e1;

        box-shadow:
            0 20px 40px rgba(15, 23, 42, .08),
            0 4px 12px rgba(15, 23, 42, .05);
    }

    .menu-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;

        width: 100%;
        height: 5px;

        background: var(--accent);
    }

    .icon-box {
        width: 62px;
        height: 62px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 24px;

        border-radius: 18px;
        background: var(--soft);
        color: var(--accent);
    }

    .icon-box svg {
        width: 31px;
        height: 31px;
    }

    .menu-card h2 {
        margin: 0 0 10px;

        color: #0f172a;
        font-size: 20px;
        font-weight: 800;
    }

    .menu-description {
        margin: 0;
        flex: 1;

        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .menu-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;

        margin-top: 24px;
        padding-top: 18px;

        border-top: 1px solid #f1f5f9;

        color: var(--accent);
        font-size: 13px;
        font-weight: 700;
    }

    .arrow {
        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 10px;
        background: var(--soft);

        transition: transform .2s ease;
    }

    .menu-card:hover .arrow {
        transform: translateX(4px);
    }

    .card-bpjs {
        --accent: #0284c7;
        --soft: #e0f2fe;
    }

    .card-satusehat {
        --accent: #059669;
        --soft: #d1fae5;
    }

    .card-sirs {
        --accent: #7c3aed;
        --soft: #ede9fe;
    }

    @media (max-width: 900px) {
        .menu-grid {
            grid-template-columns: 1fr;
        }

        .menu-card {
            min-height: auto;
        }
    }

    @media (max-width: 576px) {
        .portal-wrapper {
            padding: 18px 12px 30px;
        }

        .portal-header {
            text-align: left;
            margin-bottom: 24px;
        }

        .portal-header p {
            margin-left: 0;
        }

        .menu-card {
            padding: 22px;
            border-radius: 18px;
        }
    }
</style>

<div class="portal-wrapper">

    {{-- Header --}}
    <div class="portal-header">

        <div class="portal-eyebrow">
            Sistem Monitoring Terintegrasi
        </div>

        <h1>
            Dashboard Monitoring
        </h1>

        <p>
            Pilih layanan monitoring untuk melakukan pemantauan
            integrasi data dan layanan eksternal rumah sakit.
        </p>

    </div>


    {{-- Menu --}}
    <div class="menu-grid">

        {{-- BPJS --}}
        <a href="{{ url('/bpjs-antrean') }}"
           class="menu-card card-bpjs">

            <div class="icon-box">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 003.741-.479
                             3 3 0 00-4.682-2.72m.94 3.198
                             .001.031c0 .225-.012.447-.037.666
                             A11.944 11.944 0 0112 21
                             c-2.17 0-4.207-.576-5.963-1.584
                             A6.062 6.062 0 016 18.719
                             m12 0a5.971 5.971 0 00-.941-3.197
                             m0 0A5.995 5.995 0 0012 12.75
                             a5.995 5.995 0 00-5.058 2.772
                             m0 0a3 3 0 00-4.681 2.72
                             8.986 8.986 0 003.74.477
                             m.94-3.197a5.971 5.971 0 00-.94 3.197
                             M15 6.75a3 3 0 11-6 0
                             3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0
                             2.25 2.25 0 014.5 0zm-13.5 0
                             a2.25 2.25 0 11-4.5 0
                             2.25 2.25 0 014.5 0z" />
                </svg>

            </div>

            <h2>
                Monitoring BPJS
            </h2>

            <p class="menu-description">
                Monitoring antrean BPJS, SEP, surat kontrol,
                bridging VClaim, registrasi pasien serta
                sinkronisasi data SIMRS dengan BPJS.
            </p>

            <div class="menu-footer">

                <span>Buka Monitoring</span>

                <span class="arrow">
                    →
                </span>

            </div>

        </a>


        {{-- SATUSEHAT --}}
        <a href="{{ url('/satusehat') }}"
           class="menu-card card-satusehat">

            <div class="icon-box">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 6v12m6-6H6
                             M21 12a9 9 0 11-18 0
                             9 9 0 0118 0z" />

                </svg>

            </div>

            <h2>
                Monitoring SATUSEHAT
            </h2>

            <p class="menu-description">
                Monitoring proses integrasi SATUSEHAT,
                pengiriman resource FHIR, status transaksi,
                error integrasi dan sinkronisasi data pelayanan.
            </p>

            <div class="menu-footer">

                <span>Buka Monitoring</span>

                <span class="arrow">
                    →
                </span>

            </div>

        </a>


        {{-- SIRS --}}
        <a href="{{ url('/sirs-online') }}"
           class="menu-card card-sirs">

            <div class="icon-box">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M3 13.125C3 12.504 3.504 12 4.125 12
                             h2.25c.621 0 1.125.504 1.125 1.125
                             v6.75C7.5 20.496 6.996 21 6.375 21
                             h-2.25A1.125 1.125 0 013 19.875
                             v-6.75z
                             M9.75 8.625c0-.621.504-1.125
                             1.125-1.125h2.25c.621 0
                             1.125.504 1.125 1.125v11.25
                             c0 .621-.504 1.125-1.125 1.125
                             h-2.25a1.125 1.125 0 01-1.125-1.125
                             V8.625z
                             M16.5 4.125C16.5 3.504 17.004 3
                             17.625 3h2.25C20.496 3 21 3.504
                             21 4.125v15.75c0 .621-.504
                             1.125-1.125 1.125h-2.25
                             a1.125 1.125 0 01-1.125-1.125
                             V4.125z" />

                </svg>

            </div>

            <h2>
                Data SIRS Online
            </h2>

            <p class="menu-description">
                Monitoring dan pengelolaan data pelaporan
                SIRS Online, termasuk validasi data,
                rekapitulasi dan status pengiriman laporan.
            </p>

            <div class="menu-footer">

                <span>Lihat Data</span>

                <span class="arrow">
                    →
                </span>

            </div>

        </a>

    </div>

</div>

@endsection