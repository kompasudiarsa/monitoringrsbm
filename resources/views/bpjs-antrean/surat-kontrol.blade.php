@extends('layouts.app', ['title' => 'Surat Kontrol Per Hari'])

@section('content')
<style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen biru BPJS selaras dashboard; warna status semantik dipertahankan. */

    .page {
        padding: 24px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .header {
        padding: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .title {
        font-size: 22px;
        font-weight: 950;
        color: #0f172a;
    }

    .subtitle {
        color: #64748b;
        font-size: 13px;
        margin-top: 4px;
    }

    .btn-back {
        background: #0f172a;
        color: #fff;
        padding: 9px 14px;
        border-radius: 14px;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-back:hover {
        color: #fff;
    }

    .filter-box {
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 180px 260px auto;
        gap: 12px;
        align-items: end;
    }

    label {
        display: block;
        font-size: 12px;
        font-weight: 900;
        color: #475569;
        margin-bottom: 6px;
    }

    input,
    select {
        width: 100%;
        height: 44px;
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0 12px;
        outline: none;
    }

    input:focus,
    select:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 4px rgba(2,132,199, .12);
    }

    .btn-filter {
        height: 44px;
        border: 0;
        border-radius: 14px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #fff;
        padding: 0 18px;
        font-weight: 950;
    }

    table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        padding: 12px;
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

    .badge-soft {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        background: #ffedd5;
        color: #c2410c;
        font-weight: 900;
        font-size: 12px;
    }

    .badge-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    @media (max-width: 900px) {
        .header {
            display: grid;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $itemsSuratKontrol = data_get($suratKontrol ?? [], 'response.list', data_get($suratKontrol ?? [], 'response', []));

    if (!is_array($itemsSuratKontrol)) {
        $itemsSuratKontrol = [];
    }

    $totalSuratKontrol = count($itemsSuratKontrol);

    $metadataCode = data_get($suratKontrol ?? [], 'metaData.code', data_get($suratKontrol ?? [], 'metadata.code', '-'));
    $metadataMessage = data_get($suratKontrol ?? [], 'metaData.message', data_get($suratKontrol ?? [], 'metadata.message', '-'));
@endphp

<div class="page">
    <div class="card">
        <div class="header">
            <div>
                <div class="title">Surat Kontrol Per Hari</div>
                <div class="subtitle">
                    Total {{ $totalSuratKontrol }} surat kontrol |
                    Status BPJS: {{ $metadataCode }} - {{ $metadataMessage }}
                </div>
            </div>

            <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
                Kembali ke Menu
            </a>
        </div>

        <form method="GET" action="{{ route('bpjs-antrean.surat-kontrol') }}" class="filter-box">
            <div class="filter-grid">
                <div>
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}">
                </div>

                <div>
                    <label>Filter</label>
                    <select name="filter">
                        <option value="2" {{ (int) $filter === 2 ? 'selected' : '' }}>
                            Tanggal Rencana Kontrol
                        </option>
                        <option value="1" {{ (int) $filter === 1 ? 'selected' : '' }}>
                            Tanggal Entri
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn-filter">
                    Tampilkan
                </button>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Surat Kontrol</th>
                        <th>Tgl Rencana Kontrol</th>
                        <th>No Kartu</th>
                        <th>Nama</th>
                        <th>Poli Tujuan</th>
                        <th>Dokter</th>
                        <th>No SEP</th>
                        <th>Terbit SEP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($itemsSuratKontrol as $index => $item)
                        @php
                            $terbitSep = data_get($item, 'terbitSEP', data_get($item, 'terbitSep', null));
                        @endphp

                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <b>
                                    {{ data_get($item, 'noSuratKontrol', data_get($item, 'no_surat_kontrol', '-')) }}
                                </b>
                            </td>

                            <td>
                                {{ data_get($item, 'tglRencanaKontrol', data_get($item, 'tgl_rencana_kontrol', '-')) }}
                            </td>

                            <td>
                                {{ data_get($item, 'noKartu', data_get($item, 'nokartu', '-')) }}
                            </td>

                            <td>
                                {{ data_get($item, 'nama', data_get($item, 'namaPeserta', data_get($item, 'namapeserta', '-'))) }}
                            </td>

                            <td>
                                {{ data_get($item, 'namaPoliTujuan', data_get($item, 'poliTujuan', data_get($item, 'namaPoli', '-'))) }}
                            </td>

                            <td>
                                {{ data_get($item, 'namaDokter', data_get($item, 'dokter', '-')) }}
                            </td>

                            <td>
                                {{ data_get($item, 'noSepAsalKontrol', data_get($item, 'noSEP', data_get($item, 'noSep', '-'))) }}
                            </td>

                            <td>
                                @if ($terbitSep === 'Sudah' || $terbitSep === '1' || $terbitSep === 1)
                                    <span class="badge-soft">Sudah</span>
                                @elseif ($terbitSep === 'Belum' || $terbitSep === '0' || $terbitSep === 0)
                                    <span class="badge-soft badge-danger">Belum</span>
                                @else
                                    <span class="badge-soft">{{ $terbitSep ?? '-' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:28px; color:#64748b;">
                                Tidak ada surat kontrol pada tanggal {{ $tanggal }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection