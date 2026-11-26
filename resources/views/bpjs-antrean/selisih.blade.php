@extends('layouts.app', ['title' => 'Selisih SIMRS vs BPJS'])

@section('content')
<style>
/* Jhon | 2026-11-26 13:27 WITA | UBAH: aksen biru BPJS selaras dashboard; warna status semantik dipertahankan. */

    .selisih-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 22px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .btn-back {
        display: inline-flex;
        margin-bottom: 14px;
        background: #0f172a;
        color: #fff;
        padding: 9px 14px;
        border-radius: 14px;
        font-weight: 900;
        text-decoration: none;
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
        grid-template-columns: 220px 220px auto;
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
    }

    .check-inline {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        font-weight: 800;
        color: #334155;
    }

    .btn-primary-custom {
        border: 0;
        border-radius: 14px;
        min-height: 44px;
        padding: 0 18px;
        font-weight: 900;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: white;
    }

    .page-title {
        font-size: 26px;
        font-weight: 950;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .page-desc {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 18px;
        line-height: 1.6;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
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
        background: rgba(2,132,199, .13);
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
        margin-bottom: 18px;
    }

    .table-card-header {
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 16px;
        font-weight: 900;
        color: #0f172a;
    }

    .table-subtitle {
        color: #64748b;
        font-size: 12px;
        margin-top: 4px;
    }

    .bpjs-table {
        width: 100%;
        margin: 0;
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
        white-space: nowrap;
    }

    .badge-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-info {
        background: #e0f2fe;
        color: #0369a1;
    }

    .empty-row {
        text-align: center;
        padding: 28px;
        color: #64748b;
    }
.selisih-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}

.selisih-tab-btn {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    border-radius: 999px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 900;
    cursor: pointer;
    transition: .16s ease;
}

.selisih-tab-btn:hover {
    background: #f0f9ff;
    color: #0369a1;
    border-color: #bae6fd;
}

.selisih-tab-btn.active {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 10px 24px rgba(2,132,199, .25);
}

.selisih-tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    margin-left: 8px;
    padding: 0 7px;
    border-radius: 999px;
    background: rgba(15, 23, 42, .08);
    font-size: 12px;
    font-weight: 950;
}

.selisih-tab-btn.active .selisih-tab-count {
    background: rgba(255, 255, 255, .2);
    color: #fff;
}

.selisih-tab-pane {
    display: none;
}

.selisih-tab-pane.active {
    display: block;
}
    @media (max-width: 900px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
    Kembali ke Menu
</a>

<div class="selisih-page">
    <h1 class="page-title">Selisih SIMRS vs BPJS</h1>

    <div class="page-desc">
        Menu ini digunakan untuk membandingkan data antrean dari SIMRS dengan data antrean BPJS.
        Sistem dapat mendeteksi kode booking ada di SIMRS tetapi tidak ada di BPJS, task ID belum lengkap,
        pasien sudah check-in tetapi belum update task ID, serta antrean BPJS aktif tetapi status SIMRS sudah selesai.
    </div>

    <form method="GET" action="{{ route('bpjs-antrean.selisih') }}" class="filter-card">
        <div class="filter-grid">
            <div>
                <div class="form-label">Tanggal</div>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
            </div>

            <label class="check-inline">
                <input type="checkbox" name="cek_task" value="1" {{ $cekTask ? 'checked' : '' }}>
                Cek kelengkapan Task ID
            </label>

            <button class="btn-primary-custom" type="submit">
                Bandingkan Data
            </button>
        </div>
    </form>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Total SIMRS</div>
            <div class="stat-value">{{ data_get($statistik, 'total_simrs', 0) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total BPJS</div>
            <div class="stat-value">{{ data_get($statistik, 'total_bpjs', 0) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">SIMRS Tidak Ada di BPJS</div>
            <div class="stat-value">{{ data_get($statistik, 'simrs_tidak_ada_di_bpjs', 0) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">BPJS Tidak Ada di SIMRS</div>
            <div class="stat-value">{{ data_get($statistik, 'bpjs_tidak_ada_di_simrs', 0) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Check-in Belum Update Task</div>
            <div class="stat-value">{{ data_get($statistik, 'checkin_belum_update_task', 0) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">BPJS Aktif, SIMRS Selesai</div>
            <div class="stat-value">{{ data_get($statistik, 'bpjs_aktif_simrs_selesai', 0) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Task ID Belum Lengkap</div>
            <div class="stat-value">{{ data_get($statistik, 'task_belum_lengkap', 0) }}</div>
        </div>
    </div>

   <div class="selisih-tabs">
    <button type="button" class="selisih-tab-btn active" data-tab="tab-simrs-tidak-ada">
        SIMRS Tidak Ada di BPJS
        <span class="selisih-tab-count">{{ count($simrsTidakAdaDiBpjs) }}</span>
    </button>

    <button type="button" class="selisih-tab-btn" data-tab="tab-bpjs-tidak-ada">
        BPJS Tidak Ada di SIMRS
        <span class="selisih-tab-count">{{ count($bpjsTidakAdaDiSimrs) }}</span>
    </button>

    <button type="button" class="selisih-tab-btn" data-tab="tab-checkin-task">
        Check-in Belum Update Task
        <span class="selisih-tab-count">{{ count($checkinBelumUpdateTask) }}</span>
    </button>

    <button type="button" class="selisih-tab-btn" data-tab="tab-bpjs-aktif-simrs-selesai">
        BPJS Aktif, SIMRS Selesai
        <span class="selisih-tab-count">{{ count($bpjsAktifSimrsSelesai) }}</span>
    </button>

    <button type="button" class="selisih-tab-btn" data-tab="tab-task-belum-lengkap">
        Task ID Belum Lengkap
        <span class="selisih-tab-count">{{ count($taskBelumLengkap) }}</span>
    </button>
</div>

<div class="selisih-tab-content">
    <div id="tab-simrs-tidak-ada" class="selisih-tab-pane active">
        @include('bpjs-antrean.partials.selisih-table', [
            'title' => 'Kode Booking Ada di SIMRS tetapi Tidak Ada di BPJS',
            'subtitle' => 'Data ini perlu dicek karena antrean sudah ada di SIMRS tetapi belum ditemukan pada antrean BPJS.',
            'items' => $simrsTidakAdaDiBpjs,
            'source' => 'simrs'
        ])
    </div>

    <div id="tab-bpjs-tidak-ada" class="selisih-tab-pane">
        @include('bpjs-antrean.partials.selisih-table', [
            'title' => 'Antrean BPJS Ada tetapi Tidak Ada di SIMRS',
            'subtitle' => 'Data ini perlu dicek jika BPJS aktif tetapi tidak ditemukan pada data reservasi SIMRS.',
            'items' => $bpjsTidakAdaDiSimrs,
            'source' => 'bpjs'
        ])
    </div>

    <div id="tab-checkin-task" class="selisih-tab-pane">
        @include('bpjs-antrean.partials.selisih-table', [
            'title' => 'Pasien Check-in tetapi Task ID BPJS Belum Update',
            'subtitle' => 'Pasien terdeteksi sudah berjalan di SIMRS, tetapi status/task antrean BPJS masih belum sesuai.',
            'items' => $checkinBelumUpdateTask,
            'source' => 'simrs'
        ])
    </div>

    <div id="tab-bpjs-aktif-simrs-selesai" class="selisih-tab-pane">
        @include('bpjs-antrean.partials.selisih-table', [
            'title' => 'Antrean BPJS Aktif tetapi SIMRS Sudah Selesai',
            'subtitle' => 'Data ini perlu dicek karena BPJS masih aktif, tetapi status SIMRS sudah selesai.',
            'items' => $bpjsAktifSimrsSelesai,
            'source' => 'bpjs'
        ])
    </div>

    <div id="tab-task-belum-lengkap" class="selisih-tab-pane">
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-title">Task ID Belum Lengkap</div>
                <div class="table-subtitle">
                    Ditampilkan hanya jika opsi cek kelengkapan Task ID dicentang.
                </div>
            </div>

            <div class="table-responsive">
                <table class="bpjs-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>Nama Pasien</th>
                            <th>No Kartu</th>
                            <th>No Antrean</th>
                            <th>Task Ada</th>
                            <th>Task Belum Ada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($taskBelumLengkap as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><b>{{ data_get($item, 'kodebooking', '-') }}</b></td>
                                <td>{{ data_get($item, 'namapasien', '-') }}</td>
                                <td>{{ data_get($item, 'nomorkartu', '-') }}</td>
                                <td>{{ data_get($item, 'noantrean', '-') }}</td>
                                <td>
                                    <span class="badge-soft badge-info">
                                        {{ implode(', ', data_get($item, 'task_ada', [])) ?: '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-soft badge-danger">
                                        {{ implode(', ', data_get($item, 'task_belum_ada', [])) ?: '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-row">
                                    {{ $cekTask ? 'Tidak ada task ID yang bermasalah.' : 'Centang opsi cek kelengkapan Task ID untuk menampilkan data.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-title">Task ID Belum Lengkap</div>
            <div class="table-subtitle">
                Ditampilkan hanya jika opsi cek kelengkapan Task ID dicentang.
            </div>
        </div>

        <div class="table-responsive">
            <table class="bpjs-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Booking</th>
                        <th>Nama Pasien</th>
                        <th>No Kartu</th>
                        <th>No Antrean</th>
                        <th>Task Ada</th>
                        <th>Task Belum Ada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($taskBelumLengkap as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><b>{{ data_get($item, 'kodebooking', '-') }}</b></td>
                            <td>{{ data_get($item, 'namapasien', '-') }}</td>
                            <td>{{ data_get($item, 'nomorkartu', '-') }}</td>
                            <td>{{ data_get($item, 'noantrean', '-') }}</td>
                            <td>
                                <span class="badge-soft badge-info">
                                    {{ implode(', ', data_get($item, 'task_ada', [])) ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-soft badge-danger">
                                    {{ implode(', ', data_get($item, 'task_belum_ada', [])) ?: '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-row">
                                {{ $cekTask ? 'Tidak ada task ID yang bermasalah.' : 'Centang opsi cek kelengkapan Task ID untuk menampilkan data.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.selisih-tab-btn');
        const panes = document.querySelectorAll('.selisih-tab-pane');

        function activateTab(tabId) {
            buttons.forEach(function (button) {
                button.classList.toggle('active', button.dataset.tab === tabId);
            });

            panes.forEach(function (pane) {
                pane.classList.toggle('active', pane.id === tabId);
            });
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const tabId = this.dataset.tab;

                activateTab(tabId);

                if (history.replaceState) {
                    history.replaceState(null, '', '#' + tabId);
                }
            });
        });

        const hashTab = window.location.hash.replace('#', '');

        if (hashTab && document.getElementById(hashTab)) {
            activateTab(hashTab);
        }
    });
</script>
@endsection