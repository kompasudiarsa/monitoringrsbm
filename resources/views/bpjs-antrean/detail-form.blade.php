@extends('layouts.app', ['title' => 'Cari Detail Kode Booking'])

@section('content')
<style>
    .page {
        min-height: 100vh;
        background: #f8fafc;
        padding: 28px;
    }

    .card-search {
        max-width: 680px;
        margin: 60px auto;
        background: #fff;
        border-radius: 28px;
        padding: 26px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        border: 1px solid #e2e8f0;
    }

    .title {
        font-size: 26px;
        font-weight: 950;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .desc {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .input {
        width: 100%;
        height: 50px;
        border-radius: 16px;
        border: 1px solid #cbd5e1;
        padding: 0 15px;
        outline: none;
        font-weight: 800;
    }

    .input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, .12);
    }

    .actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .btn-main {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        height: 46px;
        padding: 0 18px;
        font-weight: 950;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        border-radius: 16px;
        background: #0f172a;
        color: #fff;
        height: 46px;
        padding: 0 18px;
        font-weight: 950;
        text-decoration: none;
    }

    .btn-back:hover {
        color: #fff;
        opacity: .9;
    }
</style>

<div class="page">
    <div class="card-search">
        <div class="title">Cari Detail Kode Booking</div>
        <div class="desc">
            Masukkan kode booking untuk melihat detail antrean BPJS dan timeline task ID.
        </div>

        <form onsubmit="return cariKodeBooking(event)">
            <input
                id="kodebooking"
                type="text"
                class="input"
                placeholder="Contoh: 8e75d8a"
                autocomplete="off"
                autofocus
                required
            >

            <div class="actions">
                <button type="submit" class="btn-main">
                    Cari Detail
                </button>

                <a href="{{ route('bpjs-antrean.menu') }}" class="btn-back">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function cariKodeBooking(event) {
        event.preventDefault();

        const kodebooking = document.getElementById('kodebooking').value.trim();

        if (!kodebooking) {
            return false;
        }

        window.location.href = "{{ url('/bpjs-antrean/kodebooking') }}" + '/' + encodeURIComponent(kodebooking);

        return false;
    }
</script>
@endsection