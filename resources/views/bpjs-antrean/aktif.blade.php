@extends('layouts.app', ['title' => 'Antrean Aktif BPJS'])

@section('content')
<style>
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
        border-bottom: 1px solid #e2e8f0;
    }

    .title {
        font-size: 22px;
        font-weight: 950;
        color: #0f172a;
    }

    .btn-back {
        background: #0f172a;
        color: #fff;
        padding: 9px 14px;
        border-radius: 14px;
        font-weight: 900;
        text-decoration: none;
    }

    .btn-back:hover {
        color: #fff;
    }

    table {
        width: 100%;
        margin: 0;
    }

    th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        padding: 12px;
    }

    td {
        padding: 12px;
        border-top: 1px solid #eef2f7;
        color: #334155;
        font-size: 13px;
    }
</style>

@php
    $items = data_get($antreanAktif, 'response.list', data_get($antreanAktif, 'response', []));
    if (!is_array($items)) $items = [];
@endphp

<div class="page">
    <div class="card">
        <div class="header">
            <div>
                <div class="title">Antrean Aktif BPJS</div>
                <div style="color:#64748b; font-size:13px;">
                    Total data: {{ count($items) }}
                </div>
            </div>

            <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
                Kembali
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Booking</th>
                        <th>No Antrean</th>
                        <th>Nama Pasien</th>
                        <th>No Kartu</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $index => $item)
                        @php
                            $kodebooking = data_get($item, 'kodebooking', data_get($item, 'kode_booking'));
                        @endphp

                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if ($kodebooking)
                                    <a href="{{ route('bpjs-antrean.detail', $kodebooking) }}">
                                        <b>{{ $kodebooking }}</b>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ data_get($item, 'noantrean', data_get($item, 'noantrian', '-')) }}</td>
                            <td>{{ data_get($item, 'namapasien', '-') }}</td>
                            <td>{{ data_get($item, 'nomorkartu', data_get($item, 'nokapst', '-')) }}</td>
                            <td>{{ data_get($item, 'namapoli', data_get($item, 'kodepoli', '-')) }}</td>
                            <td>{{ data_get($item, 'namadokter', data_get($item, 'kodedokter', '-')) }}</td>
                            <td>{{ data_get($item, 'status', data_get($item, 'statusantrean', '-')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:28px; color:#64748b;">
                                Tidak ada antrean aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection