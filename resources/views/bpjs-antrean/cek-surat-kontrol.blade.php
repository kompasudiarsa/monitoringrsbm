@extends('layouts.app', ['title' => 'Cek Surat Kontrol SIMRS vs BPJS'])

@section('content')
<style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen biru BPJS selaras dashboard; warna status semantik dipertahankan. */

    .page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(2,132,199, .10), transparent 28%),
            #f8fafc;
        padding: 24px;
    }

    .header-page {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
    }

    .title {
        font-size: 26px;
        font-weight: 950;
        color: #0f172a;
        margin-bottom: 6px;
        letter-spacing: -.02em;
    }

    .subtitle {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .btn-back {
        background: #0f172a;
        color: #fff;
        padding: 10px 14px;
        border-radius: 14px;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-back:hover {
        color: #fff;
        opacity: .92;
        text-decoration: none;
    }

    .filter-card,
    .stat-card,
    .table-card {
        background: #fff;
        border-radius: 22px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
    }

    /* FILTER */
    .filter-card {
        padding: 18px;
        margin-bottom: 16px;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        padding-bottom: 14px;
        margin-bottom: 16px;
        border-bottom: 1px solid #eef2f7;
    }

    .filter-title {
        font-size: 17px;
        font-weight: 950;
        color: #0f172a;
        letter-spacing: -.01em;
    }

    .filter-subtitle {
        margin-top: 4px;
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        max-width: 760px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
    }

    .filter-field {
        min-width: 0;
    }

    .filter-field-wide {
        grid-column: span 2;
    }

    .filter-field label {
        display: block;
        font-size: 12px;
        font-weight: 900;
        color: #475569;
        margin-bottom: 7px;
    }

    .filter-field input,
    .filter-field select {
        width: 100%;
        height: 46px;
        border-radius: 15px;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #0f172a;
        padding: 0 13px;
        font-size: 13px;
        font-weight: 700;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .filter-field input::placeholder {
        color: #94a3b8;
        font-weight: 600;
    }

    .filter-field input:focus,
    .filter-field select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 4px rgba(2,132,199, .13);
        background: #fff;
    }

    .filter-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #eef2f7;
    }

    .btn-filter-primary,
    .btn-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 44px;
        border: 0;
        border-radius: 15px;
        padding: 0 18px;
        color: #fff;
        font-size: 13px;
        font-weight: 950;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        cursor: pointer;
        box-shadow: 0 12px 24px rgba(234, 88, 12, .22);
    }

    .btn-filter-primary:hover,
    .btn-filter:hover {
        opacity: .95;
    }

    .btn-filter-secondary,
    .btn-reset-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 44px;
        border-radius: 15px;
        padding: 0 15px;
        color: #0f172a;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        font-size: 13px;
        font-weight: 900;
        white-space: nowrap;
    }

    .btn-filter-secondary:hover,
    .btn-reset-filter:hover {
        color: #0f172a;
        background: #e2e8f0;
        text-decoration: none;
    }

    /* STATS */
    .stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .stat-card {
        padding: 15px;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: "";
        position: absolute;
        right: -28px;
        top: -28px;
        width: 80px;
        height: 80px;
        border-radius: 999px;
        background: rgba(2,132,199, .10);
    }

    .stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        position: relative;
        z-index: 1;
    }

    .stat-value {
        color: #0f172a;
        font-size: 24px;
        font-weight: 950;
        margin-top: 6px;
        position: relative;
        z-index: 1;
    }

    /* TABLE */
    .table-card {
        overflow: hidden;
    }

    .table-header {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 17px;
        font-weight: 950;
        color: #0f172a;
    }

    table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    th {
        background: #f8fafc;
        padding: 12px;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    td {
        padding: 12px;
        border-top: 1px solid #eef2f7;
        color: #334155;
        font-size: 13px;
        vertical-align: top;
        white-space: nowrap;
    }

    /* BADGE */
    .badge {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 950;
    }

    .badge-ok {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: #ffedd5;
        color: #c2410c;
    }

    .badge-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-muted {
        background: #e2e8f0;
        color: #475569;
    }

    /* ACTION BUTTON DI TABLE */
    .action-group {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    /* Jhon | 2026-11-27 11:14 WITA | TAMBAH: merah belum, biru tersimpan; status gagal baca netral. */
    .btn-mini.surat-verify[data-verified="no"]{background:#dc2626;color:#fff;border-color:#dc2626}
    .btn-mini.surat-verify[data-verified="yes"]{background:#0284c7;color:#fff;border-color:#0284c7}
    .btn-mini.surat-verify[data-verified="unknown"]{background:#64748b;color:#fff;border-color:#64748b}
    .btn-mini {
        border: 0;
        border-radius: 999px;
        padding: 7px 10px;
        font-size: 12px;
        font-weight: 900;
        color: #fff;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-dark-mini {
        background: #0f172a;
    }

    .btn-mini:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    /* MODAL */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .62);
        z-index: 9998;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .modal-backdrop-custom.show {
        display: flex;
    }

    .modal-card-custom {
        width: min(980px, 100%);
        max-height: 88vh;
        overflow: hidden;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .35);
        display: flex;
        flex-direction: column;
    }

    .modal-header-custom {
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
    }

    .modal-title-custom {
        font-size: 18px;
        font-weight: 950;
        color: #0f172a;
    }

    .modal-subtitle-custom {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .modal-close-custom {
        border: 0;
        background: #f1f5f9;
        color: #0f172a;
        width: 36px;
        height: 36px;
        border-radius: 999px;
        font-weight: 950;
        cursor: pointer;
    }

    .modal-close-custom:hover {
        background: #e2e8f0;
    }

    .modal-body-custom {
        padding: 16px 18px;
        overflow: auto;
    }

    .modal-loading {
        padding: 28px;
        text-align: center;
        color: #64748b;
        font-weight: 800;
    }

    .modal-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modal-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        padding: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .modal-table td {
        padding: 10px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        font-size: 13px;
        vertical-align: top;
    }

    .json-box {
        background: #0f172a;
        color: #e2e8f0;
        border-radius: 16px;
        padding: 14px;
        font-size: 12px;
        overflow: auto;
        max-height: 360px;
    }

    /* RESPONSIVE TABLET */
    @media (max-width: 1100px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-field-wide {
            grid-column: span 2;
        }

        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* RESPONSIVE MOBILE */
    @media (max-width: 640px) {
        .page {
            padding: 14px;
        }

        .header-page {
            display: grid;
        }

        .title {
            font-size: 23px;
        }

        .subtitle {
            font-size: 13px;
        }

        .btn-back {
            width: 100%;
            text-align: center;
            justify-content: center;
        }

        .filter-card {
            border-radius: 20px;
            padding: 14px;
        }

        .filter-header {
            display: grid;
            gap: 10px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .filter-field-wide {
            grid-column: span 1;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .btn-filter-primary,
        .btn-filter,
        .btn-filter-secondary,
        .btn-reset-filter {
            width: 100%;
        }

        .stats {
            grid-template-columns: 1fr;
        }

        .table-card {
            border-radius: 18px;
        }

        .action-group {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-mini {
            width: 100%;
        }

        .modal-backdrop-custom {
            padding: 10px;
        }

        .modal-card-custom {
            border-radius: 18px;
            max-height: 92vh;
        }

        .modal-header-custom {
            padding: 14px;
        }

        .modal-body-custom {
            padding: 14px;
        }
    }

    .pagination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        border-top: 1px solid #e2e8f0;
        background: #fff;
    }

    .pagination-info {
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .pagination-links {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 11px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        font-size: 13px;
        font-weight: 900;
        text-decoration: none;
    }

    .page-btn:hover {
        background: #f0f9ff;
        color: #0369a1;
        border-color: #bae6fd;
        text-decoration: none;
    }

    .page-btn.active {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 10px 20px rgba(2,132,199, .22);
    }

    .page-btn.disabled {
        opacity: .45;
        cursor: not-allowed;
        background: #f8fafc;
    }

    .badge-bpjs {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-non-bpjs {
        background: #f1f5f9;
        color: #475569;
    }
    .is-hidden {
    display: none !important;
}
    /* Jhon | 2026-11-23 19:25 WITA | TAMBAH: palet status pembandingan surat kontrol. */
    /* Jhon | 2026-11-23 19:33 WITA | UBAH: surat ditemukan dan sesuai memakai hijau; detail status dapat dibuka dengan keyboard/klik. */
    .kontrol-detail summary { cursor: pointer; white-space: normal; }
    .kontrol-detail .kontrol-explanation { min-width: 230px; max-width: 420px; white-space: normal; padding: 12px; background: #f8fafc; color: #0f172a; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 8px; font-size: 13px; }
    .kontrol-explanation p { margin: 6px 0; }
    /* Jhon | 2026-11-23 20:00 WITA | UBAH: status awal ada di SIMRS memakai hijau. */
    .badge.kontrol-status-ada_di_simrs { background: #dcfce7; color: #14532d; }
    .badge.kontrol-status-sesuai { background: #dcfce7; color: #14532d; }
    .badge.kontrol-status-ada_di_bpjs_belum_simrs { background: #dcfce7; color: #14532d; }
    .badge.kontrol-status-belum_ada_surat_kontrol { background: #ffedd5; color: #7c2d12; }
    .badge.kontrol-status-tidak_sesuai { background: #fee2e2; color: #7f1d1d; }
    .badge.kontrol-status-tidak_ditemukan_bpjs { background: #fce7f3; color: #831843; }
    .badge.kontrol-status-ambigu { background: #f3e8ff; color: #581c87; }
    .badge.kontrol-status-belum_terverifikasi { background: #fef9c3; color: #713f12; }
    .badge.kontrol-status-bpjs_gagal { background: #334155; color: #ffffff; }
    .badge.kontrol-status-data_tidak_lengkap { background: #e2e8f0; color: #334155; }
    /* Jhon | 2026-11-23 21:01 WITA | UBAH: kartu hero khusus halaman surat kontrol; tidak mengubah tabel/filter. */
    .page .header-page{padding:26px 28px;background:linear-gradient(120deg,#fff,#eff6ff);border:1px solid #dbeafe;border-radius:20px;align-items:center;box-shadow:0 6px 24px #0f172a06;gap:24px;margin-bottom:22px}
    .page .header-page .title{font-size:27px;font-weight:750;line-height:1.25;margin:8px 0 10px;letter-spacing:-.035em}
    .page .hero-eyebrow{font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#0369a1}
    .page .header-page .subtitle{max-width:780px;font-size:13px;line-height:1.7;color:#64748b}
    .page .connection-status{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
    .page .connection-chip{display:inline-flex;gap:6px;align-items:center;padding:6px 10px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#475569;font-size:12px;overflow-wrap:anywhere}
    .page .connection-chip.is-ok{background:#ecfdf5;border-color:#bbf7d0;color:#166534}
    .page .connection-chip.is-error{background:#fff7ed;border-color:#fed7aa;color:#9a3412}
    .page .header-page .btn-back{padding:12px 16px;border-radius:11px;background:#0f172a;font-size:13px;font-weight:600;box-shadow:0 4px 10px #0f172a12}
    .page .header-page .btn-back:focus-visible{outline:3px solid #7dd3fc;outline-offset:3px}
    @media(max-width:700px){.page .header-page{padding:20px;flex-direction:column;align-items:stretch;gap:18px}.page .header-page .title{font-size:23px}.page .header-page .btn-back{text-align:center;align-self:flex-start}.page .connection-chip{max-width:100%}}
    /* Jhon | 2026-11-23 21:11 WITA | UBAH: label status maksimal dua baris; rincian klik tidak dibatasi. */
    .page .kontrol-detail > summary { width: 180px; max-width: 180px; white-space: normal; }
    .page .kontrol-status-text { display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; overflow: hidden; line-height: 1.4; max-height: 2.8em; }
    /* Jhon | 2026-11-24 11:20 WITA | TAMBAH: pegangan drag hanya header, scroll body tetap normal. */
    #modalCekSuratKontrol .modal-header-custom { cursor: grab; touch-action: none; user-select: none; }
    #modalCekSuratKontrol .modal-header-custom.is-dragging { cursor: grabbing; }
    /* Jhon | 2026-11-24 11:45 WITA | UBAH: header ringkas desktop/mobile tanpa mengubah tabel atau filter. */
    .page { padding-top: 14px; }
    .page .header-page { padding: 14px 18px; margin-bottom: 14px; border-radius: 14px; gap: 14px; }
    .page .header-page .title { font-size: 21px; margin: 0; line-height: 1.3; }
    .page .connection-status { margin-top: 7px; gap: 6px; }
    .page .connection-chip { padding: 3px 8px; font-size: 11px; border-radius: 6px; }
    .page .header-page .btn-back { padding: 9px 12px; font-size: 12px; }
    @media(max-width:700px) {
        .page .header-page { padding: 12px 14px; flex-direction: row; align-items: center; flex-wrap: wrap; gap: 10px; }
        .page .header-page > div { flex: 1 1 240px; min-width: 0; }
        .page .header-page .title { font-size: 19px; }
        .page .header-page .btn-back { align-self: center; }
    }
    /* Jhon | 2026-11-24 11:48 WITA | UBAH: satu permukaan kartu, tanpa kartu judul bertumpuk. */
    .page .unified-filter-card { padding: 18px; }
    .page .unified-filter-card .header-page { padding: 0 0 14px; margin: 0 0 16px; border: 0; border-bottom: 1px solid #e2e8f0; border-radius: 0; background: none; box-shadow: none; }
    .page .unified-header-actions { display: flex; align-items: center; gap: 8px; flex: 0 0 auto; }
    @media(max-width:700px) {
        .page .unified-filter-card { padding: 14px; }
        .page .unified-filter-card .header-page > .unified-header-actions { flex: 0 1 auto; }
    }
    /* Jhon | 2026-11-27 11:15 WITA | TAMBAH: gaya khusus form verifikasi; modal lain dan hook drag tetap. */
    #surat-verification-modal{width:min(560px,calc(100vw - 32px));padding:0;border:1px solid #dbeafe;border-radius:20px;box-shadow:0 24px 80px #0f172a40;max-height:calc(100dvh - 32px);overflow:auto}
    #surat-verification-modal::backdrop{background:#0f172a80;backdrop-filter:blur(3px)}
    #surat-verification-modal .shared-status-header{background:linear-gradient(120deg,#eff6ff,#fff);padding:20px 24px;border-bottom:1px solid #dbeafe}
    #surat-verification-modal h2{font-size:18px;color:#0f172a;margin:0}
    #surat-verification-form{display:flex;flex-direction:column;gap:20px;padding:24px}
    #surat-verification-form .verification-reference{margin:0;padding:12px 14px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;color:#64748b;font-size:12px}
    #verification-norec{display:block;margin-top:5px;color:#334155;font-family:monospace;overflow-wrap:anywhere}
    #surat-verification-form label{display:flex;flex-direction:column;gap:8px;font-size:13px;font-weight:600;color:#334155}
    #surat-verification-form select,#surat-verification-form textarea{box-sizing:border-box;width:100%;border:1px solid #cbd5e1;border-radius:10px;background:#fff;padding:12px 14px;font:inherit;font-weight:400;color:#0f172a}
    #surat-verification-form textarea{resize:vertical;min-height:110px}
    #surat-verification-form select:focus,#surat-verification-form textarea:focus{outline:3px solid #bae6fd;border-color:#0284c7}
    #surat-verification-form .verification-footer{display:flex;justify-content:flex-end;border-top:1px solid #e2e8f0;padding-top:18px}
    #surat-verification-form button[type=submit]{padding:12px 20px;border:0;border-radius:10px;background:#0284c7;color:#fff;font-weight:700;font-size:14px;cursor:pointer}
    #surat-verification-form button:disabled{opacity:.55;cursor:wait}
    #verification-feedback{margin:0;color:#334155;font-size:13px;line-height:1.5;overflow-wrap:anywhere}
    #verification-feedback:empty{display:none}
    @media(max-width:480px){#surat-verification-form{padding:18px;gap:16px}#surat-verification-form .verification-footer button{width:100%}}
</style>

@php
$simrsCode = data_get($dataSimrs, 'metaData.code', data_get($dataSimrs, 'metadata.code', '-'));
$simrsMessage = data_get($dataSimrs, 'metaData.message', data_get($dataSimrs, 'metadata.message', '-'));

$bpjsCode = data_get($dataBpjs, 'metaData.code', data_get($dataBpjs, 'metadata.code', '-'));
$bpjsMessage = data_get($dataBpjs, 'metaData.message', data_get($dataBpjs, 'metadata.message', '-'));
@endphp

<div class="page">
    {{-- Jhon | 2026-11-24 11:48 WITA | PINDAH: satu kartu untuk judul, koneksi, dan isian filter. --}}
    <form method="GET" action="{{ route('bpjs-antrean.cek-surat-kontrol') }}" class="filter-card unified-filter-card">
    <div class="header-page">
        <div>
            {{-- Jhon | 2026-11-23 21:01 WITA | UBAH: hierarki judul, deskripsi dan chip koneksi yang terpisah. --}}
            {{-- Jhon | 2026-11-24 11:45 WITA | UBAH: ringkas judul; hapus eyebrow dan deskripsi berulang. --}}
            <h1 class="title">Cek Surat Kontrol SIMRS vs BPJS</h1>
            <div class="connection-status" aria-label="Status respons layanan">
                <span class="connection-chip {{ (string) $simrsCode === '200' ? 'is-ok' : 'is-error' }}"><b>SIMRS</b> {{ $simrsCode }} · {{ $simrsMessage }}</span>
                <span class="connection-chip {{ (string) $bpjsCode === '200' ? 'is-ok' : 'is-error' }}"><b>BPJS</b> {{ $bpjsCode }} · {{ $bpjsMessage }}</span>
            </div>
        </div>

        {{-- Jhon | 2026-11-24 11:48 WITA | PINDAH: tombol kembali dan Reset dalam header kartu gabungan. --}}
        <div class="unified-header-actions">
            <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">Kembali ke Menu</a>
            {{-- Jhon | 2026-11-24 11:49 WITA | UBAH: hapus Reset duplikat; Bersihkan Filter tetap tersedia. --}}
        </div>
    </div>

    {{-- Jhon | 2026-11-24 11:48 WITA | PINDAH: form filter kini membungkus judul di atas. --}}
        {{-- Jhon | 2026-11-24 11:48 WITA | UBAH: hapus judul filter berulang; Reset dipindahkan ke aksi header. --}}
        <div class="filter-grid">
             <div class="filter-field">
                <label>Kategori Tanggal SIMRS</label>
                <select name="kategori">
                    <option value="tglreservasi" {{ $filters['kategori'] === 'tglreservasi' ? 'selected' : '' }}>Tanggal Reservasi/ Kontrol</option>
                    <option value="tglentri" {{ $filters['kategori'] === 'tglentri' ? 'selected' : '' }}>Tanggal Input</option>
                
                </select>
            </div>
            <div class="filter-field">
                <label>Tanggal Awal</label>
                <input type="date" name="dari" value="{{ $filters['dari'] }}">
            </div>

            <div class="filter-field">
                <label>Tanggal Akhir</label>
                <input type="date" name="sampai" value="{{ $filters['sampai'] }}">
            </div>

           

            {{-- Jhon | 2026-11-23 20:29 WITA | UBAH: pilihan filter tanggal BPJS sesuai kontrak 1 entri, 2 rencana kontrol. --}}
            {{-- Jhon | 2026-11-23 20:33 WITA | UBAH: filter tanggal BPJS otomatis mengikuti kategori SIMRS. --}}

            <div class="filter-field">
                <label>Status</label>
                <select name="status">
                    <option value="ALL" {{ $filters['status'] === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                    {{-- Jhon | 2026-11-23 18:31 WITA | UBAH: status pembandingan nomor surat, poli, dokter. --}}
                    <option value="ADA_DI_SIMRS" {{ $filters['status'] === 'ADA_DI_SIMRS' ? 'selected' : '' }}>Ada Surat di SIMRS</option>
                    <option value="TIDAK_SESUAI" {{ $filters['status'] === 'TIDAK_SESUAI' ? 'selected' : '' }}>Tidak Sesuai</option>
                    <option value="TIDAK_DITEMUKAN_BPJS" {{ $filters['status'] === 'TIDAK_DITEMUKAN_BPJS' ? 'selected' : '' }}>Surat SIMRS Tidak Ditemukan di BPJS</option>
                    <option value="AMBIGU" {{ $filters['status'] === 'AMBIGU' ? 'selected' : '' }}>Beberapa Kandidat Surat</option>
                    <option value="BELUM_TERVERIFIKASI" {{ $filters['status'] === 'BELUM_TERVERIFIKASI' ? 'selected' : '' }}>Belum Terverifikasi</option>
                    <option value="BPJS_GAGAL" {{ $filters['status'] === 'BPJS_GAGAL' ? 'selected' : '' }}>Pengecekan BPJS Gagal</option>
                    <option value="ADA_DI_BPJS_BELUM_SIMRS" {{ $filters['status'] === 'ADA_DI_BPJS_BELUM_SIMRS' ? 'selected' : '' }}>Ada di BPJS, belum SIMRS</option>
                    <option value="BELUM_ADA_SURAT_KONTROL" {{ $filters['status'] === 'BELUM_ADA_SURAT_KONTROL' ? 'selected' : '' }}>Belum Ada Surat Kontrol</option>
                    {{-- Jhon | 2026-11-27 11:35 WITA | TAMBAH: pisahkan surat kosong berdasarkan verifikasi petugas tersimpan. --}}
                    <option value="BELUM_SURAT_SUDAH_VERIFIKASI" {{ $filters['status'] === 'BELUM_SURAT_SUDAH_VERIFIKASI' ? 'selected' : '' }}>Belum Ada Surat Kontrol — Sudah Diverifikasi</option>
                    <option value="BELUM_SURAT_BELUM_VERIFIKASI" {{ $filters['status'] === 'BELUM_SURAT_BELUM_VERIFIKASI' ? 'selected' : '' }}>Belum Ada Surat Kontrol — Belum Diverifikasi</option>
                    <option value="DATA_TIDAK_LENGKAP" {{ $filters['status'] === 'DATA_TIDAK_LENGKAP' ? 'selected' : '' }}>Data Tidak Lengkap</option>
                </select>
            </div>

            <div class="filter-field filter-field-wide">
                <label>Keyword</label>
                <input type="text" name="keyword" value="{{ $filters['keyword'] }}" placeholder="Nama / RM / BPJS / reservasi / surat">
            </div>

            <div class="filter-field">
                <label>Poli</label>
                <input type="text" name="poli" value="{{ $filters['poli'] }}" placeholder="Contoh: PARU">
            </div>

            <div class="filter-field">
                <label>Dokter</label>
                <input type="text" name="dokter" value="{{ $filters['dokter'] }}" placeholder="Nama dokter">
            </div>

            {{-- Jhon | 2026-11-23 20:34 WITA | UBAH: hapus filter Jenis / Tipe Pasien. --}}
            {{-- Jhon | 2026-11-23 18:17 WITA | UBAH: kelompok pasien dikunci BPJS di backend. --}}
            {{-- Jhon | 2026-11-23 20:34 WITA | UBAH: hapus filter Mobile JKN. --}}
{{-- Jhon | 2026-11-23 18:22 WITA | UBAH: hapus pilihan jumlah data; muat bertahap saat scroll. --}}
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter-primary">
                Terapkan Filter
            </button>

            <a href="{{ route('bpjs-antrean.cek-surat-kontrol') }}" class="btn-filter-secondary">
                Bersihkan Filter
            </a>
        </div>
    </form>

    {{-- Jhon | 2026-11-23 18:17 WITA | UBAH: judul ringkasan menjelaskan jumlah reservasi dan status surat kontrol. --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Total Reservasi BPJS</div>
            <div class="stat-value">{{ $summary['total_simrs'] }}</div>
        </div>

        <div class="stat-card">
            {{-- Jhon | 2026-11-23 18:31 WITA | UBAH: status pembandingan nomor surat, poli, dokter. --}}
            <div class="stat-label">Surat Kontrol di SIMRS</div>
            <div class="stat-value">{{ $summary['ada_di_simrs'] }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Surat Kontrol di BPJS, Belum di SIMRS</div>
            <div class="stat-value">{{ $summary['ada_di_bpjs_belum_simrs'] }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Belum Ada Surat Kontrol</div>
            <div class="stat-value">{{ $summary['belum_ada_surat_kontrol'] }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Data Tidak Lengkap</div>
            <div class="stat-value">{{ $summary['data_tidak_lengkap'] }}</div>
        </div>
    </div>

    {{-- Jhon | 2026-11-23 18:31 WITA | UBAH: status pembandingan nomor surat, poli, dokter. --}}
    {{-- Jhon | 2026-11-23 20:39 WITA | UBAH: hapus dua kartu ringkasan tambahan sesuai gambar pengguna. --}}
    <div class="table-card">
        <div class="table-header">
            {{-- Jhon | 2026-11-23 18:17 WITA | UBAH: judul daftar khusus reservasi BPJS. --}}
            <div class="table-title">
                Hasil Pembandingan Surat Kontrol Reservasi BPJS
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Aksi</th>
                        {{-- Jhon | 2026-11-23 21:05 WITA | UBAH: urutan Status, SEP SIMRS, lalu surat kontrol SIMRS. --}}
                        <th>Status</th>
                        {{-- Jhon | 2026-11-23 21:06 WITA | UBAH: judul kolom SEP asal SIMRS. --}}
                        <th>No SEP ASAL</th>
                        {{-- Jhon | 2026-11-27 10:50 WITA | TAMBAH: rujukan bersumber dari lookup SEP, tidak memuat nomor dugaan. --}}
                        <th>No Rujukan</th>
                        <th>No Surat Kontrol SIMRS</th>
                        {{-- Jhon | 2026-11-27 10:40 WITA | PINDAH: tampilkan poli SIMRS di samping nomor surat, tanpa duplikasi kolom. --}}
                        <th>Poli</th>
                        <th>No RM</th>
                        <th>Nama Pasien</th>
                        {{-- Jhon | 2026-11-23 21:08 WITA | UBAH: hapus kolom kelompok pasien; filter BPJS backend tetap. --}}
                        <th>No BPJS</th>
                        <th>No Reservasi</th>
                        {{-- Jhon | 2026-11-27 10:25 WITA | TAMBAH: kolom tanggal input sesudah reservasi, termasuk hasil infinite scroll. --}}
                        <th>Tgl Reservasi</th>
                        <th>Tanggal Input</th>
                        {{-- Jhon | 2026-11-27 10:40 WITA | PINDAH: kolom Poli kini tepat setelah nomor surat kontrol SIMRS. --}}
                        <th>Dokter</th>
                        <th>No Antrean</th>
                        {{-- Jhon | 2026-11-23 21:09 WITA | UBAH: hapus dua kolom BPJS paling belakang sesuai gambar; SEP asal SIMRS tetap. --}}
                        {{-- Jhon | 2026-11-23 21:05 WITA | PINDAH: kolom surat SIMRS kini setelah SEP SIMRS. --}}



                    </tr>
                </thead>
                {{-- Jhon | 2026-11-23 18:22 WITA | UBAH: penanda daftar dan URL halaman berikutnya. --}}
                <tbody id="kontrol-rows" data-next-url="{{ $hasil->nextPageUrl() }}">
                    @forelse ($hasil as $index => $row)
                    {{-- Jhon | 2026-11-23 18:22 WITA | UBAH: hash stabil mencegah baris ganda saat append. --}}
                    <tr data-row-key="{{ hash('sha256', json_encode($row)) }}">
                        <td>{{ ($hasil->firstItem() ?? 1) + $index }}</td>
                        <td>
                            <div class="action-group">
                                {{-- Jhon | 2026-11-27 11:12 WITA | PINDAH: tombol Verifikasi dipindahkan ke sel Status. --}}
                                <button
                                    type="button"
                                    class="btn-mini btn-cek-nokartu"
                                    data-nokartu="{{ $row['nobpjs'] ?? '' }}"
                                    data-tanggal="{{ $row['tanggal_pembanding'] ?? now()->format('Y-m-d') }}"
                                    data-nama="{{ $row['namapasien'] ?? '-' }}">
                                    Cek No Kartu
                                </button>

                                @php
                                $noSuratUntukCek = $row['nosurat_simrs'] ?: $row['nosurat_bpjs'];
                                @endphp

                                <button
                                    type="button"
                                    class="btn-mini btn-dark-mini btn-cek-nosurat"
                                    data-nosurat="{{ $noSuratUntukCek ?? '' }}"
                                    data-nama="{{ $row['namapasien'] ?? '-' }}"
                                    {{ empty($noSuratUntukCek) ? 'disabled' : '' }}>
                                    Cek No Surat
                                </button>
                            </div>
                        </td>
                        <td>
                            {{-- Jhon | 2026-11-23 18:31 WITA | UBAH: status pembandingan nomor surat, poli, dokter. --}}
                            {{-- Jhon | 2026-11-23 19:25 WITA | UBAH: satu warna per status, label tetap terbaca tanpa bergantung warna. --}}
                            {{-- Jhon | 2026-11-23 19:33 WITA | UBAH: klik status membuka bukti pembandingan; tetap berfungsi pada baris infinite scroll. --}}
                            {{-- Jhon | 2026-11-27 11:12 WITA | PINDAH: verifikasi pada Status, di luar rincian agar tidak tersembunyi/ikut dipindah ke modal status. --}}
                                {{-- Jhon | 2026-11-27 11:06 WITA | TAMBAH: verifikasi reservasi terpilih, bukan UUID contoh yang di-hardcode. --}}
                                {{-- Jhon | 2026-11-27 11:09 WITA | UBAH: sembunyikan verifikasi jika surat sudah ada atau pemeriksaan BPJS belum valid. --}}
                                @if(!empty($row['verification_token']))
                                <button type="button" class="btn-mini surat-verify" data-verified="{{ ($row['verification_saved'] ?? null) === null ? 'unknown' : ($row['verification_saved'] ? 'yes' : 'no') }}" data-problem="{{ $row['verification_problem'] ?? '' }}" data-note="{{ $row['verification_note'] ?? '' }}" data-token="{{ $row['verification_token'] ?? '' }}" data-norec="{{ $row['verification_norec'] ?? '' }}" @if(empty($row['verification_token'])) disabled @endif>{{ ($row['verification_saved'] ?? null) === null ? 'Status verifikasi belum terbaca' : ($row['verification_saved'] ? 'Sudah diverifikasi' : 'Belum diverifikasi') }}</button>
                                @endif
                            <details class="kontrol-detail">
                                <summary class="badge kontrol-status-{{ strtolower($row['status']) }}" title="{{ $row['status_label'] }} — Klik untuk melihat rincian"><span class="kontrol-status-text">{{ $row['status_label'] }} ⓘ</span></summary>
                                <div class="kontrol-explanation">
                                    {{-- Jhon | 2026-11-23 20:22 WITA | UBAH: tampilkan alasan spesifik hasil pencocokan, bukan informasi umum pasien. --}}
                                    <b>Hasil pemeriksaan data</b>
                                    {{-- Jhon | 2026-11-24 14:15 WITA | TAMBAH: log Data/Response memakai SEP asal; tidak mengganti alasan pembandingan. --}}
                                    @if ($row['status'] === 'BELUM_ADA_SURAT_KONTROL')
                                        @if (!empty($row['sep_simrs']))
                                        <button type="button" class="btn-mini surat-log-check" data-sep="{{ $row['sep_simrs'] }}">Cek Log Bridging</button>
                                        <div class="surat-log-result" role="status" aria-live="polite"></div>
                                        @else
                                        <p>Log bridging belum dapat dicek karena SEP asal SIMRS kosong.</p>
                                        @endif
                                    @endif
                                    @foreach ($row['alasan_pembandingan'] as $alasan)
                                    <p>{{ $alasan }}</p>
                                    @endforeach
                                    <p>Tanggal SIMRS memakai {{ !empty($row['tanggal_pembanding']) ? $row['tanggal_pembanding'] : '(kosong/tidak valid)' }}; sumbernya tanggal kontrol, atau tanggal reservasi jika tanggal kontrol kosong.</p>
                                    <p>Yang dibandingkan hanya nomor kartu dan tanggal. Poli, SEP, dokter, dan kesamaan nomor surat tidak diperiksa.</p>

                                </div>
                            </details>
                        </td>

                        {{-- Jhon | 2026-11-23 21:05 WITA | UBAH: SEP memakai sumber SIMRS; kosong ditampilkan tanda hubung. --}}
                        {{-- Jhon | 2026-11-27 10:33 WITA | UBAH: nomor SEP membuka detail baca-saja, termasuk baris infinite scroll. --}}
                        {{-- Jhon | 2026-11-27 10:50 WITA | UBAH: SEP dan rujukan memakai popup draggable bersama. --}}
                        <td>@if(!empty($row['sep_simrs']))<button type="button" class="surat-sep-check" data-sep="{{ $row['sep_simrs'] }}">{{ $row['sep_simrs'] }}</button>@else - @endif</td>
                        <td>@if(!empty($row['sep_simrs']))<button type="button" class="surat-sep-check" data-kind="rujukan" data-sep="{{ $row['sep_simrs'] }}">Lihat No Rujukan</button>@else - @endif</td>
                        <td>{{ $row['nosurat_simrs'] ?: '-' }}</td>
                        {{-- Jhon | 2026-11-27 10:40 WITA | PINDAH: tetap gunakan field poli SIMRS yang sudah tersedia. --}}
                        <td>{{ $row['poli'] ?? '-' }}</td>
                        <td>{{ $row['nocm'] ?? '-' }}</td>
                        <td><b>{{ $row['namapasien'] ?? '-' }}</b></td>
                        {{-- Jhon | 2026-11-23 21:08 WITA | UBAH: hapus isi kolom kelompok pasien. --}}
                        <td>{{ $row['nobpjs'] ?? '-' }}</td>
                        <td>{{ $row['noreservasi'] ?? '-' }}</td>
                        {{-- Jhon | 2026-11-27 10:25 WITA | UBAH: format reservasi tanggal saja dan input lengkap dengan jam. --}}
                        <td>{{ $row['tanggalreservasi_tampilan'] ?? '-' }}</td>
                        <td>{{ $row['tanggalinput_tampilan'] ?? '-' }}</td>
                        {{-- Jhon | 2026-11-27 10:40 WITA | PINDAH: isi Poli dipindah ke setelah nomor surat. --}}
                        <td>{{ $row['dokter'] ?? '-' }}</td>
                        <td>{{ $row['noantrianpoli'] ?? '-' }}</td>
                        {{-- Jhon | 2026-11-23 21:09 WITA | UBAH: hapus sel surat dan SEP asal BPJS paling belakang. --}}
                        {{-- Jhon | 2026-11-23 21:05 WITA | PINDAH: isi surat SIMRS dipindahkan sesudah status dan SEP. --}}



                    </tr>
                    @empty
                    <tr>
                       {{-- Jhon | 2026-11-27 10:25 WITA | UBAH: sesuaikan bentang baris kosong dengan tambahan kolom input. --}}
                       <td colspan="15" style="text-align:center; padding:30px; color:#64748b;">
                            Tidak ada data reservasi SIMRS pada tanggal {{ $filters['dari'] }} s/d {{ $filters['sampai'] }}.
                            </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Jhon | 2026-11-23 18:22 WITA | UBAH: status pemuatan dan retry; pagination tetap fallback tanpa JavaScript. --}}
            <div id="kontrol-scroll-sentinel" style="padding:16px;text-align:center">
                <span id="kontrol-scroll-status" role="status" aria-live="polite"></span>
                <button id="kontrol-scroll-retry" type="button" hidden>Coba lagi</button>
            </div>
            <div class="pagination-footer" id="kontrol-pagination">
                <div class="pagination-info">
                    Menampilkan
                    <b>{{ $hasil->firstItem() ?? 0 }}</b>
                    -
                    <b>{{ $hasil->lastItem() ?? 0 }}</b>
                    dari
                    <b>{{ $hasil->total() }}</b>
                    data
                </div>

                @if ($hasil->hasPages())
                <div class="pagination-links">
                    @if ($hasil->onFirstPage())
                    <span class="page-btn disabled">Sebelumnya</span>
                    @else
                    <a class="page-btn" href="{{ $hasil->previousPageUrl() }}">Sebelumnya</a>
                    @endif

                    @php
                    $start = max($hasil->currentPage() - 2, 1);
                    $end = min($hasil->currentPage() + 2, $hasil->lastPage());
                    @endphp

                    @if ($start > 1)
                    <a class="page-btn" href="{{ $hasil->url(1) }}">1</a>
                    @if ($start > 2)
                    <span class="page-btn disabled">...</span>
                    @endif
                    @endif

                    @foreach ($hasil->getUrlRange($start, $end) as $page => $url)
                    @if ($page == $hasil->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                    @else
                    <a class="page-btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                    @endforeach

                    @if ($end < $hasil->lastPage())
                        @if ($end < $hasil->lastPage() - 1)
                            <span class="page-btn disabled">...</span>
                            @endif
                            <a class="page-btn" href="{{ $hasil->url($hasil->lastPage()) }}">
                                {{ $hasil->lastPage() }}
                            </a>
                            @endif

                            @if ($hasil->hasMorePages())
                            <a class="page-btn" href="{{ $hasil->nextPageUrl() }}">Berikutnya</a>
                            @else
                            <span class="page-btn disabled">Berikutnya</span>
                            @endif
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
<div id="modalCekSuratKontrol" class="modal-backdrop-custom">
    <div class="modal-card-custom">
        <div class="modal-header-custom">
            <div>
                <div id="modalCekTitle" class="modal-title-custom">
                    Cek Surat Kontrol
                </div>
                <div id="modalCekSubtitle" class="modal-subtitle-custom">
                    -
                </div>
            </div>

            <button type="button" class="modal-close-custom" onclick="closeCekModal()">
                ×
            </button>
        </div>

        <div id="modalCekBody" class="modal-body-custom">
            <div class="modal-loading">Memuat data...</div>
        </div>
    </div>
</div>
{{-- Jhon | 2026-11-24 14:20 WITA | TAMBAH: modal status surat kontrol; isi dipindah dari details saat klik. --}}
<dialog id="surat-status-modal" data-draggable-modal class="shared-status-modal">
    <div class="shared-status-header" data-drag-handle><h2>Rincian Status Surat Kontrol</h2><button type="button" data-modal-close aria-label="Tutup">×</button></div>
    <div id="surat-status-body" class="shared-status-body"></div>
</dialog>

{{-- Jhon | 2026-11-27 10:50 WITA | TAMBAH: modal memakai handler drag dan tutup dari layout bersama. --}}
{{-- Jhon | 2026-11-27 11:06 WITA | TAMBAH: form verifikasi popup dapat digeser, tidak memuat identitas pasien tambahan. --}}
<dialog id="surat-verification-modal" data-draggable-modal class="shared-status-modal">
<div class="shared-status-header" data-drag-handle><h2>Verifikasi Surat Kontrol</h2><button type="button" data-modal-close aria-label="Tutup">×</button></div>
<form id="surat-verification-form" class="shared-status-body">
{{-- Jhon | 2026-11-27 11:15 WITA | UBAH: referensi ringkas dan form vertikal, ID field tetap untuk handler existing. --}}
<p class="verification-reference">REFERENSI RESERVASI<span id="verification-norec"></span></p>
<input type="hidden" name="reservation_token">
{{-- Jhon | 2026-11-27 11:21 WITA | TAMBAH: kategori tambahan memakai nilai yang diterima validator server. --}}
<label>Permasalahan <select name="permasalahan" required><option value="">Pilih permasalahan</option value="rujukan">Rujukan</option><option value="sep">SEP</option><option value="jadwal_poli">Jadwal Poli</option><option value="lainnya">Lainnya</option><option value="kontrol_internal">Kontrol Internal</option></select></label>
<label>Catatan / Keterangan <textarea name="keterangan" maxlength="2000" rows="4" placeholder="Tuliskan hasil pemeriksaan atau kendala yang ditemukan…"></textarea></label>
<div class="verification-footer"><button type="submit" class="btn-mini">Simpan Verifikasi</button></div><p id="verification-feedback" role="status" aria-live="polite"></p>
</form></dialog>
<script>
// Jhon | 2026-11-27 11:06 WITA | TAMBAH: delegasi mendukung infinite scroll; cegah submit ganda dan pertahankan catatan ketika simpan gagal.
document.addEventListener('click', function(event) {
    const button=event.target.closest('.surat-verify');
    if (!button || button.disabled) return;
    const form=document.getElementById('surat-verification-form');
    if (form.dataset.busy) return;
    // Jhon | 2026-11-27 11:14 WITA | TAMBAH: isi ulang verifikasi tersimpan saat dibuka.
    form.reset(); form.elements.reservation_token.value=button.dataset.token;
    form.elements.permasalahan.value=button.dataset.problem || '';
    form.elements.keterangan.value=button.dataset.note || '';
    document.getElementById('verification-norec').textContent=button.dataset.norec;
    document.getElementById('verification-feedback').textContent='';
    document.getElementById('surat-verification-modal').showModal();
});
document.getElementById('surat-verification-form').addEventListener('submit', async function(event) {
    event.preventDefault(); if (this.dataset.busy || !this.reportValidity()) return;
    const csrf=document.querySelector('#monitoring-logout [name="_token"]');
    const feedback=document.getElementById('verification-feedback');
    if (!csrf) { feedback.textContent='Silakan login kembali.'; return; }
    this.dataset.busy='1'; const button=this.querySelector('button[type="submit"]'); button.disabled=true;
    feedback.textContent='Menyimpan verifikasi…';
    try {
        const response=await fetch("{{ route('bpjs-antrean.surat-kontrol-verifikasi') }}", {method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf.value},body:JSON.stringify(Object.fromEntries(new FormData(this)))});
        if (response.status===401 || response.status===419) throw new Error('Sesi berakhir. Silakan login kembali.');
        const json=await response.json(); if (!response.ok) throw new Error(json.message || 'Verifikasi belum tersimpan.');
        // Jhon | 2026-11-27 11:14 WITA | TAMBAH: jangan ubah warna saat gagal; perbarui seluruh tombol reservasi sama setelah server memastikan simpan.
        if (json.verified === true) document.querySelectorAll('.surat-verify').forEach(item => {
            if (item.dataset.norec !== json.norec) return;
            item.dataset.verified='yes'; item.textContent='Sudah diverifikasi';
            item.dataset.problem=this.elements.permasalahan.value; item.dataset.note=this.elements.keterangan.value;
        });
        feedback.textContent=json.message;
        // Jhon | 2026-11-27 11:33 WITA | UBAH: tutup hanya setelah server memastikan verifikasi tersimpan, bukan saat request dimulai.
        if (json.verified === true) document.getElementById('surat-verification-modal').close();
    } catch(error) { feedback.textContent=error.message; }
    finally { delete this.dataset.busy; button.disabled=false; }
});
</script>
<dialog id="surat-document-modal" data-draggable-modal class="shared-status-modal">
<div class="shared-status-header" data-drag-handle><h2 id="surat-document-title">Informasi Dokumen</h2><button type="button" data-modal-close aria-label="Tutup">×</button></div>
<div id="surat-document-body" class="shared-status-body" role="status"></div>
</dialog>
<script>
// Jhon | 2026-11-27 10:33 WITA | TAMBAH: delegasi lookup SEP, cegah request ganda dan render informasi sebagai teks.
document.addEventListener('click', async function(event) {
    const button = event.target.closest('.surat-sep-check');
    if (!button || button.disabled) return;
    // Jhon | 2026-11-27 10:50 WITA | UBAH: satu modal, hasil request lama tidak menimpa dokumen yang baru dipilih.
    const modal = document.getElementById('surat-document-modal');
    const target = document.getElementById('surat-document-body');
    const kind = button.dataset.kind === 'rujukan' ? 'rujukan' : 'sep';
    if (!modal || modal.dataset.busy) return;
    document.getElementById('surat-document-title').textContent = kind === 'rujukan' ? 'Informasi Rujukan' : 'Informasi SEP';
    if (!modal.open) modal.showModal();
    modal.dataset.busy = '1';
    const csrf = document.querySelector('#monitoring-logout [name="_token"]');
    if (!target || !csrf) return;
    button.disabled = true; target.textContent = 'Memuat informasi dokumen…';
    try {
        const response = await fetch(kind === 'rujukan' ? "{{ route('bpjs-antrean.surat-kontrol-rujukan') }}" : "{{ route('bpjs-antrean.surat-kontrol-sep') }}", {method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf.value}, body:JSON.stringify({noSEP:button.dataset.sep})});
        if (response.status === 401 || response.status === 419) throw new Error('Sesi berakhir. Silakan login kembali.');
        const json = await response.json();
        if (kind === 'rujukan' && json.number) button.textContent = json.number;
        if (!response.ok) throw new Error(json.message || 'Informasi dokumen gagal diambil.');
        if (!json.data || typeof json.data !== 'object' || Array.isArray(json.data)) throw new Error('Format informasi SEP tidak sesuai.');
        target.textContent = '';
        Object.entries(json.data).forEach(([label,value]) => { const line = document.createElement('p'); line.textContent = label + ': ' + (value || '-'); target.appendChild(line); });
    } catch(error) { target.textContent = error.message; }
    finally { button.disabled = false; delete modal.dataset.busy; }
});
</script>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalCek = document.getElementById('modalCekSuratKontrol');
        const modalCekTitle = document.getElementById('modalCekTitle');
        const modalCekSubtitle = document.getElementById('modalCekSubtitle');
        const modalCekBody = document.getElementById('modalCekBody');
        const btnCloseCekModal = document.getElementById('btnCloseCekModal');

        if (!modalCek || !modalCekTitle || !modalCekSubtitle || !modalCekBody) {
            console.error('Elemen modal cek surat kontrol belum ditemukan. Pastikan HTML modal ada sebelum script.');
            return;
        }

        // Jhon | 2026-11-24 11:20 WITA | TAMBAH: pointer drag mouse/sentuh, batas viewport, reset saat dibuka ulang.
        const dragCard = modalCek.querySelector('.modal-card-custom');
        const dragHeader = modalCek.querySelector('.modal-header-custom');
        let drag = null;
        let offsetX = 0, offsetY = 0;
        function resetModalPosition() {
            drag = null; offsetX = 0; offsetY = 0;
            dragCard.style.removeProperty('transform');
            dragHeader.classList.remove('is-dragging');
        }
        dragHeader.addEventListener('pointerdown', function (event) {
            if (event.button !== 0 || event.target.closest('button, a, input, select, textarea')) return;
            const rect = dragCard.getBoundingClientRect();
            drag = {id: event.pointerId, x: event.clientX, y: event.clientY, rect, offsetX, offsetY};
            dragHeader.setPointerCapture(event.pointerId);
            dragHeader.classList.add('is-dragging');
            event.preventDefault();
        });
        dragHeader.addEventListener('pointermove', function (event) {
            if (!drag || event.pointerId !== drag.id) return;
            const dx = Math.max(8 - drag.rect.left, Math.min(event.clientX - drag.x, window.innerWidth - 8 - drag.rect.right));
            const dy = Math.max(8 - drag.rect.top, Math.min(event.clientY - drag.y, window.innerHeight - 8 - drag.rect.bottom));
            offsetX = drag.offsetX + dx; offsetY = drag.offsetY + dy;
            dragCard.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
        });
        function stopDrag(event) {
            if (!drag || drag.id !== event.pointerId) return;
            drag = null;
            dragHeader.classList.remove('is-dragging');
            if (dragHeader.hasPointerCapture(event.pointerId)) dragHeader.releasePointerCapture(event.pointerId);
        }
        ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(name => dragHeader.addEventListener(name, stopDrag));
        window.addEventListener('resize', resetModalPosition);

        function openCekModal(title, subtitle) {
            resetModalPosition();
            modalCekTitle.textContent = title;
            modalCekSubtitle.textContent = subtitle;
            modalCekBody.innerHTML = '<div class="modal-loading">Memuat data...</div>';
            modalCek.classList.add('show');
        }

        function closeCekModal() {
            modalCek.classList.remove('show');
        }

        window.closeCekModal = closeCekModal;
        if (btnCloseCekModal) {
            btnCloseCekModal.addEventListener('click', closeCekModal);
        }

        modalCek.addEventListener('click', function(event) {
            if (event.target === modalCek) {
                closeCekModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCekModal();
            }
        });

        // Jhon | 2026-11-23 18:22 WITA | UBAH: delegasi klik agar baris hasil scroll tetap dapat dicek.
        document.addEventListener('click', function(event) {
                const button = event.target.closest('.btn-cek-nokartu');
                if (!button) return;
                const noKartu = button.dataset.nokartu || '';
                const tanggal = button.dataset.tanggal || '{{ now()->format('Y-m-d')}}';
                const nama = button.dataset.nama || '-';

                if (!noKartu) {
                    openCekModal('Cek No Kartu', nama);
                    modalCekBody.innerHTML = '<div class="modal-loading">No kartu BPJS kosong.</div>';
                    return;
                }

                openCekModal(
                    'Cek Surat Kontrol Berdasarkan No Kartu',
                    nama + ' | No Kartu: ' + noKartu + ' | Tanggal: ' + tanggal
                );

                const url = new URL("{{ route('bpjs-antrean.cek-surat-kontrol.ajax.no-kartu') }}");
                url.searchParams.set('no_kartu', noKartu);
                url.searchParams.set('tanggal', tanggal);
                url.searchParams.set('filter', '2');

                fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(json => renderCekResult(json))
                    .catch(error => {
                        modalCekBody.innerHTML = '<div class="modal-loading">Gagal mengambil data: ' + escapeHtml(error.message) + '</div>';
                    });
        });

        // Jhon | 2026-11-23 18:22 WITA | UBAH: delegasi klik agar baris hasil scroll tetap dapat dicek.
        document.addEventListener('click', function(event) {
                const button = event.target.closest('.btn-cek-nosurat');
                if (!button) return;
                const noSurat = button.dataset.nosurat || '';
                const nama = button.dataset.nama || '-';

                if (!noSurat) {
                    openCekModal('Cek No Surat', nama);
                    modalCekBody.innerHTML = '<div class="modal-loading">No surat kontrol kosong.</div>';
                    return;
                }

                openCekModal(
                    'Cek Surat Kontrol Berdasarkan No Surat',
                    nama + ' | No Surat: ' + noSurat
                );

                const url = new URL("{{ route('bpjs-antrean.cek-surat-kontrol.ajax.no-surat') }}");
                url.searchParams.set('no_surat', noSurat);

                fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(json => renderCekResult(json))
                    .catch(error => {
                        modalCekBody.innerHTML = '<div class="modal-loading">Gagal mengambil data: ' + escapeHtml(error.message) + '</div>';
                    });
        });

        function renderCekResult(json) {
            if (!json.success) {
                modalCekBody.innerHTML = '<div class="modal-loading">' + escapeHtml(json.message || 'Gagal mengambil data.') + '</div>';
                return;
            }

            const result = json.result || {};
            const metadataCode = getValue(result, ['metaData.code', 'metadata.code'], '-');
            const metadataMessage = getValue(result, ['metaData.message', 'metadata.message'], '-');

            const response = result.response || {};
            let rows = [];

            if (Array.isArray(response)) {
                rows = response;
            } else if (Array.isArray(response.list)) {
                rows = response.list;
            } else if (response.noSuratKontrol || response.noSurat || response.noKartu) {
                rows = [response];
            } else if (result.response && typeof result.response === 'object') {
                rows = [result.response];
            }

            let html = `
            <div style="margin-bottom:14px;">
                <span class="badge badge-muted">Metadata: ${escapeHtml(metadataCode)}</span>
                <span style="font-size:13px; color:#64748b; margin-left:8px;">
                    ${escapeHtml(metadataMessage)}
                </span>
            </div>
        `;

            if (rows.length > 0) {
                html += `
                <div style="overflow-x:auto;">
                    <table class="modal-table">
                        <thead>
                            <tr>
                                <th>No Surat</th>
                                <th>No Kartu</th>
                                <th>Nama</th>
                                <th>Tgl Kontrol</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>No SEP Asal</th>
                                <th>Terbit SEP</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                rows.forEach(function(item) {
                    html += `
                    <tr>
                        <td><b>${escapeHtml(item.noSuratKontrol || item.noSurat || item.no_surat_kontrol || '-')}</b></td>
                        <td>${escapeHtml(item.noKartu || item.nokartu || '-')}</td>
                        <td>${escapeHtml(item.nama || item.namaPeserta || item.namapeserta || '-')}</td>
                        <td>${escapeHtml(item.tglRencanaKontrol || item.tgl_rencana_kontrol || item.tglKontrol || '-')}</td>
                        <td>${escapeHtml(item.namaPoliTujuan || item.poliTujuan || item.namaPoli || item.poli || '-')}</td>
                        <td>${escapeHtml(item.namaDokter || item.dokter || '-')}</td>
                        <td>${escapeHtml(item.noSepAsalKontrol || item.noSEP || item.noSep || '-')}</td>
                        <td>${escapeHtml(item.terbitSEP || item.terbitSep || '-')}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>
                </div>
            `;
            } else {
                html += `
                <div class="modal-loading">
                    Data surat kontrol tidak ditemukan.
                </div>
            `;
            }

            html += `
            <div style="margin-top:16px;">
                <div style="font-weight:950; color:#0f172a; margin-bottom:8px;">
                    Raw Response
                </div>
                <pre class="json-box">${escapeHtml(JSON.stringify(result, null, 2))}</pre>
            </div>
        `;

            modalCekBody.innerHTML = html;
        }

        function getValue(obj, paths, fallback) {
            for (const path of paths) {
                const value = path.split('.').reduce((carry, key) => {
                    if (carry && Object.prototype.hasOwnProperty.call(carry, key)) {
                        return carry[key];
                    }

                    return undefined;
                }, obj);

                if (value !== undefined && value !== null && value !== '') {
                    return value;
                }
            }

            return fallback;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    });
</script>
<script>

// Jhon | 2026-11-23 18:22 WITA | TAMBAH: infinite scroll, deduplikasi, penghentian akhir, dan retry terkontrol.
document.addEventListener('DOMContentLoaded', function () {
    const rows = document.getElementById('kontrol-rows');
    const sentinel = document.getElementById('kontrol-scroll-sentinel');
    const status = document.getElementById('kontrol-scroll-status');
    const retry = document.getElementById('kontrol-scroll-retry');
    if (!rows || !sentinel || !('IntersectionObserver' in window)) return;
    document.getElementById('kontrol-pagination').hidden = true;
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
            const incoming = doc.getElementById('kontrol-rows');
            if (!incoming) throw new Error('Respons tidak sesuai');
            const following = incoming.dataset.nextUrl || '';
            if (following === requested) throw new Error('Pagination tidak maju');
            incoming.querySelectorAll('[data-row-key]').forEach(row => {
                if (!seen.has(row.dataset.rowKey)) {
                    seen.add(row.dataset.rowKey);
                    rows.appendChild(document.importNode(row, true));
                }
            });
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

<script>

// Jhon | 2026-11-24 14:15 WITA | TAMBAH: delegasi klik agar log tetap bekerja setelah infinite scroll; render aman tanpa HTML dari API.
document.addEventListener('click', async function (event) {
    const button = event.target.closest('.surat-log-check');
    if (!button || button.disabled) return;
    const target = button.parentElement.querySelector('.surat-log-result');
    const csrf = document.querySelector('#monitoring-logout [name="_token"]');
    if (!target || !csrf) return;
    button.disabled = true; target.textContent = 'Memuat log bridging...';
    try {
        const response = await fetch("{{ route('bpjs-antrean.surat-kontrol-log') }}", {
            method: 'POST', headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf.value},
            body: JSON.stringify({noSEP: button.dataset.sep})
        });
        if (response.status === 401 || response.status === 419) throw new Error('Sesi berakhir. Silakan login kembali.');
        const json = await response.json();
        if (!response.ok) throw new Error(json.message || 'Gagal mengambil log surat kontrol.');
        target.textContent = '';
        if (!Array.isArray(json.data) || !json.data.length) { target.textContent = 'Tidak ada log surat kontrol sesuai SEP asal ini pada hasil pencarian API.'; return; }
        json.data.forEach((item, index) => {
            const section = document.createElement('section');
            const title = document.createElement('b'); title.textContent = 'Log ' + (index + 1); section.appendChild(title);
            // Jhon | 2026-11-27 07:31 WITA | TAMBAH: nama dari log ditampilkan sebagai teks aman, termasuk saat baris hasil append.
            const inputUser = document.createElement('p');
            inputUser.textContent = 'User penginput: ' + (typeof item.user === 'string' && item.user.trim() ? item.user : 'Tidak tercatat');
            section.appendChild(inputUser);
            ['data', 'response'].forEach(key => {
                const label = document.createElement('p'); label.textContent = key === 'data' ? 'Data' : 'Response';
                const pre = document.createElement('pre'); pre.textContent = JSON.stringify(item[key], null, 2);
                pre.style.cssText = 'white-space:pre-wrap;overflow-wrap:anywhere;max-height:260px;overflow:auto;background:#0f172a;color:#e2e8f0;padding:10px;border-radius:8px';
                section.append(label, pre);
            }); target.appendChild(section);
        });
        if (json.truncated) { const note = document.createElement('p'); note.textContent = 'Menampilkan maksimal 20 log.'; target.appendChild(note); }
    } catch (error) { target.textContent = error.message; }
    finally { button.disabled = false; }
});
</script>

<script>
// Jhon | 2026-11-24 14:20 WITA | TAMBAH: delegasi klik status mendukung baris infinite scroll, isi log tetap interaktif.
document.addEventListener('click', function (event) {
    const summary = event.target.closest('.kontrol-detail > summary');
    if (!summary) return;
    event.preventDefault();
    const modal = document.getElementById('surat-status-modal');
    const body = document.getElementById('surat-status-body');
    const content = summary.parentElement.querySelector('.kontrol-explanation');
    if (!modal || !body || !content) return;
    body.replaceChildren(content.cloneNode(true));
    modal.showModal();
});
</script>
