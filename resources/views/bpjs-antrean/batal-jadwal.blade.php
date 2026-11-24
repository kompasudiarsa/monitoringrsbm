@extends('layouts.app', ['title' => 'Batal Antrean Berdasarkan Jadwal'])

@section('content')
<style>
    .bpjs-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(249, 115, 22, .16), transparent 32%),
            linear-gradient(180deg, #fff7ed 0%, #f8fafc 38%, #f8fafc 100%);
        padding: 28px;
    }

    .bpjs-container {
        max-width: 1180px;
        margin: 0 auto;
    }

    .bpjs-eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: #ffedd5;
        color: #c2410c;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 12px;
    }

    .bpjs-title {
        font-size: 32px;
        font-weight: 950;
        color: #0f172a;
        line-height: 1.15;
        margin: 0;
        letter-spacing: -.04em;
    }

    .bpjs-desc {
        color: #64748b;
        font-size: 15px;
        margin-top: 10px;
        margin-bottom: 22px;
        line-height: 1.7;
    }

    .bpjs-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 28px;
        padding: 24px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        margin-bottom: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
    }

    .form-group {
        display: grid;
        gap: 8px;
    }

    .form-label {
        color: #0f172a;
        font-size: 13px;
        font-weight: 900;
        margin: 0;
    }

    .form-control {
        width: 100%;
        height: 48px;
        border-radius: 16px;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #0f172a;
        font-size: 14px;
        padding: 0 14px;
        outline: none;
    }

    textarea.form-control {
        height: auto;
        min-height: 90px;
        padding: 13px 14px;
        resize: vertical;
    }

    .form-control:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, .12);
    }

    .btn-orange,
    .btn-soft,
    .btn-danger-soft {
        height: 48px;
        border-radius: 16px;
        padding: 0 18px;
        font-size: 14px;
        font-weight: 950;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-orange {
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, #f97316, #ea580c);
        box-shadow: 0 10px 20px rgba(234, 88, 12, .22);
    }

    .btn-soft {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #dbe3ef;
    }

    .btn-danger-soft {
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        box-shadow: 0 10px 20px rgba(220, 38, 38, .18);
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .bpjs-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .bpjs-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 0 12px 4px;
        white-space: nowrap;
    }

    .bpjs-table td {
        background: #f8fafc;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        padding: 14px 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .bpjs-table td:first-child {
        border-radius: 16px 0 0 16px;
    }

    .bpjs-table td:last-child {
        border-radius: 0 16px 16px 0;
    }

    .badge-soft {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ffedd5;
        color: #c2410c;
        font-size: 12px;
        font-weight: 900;
    }

    .empty-state {
        text-align: center;
        color: #64748b;
        padding: 24px;
        font-weight: 700;
    }

    .actions-row {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    @media (max-width: 900px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    select.form-control {
        appearance: auto;
        padding-right: 36px;
    }

    @media (max-width: 640px) {
        .bpjs-page {
            padding: 18px;
        }

        .bpjs-title {
            font-size: 28px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .btn-orange,
        .btn-soft,
        .btn-danger-soft {
            width: 100%;
        }
    }

    .searchable-select {
        position: relative;
    }

    .searchable-options {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
        max-height: 280px;
        overflow-y: auto;
        z-index: 100;
        display: none;
        padding: 6px;
    }

    .searchable-option {
        padding: 11px 12px;
        border-radius: 12px;
        cursor: pointer;
        display: grid;
        gap: 3px;
    }

    .searchable-option:hover {
        background: #fff7ed;
    }

    .searchable-option.disabled {
        cursor: not-allowed;
        opacity: .55;
    }

    .searchable-option-title {
        font-size: 14px;
        font-weight: 900;
        color: #0f172a;
    }

    .searchable-option-sub {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .searchable-empty {
        padding: 13px;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .searchable-loading {
        padding: 13px;
        color: #ea580c;
        font-size: 13px;
        font-weight: 800;
    }
</style>

<div class="bpjs-page">
    <div class="bpjs-container">
        <div class="bpjs-eyebrow">
            Batal Antrean Berdasarkan Jadwal
        </div>
 <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
            Kembali ke Menu
        </a>
        <h1 class="bpjs-title">
            Ambil antrean dari BPJS, lalu batalkan kode booking terpilih.
        </h1>

        <div class="bpjs-desc">
            Data diambil dari endpoint pendaftaran berdasarkan tanggal, kemudian difilter berdasarkan poli dan dokter.
            Setelah data muncul, pilih kode booking yang ingin dibatalkan.
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bpjs-card">
            <form method="GET" action="{{ route('bpjs-antrean.batal-by-tanggal-dokter-poli') }}" onsubmit="return validateFilterJadwal()">
                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">Poli / Ruangan</label>

                        <div class="searchable-select" id="poliSearchBox">
                            <input
                                type="text"
                                id="poliSearchInput"
                                class="form-control"
                                placeholder="Ketik nama poli, contoh: bedah, kulit, mata"
                                autocomplete="off"
                                value="{{ request('nama_poli') }}">

                            <input
                                type="hidden"
                                name="kodepoli"
                                id="kodepoli"
                                value="{{ request('kodepoli') }}"
                                required>

                            <input
                                type="hidden"
                                name="nama_poli"
                                id="nama_poli"
                                value="{{ request('nama_poli') }}">

                            <div class="searchable-options" id="poliOptions"></div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="form-label">Dokter</label>

                        <div class="searchable-select" id="dokterSearchBox">
                            <input
                                type="text"
                                id="dokterSearchInput"
                                class="form-control"
                                placeholder="Ketik nama dokter"
                                autocomplete="off"
                                value="{{ request('nama_dokter') }}">

                            <input
                                type="hidden"
                                name="kodedokter"
                                id="kodedokter"
                                value="{{ request('kodedokter') }}"
                                required>

                            <input
                                type="hidden"
                                name="nama_dokter"
                                id="nama_dokter"
                                value="{{ request('nama_dokter') }}">

                            <div class="searchable-options" id="dokterOptions"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input
                            type="date"
                            name="tanggal"
                            value="{{ request('tanggal', now()->format('Y-m-d')) }}"
                            class="form-control"
                            required>
                    </div>
                </div>

                <div class="actions-row">
                    <button type="submit" class="btn-orange">
                        <i class="fas fa-search"></i>
                        Ambil Data Antrean
                    </button>

                    <a href="{{ route('bpjs-antrean.batal-jadwal.form') }}" class="btn-soft">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @if ($response)
        <div class="bpjs-card">
            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                <div>
                    <div style="font-weight:950; color:#0f172a; font-size:18px;">
                        Data Antrean BPJS
                    </div>
                    <div style="color:#64748b; font-size:13px; margin-top:4px;">
                        Total data: {{ count($list) }}
                    </div>
                </div>

                <div class="badge-soft">
                    {{ data_get($response, 'metadata.message', '-') }}
                </div>
            </div>

            @if (count($list) > 0)
            <form
                method="POST"
                action="{{ route('bpjs-antrean.batal-jadwal.submit') }}"
                onsubmit="return confirm('Yakin ingin membatalkan antrean yang dipilih?')">
                @csrf

                <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
                <input type="hidden" name="kodepoli" value="{{ request('kodepoli') }}">
                <input type="hidden" name="kodedokter" value="{{ request('kodedokter') }}">
                <input type="hidden" name="nama_poli" value="{{ request('nama_poli') }}">
                <input type="hidden" name="nama_dokter" value="{{ request('nama_dokter') }}">

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Keterangan Batal</label>
                    <textarea name="keterangan" class="form-control" required>{{ old('keterangan', 'Terjadi perubahan jadwal dokter, silahkan daftar kembali') }}</textarea>
                </div>

                <div class="table-wrap">
                    <table class="bpjs-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>Kode Booking</th>
                                <th>No Antrean</th>
                                <th>Nama</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $item)
                            @php
                            $kodebooking = data_get($item, 'kodebooking');
                            $status = data_get($item, 'status', data_get($item, 'statusantrean', '-'));
                            $statusLower = strtolower($status);

                            $tidakBisaBatal = in_array($statusLower, [
                            'batal',
                            'selesai dilayani',
                            'sedang dilayani',
                            ]);
                            @endphp

                            <tr>
                                <td>
                                    @if ($kodebooking && ! $tidakBisaBatal)
                                    <input
                                        type="checkbox"
                                        name="kodebooking[]"
                                        value="{{ $kodebooking }}"
                                        class="check-item">
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{ $kodebooking ?? '-' }}</td>
                                <td>{{ data_get($item, 'nomorantrean', data_get($item, 'noantrean', '-')) }}</td>
                                <td>{{ data_get($item, 'namapasien', data_get($item, 'nama', '-')) }}</td>
                                <td>{{ data_get($item, 'namapoli', data_get($item, 'kodepoli', '-')) }}</td>
                                <td>{{ data_get($item, 'namadokter', data_get($item, 'kodedokter', '-')) }}</td>
                                <td>
                                    <span class="badge-soft">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="actions-row">
                    <button type="submit" class="btn-danger-soft">
                        <i class="fas fa-calendar-times"></i>
                        Batalkan yang Dipilih
                    </button>
                </div>
            </form>
            @else
            <div class="empty-state">
                Tidak ada data antrean dari BPJS untuk parameter tersebut.
            </div>
            @endif
        </div>
        @endif

        @if (session('hasil_batal'))
        <div class="bpjs-card">
            <div style="font-weight:950; color:#0f172a; font-size:18px; margin-bottom:14px;">
                Hasil Batal Antrean
            </div>

            <div class="table-wrap">
                <table class="bpjs-table">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Code</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('hasil_batal') as $hasil)
                        <tr>
                            <td>{{ data_get($hasil, 'kodebooking') }}</td>
                            <td>{{ data_get($hasil, 'code') }}</td>
                            <td>{{ data_get($hasil, 'message') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    const checkAll = document.getElementById('checkAll');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.check-item').forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
        });
    }
    const poliSearchInput = document.getElementById('poliSearchInput');
    const poliOptions = document.getElementById('poliOptions');
    const kodepoliInput = document.getElementById('kodepoli');
    const namaPoliInput = document.getElementById('nama_poli');

    let poliSearchTimer = null;
    let poliAbortController = null;

    function escapeHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function showPoliLoading() {
        poliOptions.innerHTML = `
            <div class="searchable-loading">
                Mengambil data poli...
            </div>
        `;
        poliOptions.style.display = 'block';
    }

    function showPoliEmpty(message = 'Data poli tidak ditemukan.') {
        poliOptions.innerHTML = `
            <div class="searchable-empty">
                ${escapeHtml(message)}
            </div>
        `;
        poliOptions.style.display = 'block';
    }

    function renderPoliOptions(data) {
        poliOptions.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            showPoliEmpty();
            return;
        }

        data.forEach(function(item) {
            const option = document.createElement('div');
            const hasKodeBpjs = item.kodepoli && String(item.kodepoli).trim() !== '';

            option.className = hasKodeBpjs ?
                'searchable-option' :
                'searchable-option disabled';

            option.innerHTML = `
                <div class="searchable-option-title">
                    ${escapeHtml(item.nama || '-')}
                </div>
                <div class="searchable-option-sub">
                    ${
                        hasKodeBpjs
                            ? 'Kode BPJS: ' + escapeHtml(item.kodepoli)
                            : 'Kode BPJS kosong'
                    }
                    ${
                        item.kdruangan
                            ? ' • Kd Ruangan: ' + escapeHtml(item.kdruangan)
                            : ''
                    }
                </div>
            `;

            if (hasKodeBpjs) {
                option.addEventListener('click', function() {
                    poliSearchInput.value = item.nama;
                    kodepoliInput.value = item.kodepoli;
                    namaPoliInput.value = item.nama;
                    poliOptions.style.display = 'none';
                });
            }

            poliOptions.appendChild(option);
        });

        poliOptions.style.display = 'block';
    }

    function fetchPoli(keyword) {
        if (poliAbortController) {
            poliAbortController.abort();
        }

        poliAbortController = new AbortController();

        showPoliLoading();

        const url = "{{ route('bpjs-antrean.ajax-poli') }}" +
            "?query=" + encodeURIComponent(keyword || '');

        fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: poliAbortController.signal,
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data poli');
                }

                return response.json();
            })
            .then(function(result) {
                renderPoliOptions(result.data || []);
            })
            .catch(function(error) {
                if (error.name === 'AbortError') {
                    return;
                }

                showPoliEmpty(error.message || 'Gagal mengambil data poli');
            });
    }

    poliSearchInput.addEventListener('input', function() {
        const keyword = this.value.trim();

        kodepoliInput.value = '';
        namaPoliInput.value = '';

        clearTimeout(poliSearchTimer);

        poliSearchTimer = setTimeout(function() {
            fetchPoli(keyword);
        }, 400);
    });

    poliSearchInput.addEventListener('focus', function() {
        const keyword = this.value.trim();

        clearTimeout(poliSearchTimer);

        poliSearchTimer = setTimeout(function() {
            fetchPoli(keyword);
        }, 200);
    });

    document.addEventListener('click', function(event) {
        const box = document.getElementById('poliSearchBox');

        if (box && !box.contains(event.target)) {
            poliOptions.style.display = 'none';
        }
    });

    function validatePoliSelected() {
        const kodepoli = kodepoliInput.value;

        if (!kodepoli) {
            alert('Silakan pilih poli dari hasil pencarian.');
            poliSearchInput.focus();
            return false;
        }

        return true;
    }
    const dokterSearchInput = document.getElementById('dokterSearchInput');
    const dokterOptions = document.getElementById('dokterOptions');
    const kodedokterInput = document.getElementById('kodedokter');
    const namaDokterInput = document.getElementById('nama_dokter');

    let dokterSearchTimer = null;
    let dokterAbortController = null;

    function escapeHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function showDokterLoading() {
        dokterOptions.innerHTML = `
            <div class="searchable-loading">
                Mengambil data dokter...
            </div>
        `;
        dokterOptions.style.display = 'block';
    }

    function showDokterEmpty(message = 'Data dokter tidak ditemukan.') {
        dokterOptions.innerHTML = `
            <div class="searchable-empty">
                ${escapeHtml(message)}
            </div>
        `;
        dokterOptions.style.display = 'block';
    }

    function renderDokterOptions(data) {
        dokterOptions.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            showDokterEmpty();
            return;
        }

        data.forEach(function(item) {
            const option = document.createElement('div');
            const hasKodeBpjs = item.kodedokter && String(item.kodedokter).trim() !== '';

            option.className = hasKodeBpjs ?
                'searchable-option' :
                'searchable-option disabled';

            option.innerHTML = `
                <div class="searchable-option-title">
                    ${escapeHtml(item.nama || '-')}
                </div>
                <div class="searchable-option-sub">
                    ${
                        hasKodeBpjs
                            ? 'Kode Dokter BPJS: ' + escapeHtml(item.kodedokter)
                            : 'Kode Dokter BPJS kosong'
                    }
                </div>
            `;

            if (hasKodeBpjs) {
                option.addEventListener('click', function() {
                    dokterSearchInput.value = item.nama;
                    kodedokterInput.value = item.kodedokter;
                    namaDokterInput.value = item.nama;
                    dokterOptions.style.display = 'none';
                });
            }

            dokterOptions.appendChild(option);
        });

        dokterOptions.style.display = 'block';
    }

    function fetchDokter(keyword) {
        if (dokterAbortController) {
            dokterAbortController.abort();
        }

        dokterAbortController = new AbortController();

        showDokterLoading();

        const url = "{{ route('bpjs-antrean.ajax-dokter') }}" +
            "?query=" + encodeURIComponent(keyword || '');

        fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: dokterAbortController.signal,
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data dokter');
                }

                return response.json();
            })
            .then(function(result) {
                renderDokterOptions(result.data || []);
            })
            .catch(function(error) {
                if (error.name === 'AbortError') {
                    return;
                }

                showDokterEmpty(error.message || 'Gagal mengambil data dokter');
            });
    }

    dokterSearchInput.addEventListener('input', function() {
        const keyword = this.value.trim();

        kodedokterInput.value = '';
        namaDokterInput.value = '';

        clearTimeout(dokterSearchTimer);

        dokterSearchTimer = setTimeout(function() {
            fetchDokter(keyword);
        }, 400);
    });

    dokterSearchInput.addEventListener('focus', function() {
        const keyword = this.value.trim();

        clearTimeout(dokterSearchTimer);

        dokterSearchTimer = setTimeout(function() {
            fetchDokter(keyword);
        }, 200);
    });

    document.addEventListener('click', function(event) {
        const box = document.getElementById('dokterSearchBox');

        if (box && !box.contains(event.target)) {
            dokterOptions.style.display = 'none';
        }
    });

    function validateDokterSelected() {
        const kodedokter = kodedokterInput.value;

        if (!kodedokter) {
            alert('Silakan pilih dokter dari hasil pencarian.');
            dokterSearchInput.focus();
            return false;
        }

        return true;
    }

    function validateFilterJadwal() {
        if (typeof validatePoliSelected === 'function' && !validatePoliSelected()) {
            return false;
        }

        if (typeof validateDokterSelected === 'function' && !validateDokterSelected()) {
            return false;
        }

        return true;
    }
</script>
@endsection