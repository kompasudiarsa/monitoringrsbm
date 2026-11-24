@extends('layouts.app', ['title' => 'Batal Antrean BPJS'])

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
        max-width: 760px;
        margin: 0 auto;
    }

    .bpjs-header {
        margin-bottom: 20px;
    }

    .bpjs-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        line-height: 1.7;
    }

    .bpjs-card {
    background: #fff;
    border: 1px solid rgba(226, 232, 240, .95);
    border-radius: 28px;
    padding: 28px;
    box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
    max-width: 680px;
}

.batal-form {
    display: grid;
    gap: 18px;
}

.form-group {
    display: grid;
    gap: 8px;
}

.form-label {
    display: block;
    color: #0f172a;
    font-size: 14px;
    font-weight: 900;
    margin: 0;
}

.form-control {
    display: block;
    width: 100%;
    height: 52px;
    box-sizing: border-box;
    border-radius: 16px;
    border: 1px solid #dbe3ef;
    background: #fff;
    color: #0f172a;
    font-size: 14px;
    font-family: inherit;
    padding: 0 15px;
    outline: none;
    transition: .18s ease;
}

textarea.form-control {
    height: auto;
    min-height: 120px;
    padding: 14px 15px;
    resize: vertical;
    line-height: 1.5;
}

.form-control::placeholder {
    color: #94a3b8;
}

.form-control:focus {
    border-color: #f97316;
    box-shadow: 0 0 0 4px rgba(249, 115, 22, .12);
}

.form-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 4px;
}

.btn-orange,
.btn-soft {
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
}

.btn-orange {
    border: 0;
    color: #fff;
    background: linear-gradient(135deg, #f97316, #ea580c);
    box-shadow: 0 10px 20px rgba(234, 88, 12, .22);
}

.btn-orange:hover {
    filter: brightness(.98);
}

.btn-soft {
    background: #f8fafc;
    color: #334155;
    border: 1px solid #dbe3ef;
}

.btn-soft:hover {
    background: #f1f5f9;
    color: #0f172a;
    text-decoration: none;
}

.invalid-feedback {
    display: block;
    color: #dc2626;
    font-size: 12px;
    font-weight: 700;
    margin-top: 4px;
}

@media (max-width: 640px) {
    .bpjs-card {
        padding: 20px;
        border-radius: 24px;
    }

    .form-actions {
        display: grid;
        grid-template-columns: 1fr;
    }

    .btn-orange,
    .btn-soft {
        width: 100%;
    }
}
</style>

<div class="bpjs-page">
    <div class="bpjs-container">
        <div class="bpjs-header">
            <div class="bpjs-eyebrow">
                Batal Antrean Online BPJS
            </div>

            <h1 class="bpjs-title">
                Batalkan antrean berdasarkan kode booking.
            </h1>

            <div class="bpjs-desc">
                Masukkan kode booking pasien dan keterangan pembatalan. Sistem akan mengirim request batal antrean ke BPJS.
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="bpjs-card">
            <form method="POST" action="{{ route('bpjs-antrean.batal-manual') }}" class="batal-form">
    @csrf

    <div class="form-group">
        <label for="kodebooking" class="form-label">Kode Booking</label>

        <input
            type="text"
            id="kodebooking"
            name="kodebooking"
            value="{{ old('kodebooking') }}"
            class="form-control @error('kodebooking') is-invalid @enderror"
            placeholder="Contoh: 8e75d8a"
            autocomplete="off"
            required
            autofocus
        >

        @error('kodebooking')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="keterangan" class="form-label">Keterangan Batal</label>

        <textarea
            id="keterangan"
            name="keterangan"
            class="form-control @error('keterangan') is-invalid @enderror"
            required
        >{{ old('keterangan', 'Terjadi perubahan jadwal dokter, silahkan daftar kembali') }}</textarea>

        @error('keterangan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-actions">
        <button
            type="submit"
            class="btn-orange"
            onclick="return confirm('Yakin ingin membatalkan antrean BPJS ini?')"
        >
            <i class="fas fa-calendar-times"></i>
            Batalkan Antrean
        </button>

        <a href="{{ route('bpjs-antrean.menu') }}" class="btn-soft">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</form>

            @if (session('bpjs_response'))
                <div class="bpjs-response">
                    <strong>Response BPJS:</strong>
                    <pre class="mb-0 mt-2" style="color:inherit;">{{ json_encode(session('bpjs_response'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection