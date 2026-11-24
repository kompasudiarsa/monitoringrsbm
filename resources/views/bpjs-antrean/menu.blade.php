{{-- Jhon | 2026-11-23 20:51 WITA | UBAH: judul halaman depan Dashboard Monitoring. --}}
@extends('layouts.app', ['title' => 'Dashboard Monitoring'])

@section('content')
<style>
    .bpjs-home {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(249, 115, 22, .16), transparent 32%),
            linear-gradient(180deg, #fff7ed 0%, #f8fafc 38%, #f8fafc 100%);
        padding: 28px;
    }

    .bpjs-wrap {
        max-width: 1200px;
        margin: 0 auto;
    }

    .bpjs-hero {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: center;
        margin-bottom: 24px;
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
        font-size: 34px;
        font-weight: 950;
        color: #0f172a;
        line-height: 1.15;
        margin: 0;
        letter-spacing: -.04em;
    }

    .bpjs-desc {
        color: #64748b;
        font-size: 15px;
        max-width: 680px;
        margin-top: 10px;
        line-height: 1.7;
    }

    .bpjs-status {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, .9);
        box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        border-radius: 24px;
        padding: 16px;
        min-width: 240px;
    }

    .status-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-value {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        color: #0f172a;
        font-size: 16px;
        font-weight: 950;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 6px rgba(34, 197, 94, .13);
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .menu-card {
        position: relative;
        display: block;
        text-decoration: none;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 28px;
        padding: 20px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, .07);
        overflow: hidden;
        min-height: 190px;
        transition: .18s ease;
    }

    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 22px 48px rgba(15, 23, 42, .11);
        text-decoration: none;
    }

    .menu-card:before {
        content: "";
        position: absolute;
        right: -40px;
        top: -40px;
        width: 130px;
        height: 130px;
        border-radius: 999px;
        background: rgba(249, 115, 22, .13);
    }

    .menu-icon {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        font-size: 22px;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .menu-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 950;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
    }

    .menu-desc {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        position: relative;
        z-index: 1;
        margin-bottom: 16px;
    }

    .menu-action {
        position: absolute;
        left: 20px;
        bottom: 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #ea580c;
        font-size: 13px;
        font-weight: 950;
        z-index: 1;
    }

    .quick-search {
        margin-top: 18px;
        background: #0f172a;
        border-radius: 28px;
        padding: 18px;
        color: #fff;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .quick-title {
        font-size: 17px;
        font-weight: 950;
        margin-bottom: 6px;
    }

    .quick-desc {
        color: #cbd5e1;
        font-size: 13px;
    }

    .quick-form {
        display: flex;
        gap: 10px;
        min-width: 420px;
    }

    .quick-input {
        height: 46px;
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, .14);
        background: rgba(255, 255, 255, .08);
        color: #fff;
        padding: 0 14px;
        outline: none;
        width: 100%;
    }

    .quick-input::placeholder {
        color: #94a3b8;
    }

    .quick-btn {
        height: 46px;
        border: 0;
        border-radius: 15px;
        padding: 0 16px;
        font-weight: 950;
        color: #fff;
        background: linear-gradient(135deg, #f97316, #ea580c);
        white-space: nowrap;
    }

    @media (max-width: 1000px) {
        .bpjs-hero {
            display: grid;
        }

        .menu-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .quick-search {
            grid-template-columns: 1fr;
        }

        .quick-form {
            min-width: 0;
        }
    }

    @media (max-width: 640px) {
        .bpjs-home {
            padding: 18px;
        }

        .bpjs-title {
            font-size: 28px;
        }

        .menu-grid {
            grid-template-columns: 1fr;
        }

        .quick-form {
            display: grid;
        }
    }
</style>

<div class="bpjs-home">
    <div class="bpjs-wrap">
        <div class="bpjs-hero">
            <div>
                <div class="bpjs-eyebrow">
                    Monitoring Antrean Online BPJS
                </div>

                {{-- Jhon | 2026-11-23 20:51 WITA | UBAH: selaraskan judul utama halaman depan. --}}
                <h1 class="bpjs-title">
                    Dashboard Monitoring
                </h1>

                <div class="bpjs-desc">
                    Pantau antrean per tanggal, antrean aktif, detail kode booking, task ID,
                    log bridging, dan selisih data SIMRS dengan BPJS dari satu halaman.
                </div>
            </div>

            <div class="bpjs-status">
                <div class="status-label">Status Aplikasi</div>
                <div class="status-value">
                    <span class="status-dot"></span>
                    Siap digunakan
                </div>
                <div class="quick-desc" style="margin-top:8px;">
                    {{ now()->format('d-m-Y H:i') }}
                </div>
            </div>
        </div>

        <div class="menu-grid">
            <!-- <a href="{{ route('bpjs-antrean.surat-kontrol') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-file-medical"></i>
                </div>

                <div class="menu-title">Surat Kontrol Per Hari</div>

                <div class="menu-desc">
                    Lihat daftar surat kontrol BPJS berdasarkan tanggal rencana kontrol atau tanggal entri.
                </div>

                <div class="menu-action">
                    Lihat Surat Kontrol
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a> -->
            <a href="{{ route('bpjs-monitoring.index') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-file-medical"></i>
                </div>

                <div class="menu-title"> Monitoring Bridging BPJS</div>

                <div class="menu-desc">
                    Monitoring akses bridging BPJS, termasuk request, response, durasi akses, dan error.
                </div>

                <div class="menu-action">
                    Cek Bridging BPJS
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.dashboard') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="menu-title">Dashboard Antrean</div>
                <div class="menu-desc">
                    Lihat antrean BPJS per tanggal, jumlah antrean, status terakhir, dan waktu tunggu.
                </div>
                <div class="menu-action">
                    Buka Dashboard
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.cek-surat-kontrol') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>

                <div class="menu-title">Cek Surat Kontrol</div>

                <div class="menu-desc">
                    Bandingkan data reservasi SIMRS dengan surat kontrol BPJS dan tampilkan pasien yang belum punya surat kontrol.
                </div>

                <div class="menu-action">
                    Bandingkan Data
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.cek-riwayat-kontrol') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div class="menu-title">Cek Reservasi VS Antrean BPJS</div>

                <div class="menu-desc">
                    Bandingkan riwayat kontrol dari SIMRS dengan antrean BPJS per tanggal.
                    Status akan menunjukkan apakah no reservasi sudah masuk sebagai kode booking BPJS.
                </div>

                <div class="menu-action">
                    Bandingkan Data
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.cek-jadwal-dokter') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-user-clock"></i>
                </div>

                <div class="menu-title">Cek Jadwal Dokter</div>

                <div class="menu-desc">
                    Bandingkan jadwal dokter SIMRS dengan jadwal BPJS berdasarkan tanggal,
                    kode dokter, kode poli, jam praktik, dan kapasitas pasien.
                </div>

                <div class="menu-action">
                    Periksa Jadwal
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('bpjs-antrean.aktif') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="menu-title">Antrean Aktif</div>
                <div class="menu-desc">
                    Tampilkan pasien yang masih aktif atau belum selesai dilayani di antrean BPJS.
                </div>
                <div class="menu-action">
                    Lihat Antrean
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('bpjs-antrean.detail-form') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="menu-title">Detail Kode Booking</div>
                <div class="menu-desc">
                    Cari detail antrean berdasarkan kode booking dan lihat timeline task ID BPJS.
                </div>
                <div class="menu-action">
                    Cari Kode Booking
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.batal-form') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>

                <div class="menu-title">Batal Antrean</div>

                <div class="menu-desc">
                    Batalkan antrean BPJS berdasarkan kode booking pasien dengan keterangan pembatalan.
                </div>

                <div class="menu-action">
                    Buka Form Batal
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.batal-jadwal.form') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>

                <div class="menu-title">Batal Antrean per Jadwal</div>

                <div class="menu-desc">
                    Ambil antrean berdasarkan poli, dokter, hari, dan jam praktek, lalu batalkan kode booking yang dipilih.
                </div>

                <div class="menu-action">
                    Buka Batal Jadwal
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{ route('bpjs-antrean.logs') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="menu-title">Log Request BPJS</div>
                <div class="menu-desc">
                    Audit request, response, metadata, durasi akses, dan error dari bridging BPJS.
                </div>
                <div class="menu-action">
                    Lihat Log
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('bpjs-antrean.selisih') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="menu-title">Selisih SIMRS vs BPJS</div>
                <div class="menu-desc">
                    Cek antrean yang ada di SIMRS tetapi belum sesuai dengan data BPJS.
                </div>
                <div class="menu-action">
                    Cek Selisih
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('bpjs-antrean.dashboard', ['tanggal' => now()->format('Y-m-d')]) }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="menu-title">Antrean Hari Ini</div>
                <div class="menu-desc">
                    Shortcut untuk langsung membuka monitoring antrean BPJS tanggal hari ini.
                </div>
                <div class="menu-action">
                    Buka Hari Ini
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <a href="{{route('bpjs-antrean.dashboard-waktu-tunggu') }}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="menu-title"> Dashboard Waktu Tunggu</div>
                <div class="menu-desc">
                    Monitoring waktu tunggu task 1 sampai task 6 berdasarkan data BPJS.
                </div>
                <div class="menu-action">
                    Data Waktu Tunggu Per Poli
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
             <a href="{{route('registrasi-pasien.index')}}" class="menu-card">
                <div class="menu-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="menu-title"> Registrasi Pasien</div>
                <div class="menu-desc">
                    Monitoring Data Pasien yang sudah di registrasikan di SIMRS
                </div>
                <div class="menu-action">
                    Data Pasien Terdaftar
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>

        <div class="quick-search">
            <div>
                <div class="quick-title">Cari cepat kode booking</div>
                <div class="quick-desc">
                    Masukkan kode booking pasien untuk langsung membuka detail antrean dan task ID.
                </div>
            </div>

            <form class="quick-form" onsubmit="return openKodeBooking(event)">
                <input
                    type="text"
                    id="kodebooking"
                    class="quick-input"
                    placeholder="Contoh: 8e75d8a"
                    autocomplete="off"
                    required>

                <button class="quick-btn" type="submit">
                    Cari Detail
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openKodeBooking(event) {
        event.preventDefault();

        const input = document.getElementById('kodebooking');
        const kodebooking = input.value.trim();

        if (!kodebooking) {
            input.focus();
            return false;
        }

        const baseUrl = "{{ url('/bpjs-antrean/kodebooking') }}";
        window.location.href = baseUrl + '/' + encodeURIComponent(kodebooking);

        return false;
    }
</script>
@endsection