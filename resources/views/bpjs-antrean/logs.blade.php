@extends('layouts.app', ['title' => 'Log Request BPJS'])

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
        vertical-align: top;
    }

    .badge {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        background: #ffedd5;
        color: #c2410c;
        font-weight: 900;
        font-size: 12px;
    }
</style>

<div class="page">
    <div class="card">
        <div class="header">
            <div>
                <div class="title">Log Request BPJS</div>
                <div style="color:#64748b; font-size:13px;">
                    Menampilkan 20 request terakhir per halaman.
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
                        <th>Waktu</th>
                        <th>Method</th>
                        <th>Endpoint</th>
                        <th>Status HTTP</th>
                        <th>Metadata</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td>
                                <span class="badge">{{ $log->method }}</span>
                            </td>
                            <td>
                                <b>{{ $log->endpoint }}</b>
                            </td>
                            <td>{{ $log->status_code }}</td>
                            <td>
                                <b>{{ $log->metadata_code }}</b>
                                <br>
                                {{ $log->metadata_message }}
                            </td>
                            <td>{{ $log->duration_ms }} ms</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:28px; color:#64748b;">
                                Belum ada log request BPJS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px;">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection