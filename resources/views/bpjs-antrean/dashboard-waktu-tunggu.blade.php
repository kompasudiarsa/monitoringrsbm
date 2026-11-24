@extends('layouts.app', ['title' => 'Dashboard Waktu Tunggu BPJS'])

@section('content')
<style>
    .bpjs-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 22px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .bpjs-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .bpjs-title {
        font-size: 26px;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
    }

    .bpjs-subtitle {
        color: #64748b;
        margin-top: 6px;
        font-size: 14px;
    }

    .filter-card,
    .stat-card,
    .table-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        border: 1px solid rgba(226, 232, 240, .8);
    }

    .filter-card {
        padding: 16px;
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .form-label {
        font-weight: 800;
        font-size: 13px;
        color: #334155;
        margin-bottom: 6px;
    }

    .form-control {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        min-height: 44px;
        width: 100%;
    }

    .btn-primary-custom {
        border: 0;
        border-radius: 14px;
        min-height: 44px;
        padding: 0 18px;
        font-weight: 900;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
        cursor: pointer;
    }

    .btn-reset-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        border-radius: 14px;
        padding: 0 18px;
        font-weight: 900;
        background: #e2e8f0;
        color: #334155;
        text-decoration: none;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .stat-card {
        padding: 16px;
        position: relative;
        overflow: hidden;
    }

    .stat-card:before {
        content: "";
        position: absolute;
        right: -30px;
        top: -30px;
        width: 90px;
        height: 90px;
        background: rgba(249, 115, 22, .13);
        border-radius: 999px;
    }

    .stat-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .stat-value {
        color: #0f172a;
        font-size: 28px;
        font-weight: 950;
        margin-top: 6px;
    }

    .table-card {
        overflow: hidden;
    }

    .table-card-header {
        padding: 16px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 16px;
        font-weight: 900;
        color: #0f172a;
    }

    .auto-refresh {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .bpjs-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .bpjs-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 13px;
        white-space: nowrap;
    }

    .bpjs-table td {
        padding: 13px;
        vertical-align: middle;
        color: #334155;
        border-top: 1px solid #eef2f7;
        font-size: 13px;
        white-space: nowrap;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        background: #ffedd5;
        color: #c2410c;
    }

    .task-note {
        padding: 16px 18px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .task-note-title {
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .task-note-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 8px;
        color: #475569;
        font-size: 13px;
    }

    @media (max-width: 900px) {
        .bpjs-header {
            display: grid;
            grid-template-columns: 1fr;
        }

        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .stats {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
$formatDetikBpjs = function ($detik) {
    $detik = (int) $detik;

    if ($detik <= 0) {
        return '0 detik';
    }

    $jam = floor($detik / 3600);
    $menit = floor(($detik % 3600) / 60);
    $sisaDetik = $detik % 60;

    if ($jam > 0) {
        return $jam . ' jam ' . $menit . ' menit ' . $sisaDetik . ' detik';
    }

    if ($menit > 0) {
        return $menit . ' menit ' . $sisaDetik . ' detik';
    }

    return $sisaDetik . ' detik';
};

$formatTimestampMsBpjs = function ($timestampMs) {
    if (empty($timestampMs)) {
        return '-';
    }

    try {
        return \Carbon\Carbon::createFromTimestampMs((int) $timestampMs)
            ->timezone(config('app.timezone', 'Asia/Makassar'))
            ->format('d-m-Y H:i:s');
    } catch (\Throwable $e) {
        return '-';
    }
};

$totalDashboardWaktu = count($dashboardItems);

$totalAntreanDashboard = collect($dashboardItems)->sum(function ($item) {
    return (int) data_get($item, 'jumlah_antrean', 0);
});

$rataTask1 = collect($dashboardItems)->avg('avg_waktu_task1') ?? 0;
$rataTask3 = collect($dashboardItems)->avg('avg_waktu_task3') ?? 0;
@endphp

<a href="{{ route('bpjs-antrean.menu') }}" style="
    display:inline-flex;
    margin-bottom:14px;
    background:#0f172a;
    color:#fff;
    padding:9px 14px;
    border-radius:14px;
    font-weight:900;
    text-decoration:none;
">
    Kembali ke Menu
</a>

<div class="bpjs-page">
    <div class="bpjs-header">
        <div>
            <h1 class="bpjs-title">Dashboard Waktu Tunggu BPJS</h1>
            <div class="bpjs-subtitle">
                Monitoring waktu tunggu task 1 sampai task 6 berdasarkan data BPJS.
            </div>
        </div>

        <div class="auto-refresh">
            Auto refresh setiap <b>30 detik</b>
        </div>
    </div>

    <form method="GET" action="{{ route('bpjs-antrean.dashboard-waktu-tunggu') }}" class="filter-card">
        <div class="filter-grid">
            <div>
                <div class="form-label">Tanggal</div>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>

            <div>
                <div class="form-label">Acuan Waktu</div>
                <select name="waktu" class="form-control">
                    <option value="rs" {{ ($waktu ?? 'rs') === 'rs' ? 'selected' : '' }}>
                        Waktu RS
                    </option>
                    <option value="server" {{ ($waktu ?? 'rs') === 'server' ? 'selected' : '' }}>
                        Waktu Server BPJS
                    </option>
                </select>
            </div>

            <button class="btn-primary-custom" type="submit">
                Tampilkan Data
            </button>

            <a href="{{ route('bpjs-antrean.dashboard-waktu-tunggu') }}" class="btn-reset-filter">
                Reset
            </a>
        </div>
    </form>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Total Poli</div>
            <div class="stat-value">{{ $totalDashboardWaktu }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Antrean</div>
            <div class="stat-value">{{ $totalAntreanDashboard }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Rata Tunggu Admisi</div>
            <div class="stat-value" style="font-size:18px;">
                {{ $formatDetikBpjs($rataTask1) }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Rata Tunggu Poli</div>
            <div class="stat-value" style="font-size:18px;">
                {{ $formatDetikBpjs($rataTask3) }}
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="table-title">Data Waktu Tunggu Per Poli</div>
                <div class="auto-refresh">
                    Tanggal {{ $tanggal }} | Acuan waktu: {{ strtoupper($waktu ?? 'rs') }}
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="bpjs-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Poli</th>
                        <th>Jumlah Antrean</th>
                        <th>Task 1<br><small>Tunggu Admisi</small></th>
                        <th>Task 2<br><small>Layan Admisi</small></th>
                        <th>Task 3<br><small>Tunggu Poli</small></th>
                        <th>Task 4<br><small>Layan Poli</small></th>
                        <th>Task 5<br><small>Tunggu Farmasi</small></th>
                        <th>Task 6<br><small>Layan Farmasi</small></th>
                        <th>Waktu Ambil Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dashboardItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>


                            <td>
                                <b>{{ data_get($item, 'kodepoli', '-') }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    {{ data_get($item, 'namapoli', '-') }}
                                </div>
                            </td>

                            <td>
                                <span class="badge-soft">
                                    {{ data_get($item, 'jumlah_antrean', 0) }} antrean
                                </span>
                            </td>

                            <td>
                                <b>{{ $formatDetikBpjs(data_get($item, 'waktu_task1', 0)) }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    Avg: {{ $formatDetikBpjs(data_get($item, 'avg_waktu_task1', 0)) }}
                                </div>
                            </td>

                            <td>
                                <b>{{ $formatDetikBpjs(data_get($item, 'waktu_task2', 0)) }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    Avg: {{ $formatDetikBpjs(data_get($item, 'avg_waktu_task2', 0)) }}
                                </div>
                            </td>

                            <td>
                                <b>{{ $formatDetikBpjs(data_get($item, 'waktu_task3', 0)) }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    Avg: {{ $formatDetikBpjs(data_get($item, 'avg_waktu_task3', 0)) }}
                                </div>
                            </td>

                            <td>
                                <b>{{ $formatDetikBpjs(data_get($item, 'waktu_task4', 0)) }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    Avg: {{ $formatDetikBpjs(data_get($item, 'avg_waktu_task4', 0)) }}
                                </div>
                            </td>

                            <td>
                                <b>{{ $formatDetikBpjs(data_get($item, 'waktu_task5', 0)) }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    Avg: {{ $formatDetikBpjs(data_get($item, 'avg_waktu_task5', 0)) }}
                                </div>
                            </td>

                            <td>
                                <b>{{ $formatDetikBpjs(data_get($item, 'waktu_task6', 0)) }}</b>
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    Avg: {{ $formatDetikBpjs(data_get($item, 'avg_waktu_task6', 0)) }}
                                </div>
                            </td>

                            <td>
                                {{ $formatTimestampMsBpjs(data_get($item, 'insertdate')) }}
                                <div style="font-size:12px; color:#64748b; margin-top:3px;">
                                    {{ data_get($item, 'tanggal', '-') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align:center; padding:30px; color:#64748b">
                                Tidak ada data dashboard waktu tunggu dari BPJS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="task-note">
            <div class="task-note-title">Keterangan Task</div>

            <div class="task-note-grid">
                <div><b>Task 1</b> = Waktu tunggu admisi</div>
                <div><b>Task 2</b> = Waktu layan admisi</div>
                <div><b>Task 3</b> = Waktu tunggu poli</div>
                <div><b>Task 4</b> = Waktu layan poli</div>
                <div><b>Task 5</b> = Waktu tunggu farmasi</div>
                <div><b>Task 6</b> = Waktu layan farmasi</div>
            </div>

            <div style="color:#64748b; font-size:12px; margin-top:10px;">
                Semua waktu task dari BPJS dalam satuan detik. Insertdate adalah waktu pengambilan data dalam timestamp milisecond.
                Waktu server adalah data task yang dicatat server BPJS, sedangkan waktu RS adalah data task yang dikirimkan RS.
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>
@endsection