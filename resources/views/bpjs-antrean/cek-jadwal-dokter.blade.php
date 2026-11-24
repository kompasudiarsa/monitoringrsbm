@extends('layouts.app', [
    'title' => 'Pengecekan Jadwal Dokter'
])

@section('content')
    <style>
        .schedule-filter {
            display: grid;
            grid-template-columns: 180px minmax(260px, 1fr) minmax(280px, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .searchable-select {
            position: relative;
        }

        .searchable-options {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            left: 0;
            z-index: 100;
            display: none;
            max-height: 280px;
            padding: 6px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
        }

        .searchable-option {
            display: grid;
            gap: 3px;
            padding: 11px 12px;
            border-radius: 10px;
            cursor: pointer;
        }

        .searchable-option:hover {
            background: #eff6ff;
        }

        .searchable-option-title {
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .searchable-option-sub,
        .searchable-empty,
        .searchable-loading {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .searchable-empty,
        .searchable-loading {
            padding: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 18px 0;
        }

        .summary-card {
            padding: 16px;
            border-radius: 14px;
            background: #f8fafc;
        }

        .summary-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .summary-value {
            margin-top: 5px;
            color: #0f172a;
            font-size: 28px;
            font-weight: 800;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            white-space: nowrap;
        }

        .comparison-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-align: left;
            text-transform: uppercase;
        }

        .badge-status {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .badge-sesuai {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge-error {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-muted {
            background: #e2e8f0;
            color: #475569;
        }

        .comparison-note {
            display: block;
            max-width: 250px;
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
            white-space: normal;
        }

        @media (max-width: 900px) {
            .schedule-filter {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .schedule-filter,
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1>Pengecekan Jadwal Dokter</h1>

                <p class="text-muted">
                    Cari poli dan dokter melalui API SIMRS. Kode poli BPJS
                    memakai kdsubspesialisbpjs; jika kosong memakai
                    kdspesialisbpjs. Jadwal SIMRS kemudian dibandingkan
                    dengan jadwal BPJS.
                </p>

                <form
                    method="GET"
                    action="{{ route('bpjs-antrean.cek-jadwal-dokter') }}"
                    class="schedule-filter"
                    onsubmit="return validateJadwalFilter()"
                >
                    <div class="form-group">
                        <label for="tanggal" class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="tanggal"
                            name="tanggal"
                            value="{{ $tanggal }}"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label
                            for="poliSearchInput"
                            class="form-label"
                        >
                            Poli / Ruangan
                        </label>

                        <div
                            class="searchable-select"
                            id="poliSearchBox"
                        >
                            <input
                                type="text"
                                id="poliSearchInput"
                                class="form-control"
                                placeholder="Ketik nama poli"
                                autocomplete="off"
                                value="{{ $namaPoli }}"
                            >

                            <input
                                type="hidden"
                                name="kodepoli"
                                id="kodepoli"
                                value="{{ $kodePoli }}"
                                required
                            >

                            <input
                                type="hidden"
                                name="nama_poli"
                                id="nama_poli"
                                value="{{ $namaPoli }}"
                            >

                            <input
                                type="hidden"
                                name="ruangan_id"
                                id="ruangan_id"
                                value="{{ $ruanganId }}"
                            >

                            <div
                                class="searchable-options"
                                id="poliOptions"
                            ></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dokterSearchInput" class="form-label">
                            Dokter
                        </label>

                        <div
                            class="searchable-select"
                            id="dokterSearchBox"
                        >
                            <input
                                type="text"
                                id="dokterSearchInput"
                                class="form-control"
                                placeholder="Ketik nama dokter"
                                autocomplete="off"
                                value="{{ $namaDokter }}"
                            >

                            {{-- ID pegawai SIMRS --}}
                            <input
                                type="hidden"
                                name="dokter"
                                id="dokter"
                                value="{{ $dokterId }}"
                                required
                            >

                            <input
                                type="hidden"
                                name="nama_dokter"
                                id="nama_dokter"
                                value="{{ $namaDokter }}"
                            >

                            <div
                                class="searchable-options"
                                id="dokterOptions"
                            ></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Periksa Jadwal
                    </button>
                </form>
            </div>
        </div>

        @if($error)
            <div class="alert alert-danger mt-3">
                {{ $error }}
            </div>
        @endif

        @if($dokterId && ! $error)
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Total Jadwal</div>
                    <div class="summary-value">
                        {{ $summary['total'] }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Jadwal Sesuai</div>
                    <div class="summary-value">
                        {{ $summary['sesuai'] }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Jadwal Berbeda</div>
                    <div class="summary-value">
                        {{ $summary['berbeda'] }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Tidak Ada di BPJS</div>
                    <div class="summary-value">
                        {{ $summary['tidak_ada_bpjs'] }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if($hasil->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada jadwal SIMRS untuk dokter
                            <strong>{{ $dokter->namalengkap ?? $namaDokter }}</strong>
                            di
                            <strong>{{ $namaPoli ?: $kodePoli }}</strong>
                            pada tanggal
                            <strong>
                                {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
                            </strong>.
                        </div>
                    @else
                        <div class="table-wrapper">
                            <table class="comparison-table">
                                <thead>
                                    <tr>
                                        <th>Dokter</th>
                                        <th>Poli SIMRS</th>
                                        <th>Kode Poli</th>
                                        <th>Poli BPJS</th>
                                        <th>Jadwal SIMRS</th>
                                        <th>Jadwal BPJS</th>
                                        <th>Quota SIMRS</th>
                                        <th>Quota BPJS</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($hasil as $item)
                                        @php
                                            $badgeClass = match(
                                                $item['status']
                                            ) {
                                                'SESUAI' =>
                                                    'badge-sesuai',

                                                'BEDA_JAM',
                                                'BEDA_QUOTA',
                                                'BEDA_JAM_DAN_QUOTA',
                                                'BEDA_HARI',
                                                'LIBUR_BPJS' =>
                                                    'badge-warning',

                                                'TIDAK_ADA_BPJS',
                                                'TIDAK_ADA_MAPPING_POLI' =>
                                                    'badge-error',

                                                default =>
                                                    'badge-muted',
                                            };
                                        @endphp

                                        <tr>
                                            <td>
                                                <strong>
                                                    {{ $item['dokter_simrs'] }}
                                                </strong>

                                                <span class="comparison-note">
                                                    ID SIMRS:
                                                    {{ $item['objectpegawaifk'] }}
                                                </span>

                                                <span class="comparison-note">
                                                    Kode BPJS:
                                                    {{ $item['kodedokter_bpjs'] }}
                                                </span>

                                                @if($item['dokter_bpjs'] !== '-')
                                                    <span class="comparison-note">
                                                        Nama BPJS:
                                                        {{ $item['dokter_bpjs'] }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>{{ $item['poli_simrs'] }}</td>
                                            <td>{{ $item['kodepoli_bpjs'] }}</td>
                                            <td>{{ $item['poli_bpjs'] }}</td>
                                            <td>{{ $item['jam_simrs'] }}</td>
                                            <td>{{ $item['jam_bpjs'] }}</td>
                                            <td>{{ $item['quota_simrs'] }}</td>
                                            <td>{{ $item['quota_bpjs'] ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge-status {{ $badgeClass }}"
                                                >
                                                    {{ str_replace(
                                                        '_',
                                                        ' ',
                                                        $item['status']
                                                    ) }}
                                                </span>

                                                <span class="comparison-note">
                                                    {{ $item['keterangan'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        /*
         * Pencarian poli memakai route yang sama dengan proses
         * Batal Antrean per Jadwal.
         */
        const poliSearchInput = document.getElementById(
            'poliSearchInput'
        );
        const poliOptions = document.getElementById(
            'poliOptions'
        );
        const kodePoliInput = document.getElementById(
            'kodepoli'
        );
        const namaPoliInput = document.getElementById(
            'nama_poli'
        );
        const ruanganIdInput = document.getElementById(
            'ruangan_id'
        );

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

        function showPoliMessage(message, className) {
            poliOptions.innerHTML = `
                <div class="${className}">
                    ${escapeHtml(message)}
                </div>
            `;
            poliOptions.style.display = 'block';
        }

        function ambilKodePoliBpjs(item) {
            /*
             * Prioritas sesuai kebutuhan:
             * 1. kdsubspesialisbpjs
             * 2. kdspesialisbpjs
             *
             * item.kodepoli tetap didukung karena route ajax-poli
             * lama mungkin sudah melakukan normalisasi di server.
             */
            return String(
                item.kdsubspesialisbpjs
                || item.kdspesialisbpjs
                || item.kodepoli
                || ''
            ).trim().toUpperCase();
        }

        function renderPoliOptions(data) {
            poliOptions.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                showPoliMessage(
                    'Data poli tidak ditemukan.',
                    'searchable-empty'
                );
                return;
            }

            data.forEach(function(item) {
                const kodePoli = ambilKodePoliBpjs(item);
                const namaPoli = String(
                    item.nama
                    || item.namaruangan
                    || item.reportdisplay
                    || ''
                ).trim();

                const ruanganId = Number(
                    item.id
                    || item.qruangan
                    || item.objectruanganfk
                    || 0
                );

                if (!namaPoli) {
                    return;
                }

                const option = document.createElement('div');
                option.className = kodePoli
                    ? 'searchable-option'
                    : 'searchable-option disabled';

                option.innerHTML = `
                    <div class="searchable-option-title">
                        ${escapeHtml(namaPoli)}
                    </div>
                    <div class="searchable-option-sub">
                        ${
                            kodePoli
                                ? 'Kode BPJS: ' + escapeHtml(kodePoli)
                                : 'Kode subspesialis/spesialis BPJS kosong'
                        }
                        ${
                            ruanganId
                                ? ' | ID Ruangan: ' + escapeHtml(ruanganId)
                                : ''
                        }
                    </div>
                `;

                if (kodePoli) {
                    option.addEventListener('click', function() {
                        poliSearchInput.value = namaPoli;
                        kodePoliInput.value = kodePoli;
                        namaPoliInput.value = namaPoli;
                        ruanganIdInput.value = ruanganId || '';
                        poliOptions.style.display = 'none';
                    });
                }

                poliOptions.appendChild(option);
            });

            if (!poliOptions.children.length) {
                showPoliMessage(
                    'Data poli tidak memiliki nama.',
                    'searchable-empty'
                );
                return;
            }

            poliOptions.style.display = 'block';
        }

        function fetchPoli(keyword) {
            if (poliAbortController) {
                poliAbortController.abort();
            }

            poliAbortController = new AbortController();

            showPoliMessage(
                'Mengambil data poli...',
                'searchable-loading'
            );

            const url =
                "{{ route('bpjs-antrean.ajax-poli') }}"
                + '?query='
                + encodeURIComponent(keyword || '');

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: poliAbortController.signal,
            })
                .then(function(response) {
                    return response.json().then(function(json) {
                        if (!response.ok || json.success === false) {
                            throw new Error(
                                json.message
                                || 'Gagal mengambil data poli.'
                            );
                        }

                        return json;
                    });
                })
                .then(function(result) {
                    renderPoliOptions(result.data || []);
                })
                .catch(function(error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    showPoliMessage(
                        error.message || 'Gagal mengambil data poli.',
                        'searchable-empty'
                    );
                });
        }

        poliSearchInput.addEventListener('input', function() {
            const keyword = this.value.trim();

            kodePoliInput.value = '';
            namaPoliInput.value = '';
            ruanganIdInput.value = '';

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
            const box = document.getElementById(
                'poliSearchBox'
            );

            if (box && !box.contains(event.target)) {
                poliOptions.style.display = 'none';
            }
        });

        const dokterSearchInput = document.getElementById(
            'dokterSearchInput'
        );
        const dokterOptions = document.getElementById(
            'dokterOptions'
        );
        const dokterIdInput = document.getElementById('dokter');
        const namaDokterInput = document.getElementById(
            'nama_dokter'
        );

        let dokterSearchTimer = null;
        let dokterAbortController = null;

        function showDokterMessage(message, className) {
            dokterOptions.innerHTML = `
                <div class="${className}">
                    ${escapeHtml(message)}
                </div>
            `;
            dokterOptions.style.display = 'block';
        }

        function renderDokterOptions(data) {
            dokterOptions.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                showDokterMessage(
                    'Data dokter tidak ditemukan.',
                    'searchable-empty'
                );
                return;
            }

            data.forEach(function(item) {
                const dokterId = Number(item.id || 0);

                if (!dokterId) {
                    return;
                }

                const option = document.createElement('div');
                option.className = 'searchable-option';
                option.innerHTML = `
                    <div class="searchable-option-title">
                        ${escapeHtml(item.nama || '-')}
                    </div>
                    <div class="searchable-option-sub">
                        ID SIMRS: ${escapeHtml(dokterId)}
                        &nbsp;|&nbsp;
                        Kode BPJS:
                        ${escapeHtml(item.kodedokter_bpjs || '-')}
                    </div>
                `;

                option.addEventListener('click', function() {
                    dokterSearchInput.value = item.nama || '';
                    dokterIdInput.value = dokterId;
                    namaDokterInput.value = item.nama || '';
                    dokterOptions.style.display = 'none';
                });

                dokterOptions.appendChild(option);
            });

            if (!dokterOptions.children.length) {
                showDokterMessage(
                    'Data dokter tidak memiliki ID pegawai.',
                    'searchable-empty'
                );
                return;
            }

            dokterOptions.style.display = 'block';
        }

        function fetchDokter(keyword) {
            if (dokterAbortController) {
                dokterAbortController.abort();
            }

            dokterAbortController = new AbortController();

            showDokterMessage(
                'Mengambil data dokter...',
                'searchable-loading'
            );

            const url =
                "{{ route('bpjs-antrean.cek-jadwal-dokter.ajax-dokter') }}"
                + '?q=' + encodeURIComponent(keyword || '');

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: dokterAbortController.signal,
            })
                .then(function(response) {
                    return response.json().then(function(json) {
                        if (!response.ok || json.success === false) {
                            throw new Error(
                                json.message
                                || 'Gagal mengambil data dokter.'
                            );
                        }

                        return json;
                    });
                })
                .then(function(result) {
                    renderDokterOptions(result.data || []);
                })
                .catch(function(error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    showDokterMessage(
                        error.message || 'Gagal mengambil data dokter.',
                        'searchable-empty'
                    );
                });
        }

        dokterSearchInput.addEventListener('input', function() {
            const keyword = this.value.trim();

            dokterIdInput.value = '';
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
            const box = document.getElementById(
                'dokterSearchBox'
            );

            if (box && !box.contains(event.target)) {
                dokterOptions.style.display = 'none';
            }
        });

        function validateJadwalFilter() {
            if (!kodePoliInput.value) {
                alert(
                    'Silakan pilih poli dari hasil pencarian.'
                );
                poliSearchInput.focus();
                return false;
            }

            if (!dokterIdInput.value) {
                alert(
                    'Silakan pilih dokter dari hasil pencarian.'
                );
                dokterSearchInput.focus();
                return false;
            }

            return true;
        }
    </script>
@endsection