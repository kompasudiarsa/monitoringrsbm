@extends('layouts.app', ['title' => 'Detail Antrean BPJS'])

@section('content')
<style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen biru BPJS selaras dashboard; warna status semantik dipertahankan. */

    .detail-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 22px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .detail-card {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        border: 1px solid rgba(226, 232, 240, .8);
        padding: 18px;
        margin-bottom: 18px;
    }

    .detail-title {
        font-size: 24px;
        font-weight: 950;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .muted {
        color: #64748b;
        font-size: 13px;
    }

    .grid-detail {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .info-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 13px;
        border: 1px solid #e2e8f0;
    }

    .info-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .info-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
        margin-top: 4px;
    }

    .timeline {
        margin-top: 18px;
    }

    .timeline-item {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .task-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 64px;
        height: 34px;
        border-radius: 999px;
        background: #ffedd5;
        color: #c2410c;
        font-weight: 950;
    }

    .btn-back {
        display: inline-flex;
        text-decoration: none;
        background: #0f172a;
        color: #fff;
        padding: 10px 14px;
        border-radius: 14px;
        font-weight: 900;
        margin-bottom: 16px;
    }

    .btn-back:hover {
        color: #fff;
        opacity: .9;
    }

    pre {
        background: #0f172a;
        color: #e2e8f0;
        padding: 16px;
        border-radius: 16px;
        overflow-x: auto;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .grid-detail {
            grid-template-columns: 1fr;
        }

        .timeline-item {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
$response = data_get($detail, 'response', []);
$taskList = data_get($tasks, 'response.list', data_get($tasks, 'response', []));

if (isset($response[0])) {
$response = $response[0];
}

if (!is_array($response)) $response = [];
if (!is_array($taskList)) $taskList = [];

$taskNames = [
1 => 'Mulai waktu tunggu admisi',
2 => 'Akhir tunggu admisi / mulai layan admisi',
3 => 'Akhir layan admisi / mulai tunggu poli',
4 => 'Akhir tunggu poli / mulai layan poli',
5 => 'Akhir layan poli',
6 => 'Mulai layan farmasi',
7 => 'Akhir layan farmasi',
99 => 'Tidak hadir / batal',
];

if (!function_exists('formatBpjsTimeValue')) {
    function formatBpjsTimeValue($value) {
        if ($value === null || $value === '') {
            return '-';
        }

        $value = trim((string) $value);

        if ($value === '-' || $value === '0' || preg_match('/^0+$/', $value)) {
            return '-';
        }

        try {
            // Kalau response BPJS sudah berupa tanggal string:
            // 29-06-2026 06:50:26 WIB
            if (preg_match('/^\d{2}-\d{2}-\d{4}\s+\d{2}:\d{2}:\d{2}/', $value)) {
                return $value;
            }

            // Timestamp millisecond, contoh: 1719213600000
            if (is_numeric($value) && strlen($value) >= 13) {
                return \Carbon\Carbon::createFromTimestampMs((int) $value)
                    ->timezone('Asia/Makassar')
                    ->format('d-m-Y H:i:s');
            }

            // Timestamp second, contoh: 1719213600
            if (is_numeric($value) && strlen($value) === 10) {
                return \Carbon\Carbon::createFromTimestamp((int) $value)
                    ->timezone('Asia/Makassar')
                    ->format('d-m-Y H:i:s');
            }

            return $value;
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
    @endphp

    <div class="detail-page">
        <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
            Kembali ke Menu
        </a>

        <div class="detail-card">
            <div class="detail-title">Detail Antrean BPJS</div>
            <div class="muted">Kode booking: <b>{{ $kodebooking }}</b></div>

            <div class="grid-detail">
                <div class="info-box">
                    <div class="info-label">Nama Pasien</div>
                    <div class="info-value">{{ data_get($response, 'namapasien', '-') }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">No Kartu</div>
                    <div class="info-value">{{ data_get($response, 'nomorkartu', data_get($response, 'nokapst', '-')) }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">No Antrean</div>
                    <div class="info-value">{{ data_get($response, 'noantrean', data_get($response, 'noantrian', '-')) }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">Poli</div>
                    <div class="info-value">{{ data_get($response, 'namapoli', data_get($response, 'kodepoli', '-')) }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">Dokter</div>
                    <div class="info-value">{{ data_get($response, 'namadokter', data_get($response, 'kodedokter', '-')) }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ data_get($response, 'status', data_get($response, 'statusantrean', '-')) }}</div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-title">Timeline Task ID</div>
            <div class="muted">
                Task ID digunakan untuk melihat alur waktu admisi, poli, dan farmasi.
            </div>

            <div class="timeline">
                @forelse ($taskList as $task)
                @php
                $taskId = (int) data_get($task, 'taskid', data_get($task, 'task_id', 0));
              $waktuRs = data_get($task, 'wakturs', null);
$waktuBpjs = data_get($task, 'waktu', null);
                @endphp

                <div class="timeline-item">
                    <div>
                        <span class="task-badge">Task {{ $taskId }}</span>
                    </div>

                    <div>
                        <div style="font-weight:950; color:#0f172a">
                            {{ $taskNames[$taskId] ?? 'Task tidak dikenal' }}
                        </div>

                        <div class="muted" style="margin-top:5px">
                            Waktu RS:
                            <b>{{ formatBpjsTimeValue($waktuRs) }}</b>
                            <br>
                            Waktu BPJS:
                            <b>{{ formatBpjsTimeValue($waktuBpjs) }}</b>
                        </div>
                    </div>
                </div>
                @empty
                <div style="padding:20px; color:#64748b">
                    Belum ada data task ID atau response BPJS kosong.
                </div>
                @endforelse
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-title">Raw Response</div>
            <pre>{{ json_encode([
            'detail' => $detail,
            'tasks' => $tasks,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endsection