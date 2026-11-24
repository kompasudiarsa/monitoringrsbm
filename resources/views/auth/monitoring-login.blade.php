{{-- Jhon | 2026-11-23 20:48 WITA | UBAH: login responsif dengan CAPTCHA lokal dan tampilan biru-hijau. --}}
{{-- Jhon | 2026-11-23 20:51 WITA | UBAH: judul tab login Dashboard Monitoring. --}}
@extends('layouts.app', ['title' => 'Dashboard Monitoring'])
@section('content')
<style>
.login-shell{min-height:100vh;display:grid;place-items:center;padding:24px;background:radial-gradient(ellipse at top left,#bfdbfe,transparent 55%),linear-gradient(135deg,#eff6ff,#ecfdf5)}
.login-card{width:100%;max-width:440px;background:#fff;padding:36px;border-radius:24px;box-shadow:0 24px 70px #0f172a18;border:1px solid #ffffff}
.login-mark{width:56px;height:56px;display:grid;place-items:center;border-radius:16px;background:#dcfce7;color:#166534;font-size:28px;font-weight:700}
.login-card h1{font-size:26px;margin-bottom:8px}.login-card p{color:#64748b;line-height:1.5}.login-card label{display:block;font-weight:600;font-size:14px;margin-top:18px}
.login-card input{width:100%;padding:13px;margin-top:8px;border:1px solid #cbd5e1;border-radius:10px;font-size:16px}.login-card input:focus{outline:3px solid #bbf7d0;border-color:#16a34a}
.login-captcha{background:#f1f5f9;padding:14px;border-radius:12px;margin-top:20px}.login-captcha label{margin:0}
.login-submit{width:100%;padding:14px;background:linear-gradient(120deg,#15803d,#0f766e);color:#fff;border:0;border-radius:10px;margin-top:22px;font-size:16px;font-weight:700;cursor:pointer}
.login-error{color:#991b1b;background:#fef2f2;padding:12px;border-radius:10px}.login-note{font-size:12px;text-align:center;margin-bottom:0}@media(max-width:480px){.login-shell{padding:16px}.login-card{padding:24px}}
</style>
<div class="login-shell"><main class="login-card">
    <div class="login-mark" aria-hidden="true">+</div>
    {{-- Jhon | 2026-11-23 20:51 WITA | UBAH: judul utama halaman login sesuai permintaan. --}}
    <h1>Dashboard Monitoring</h1><p>Selamat datang. Masuk menggunakan akun SIMRS Anda.</p>
    @if ($errors->any())<div role="alert" class="login-error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('monitoring.login') }}">
        @csrf
        <label for="namaUser">Nama pengguna</label>
        <input id="namaUser" name="namaUser" value="{{ old('namaUser') }}" autocomplete="username" required maxlength="150">
        <label for="password">Kata sandi</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required maxlength="512">
        <div class="login-captcha">
            <input type="hidden" name="captcha_id" value="{{ $challengeId }}">
            <label for="captcha">Verifikasi: berapa {{ $question }}?</label>
            <input id="captcha" name="captcha" inputmode="numeric" autocomplete="off" required pattern="[0-9]{1,3}" maxlength="3" placeholder="Jawaban">
        </div>
        <button type="submit" class="login-submit">Masuk</button>
    </form>
    <p class="login-note">Sesi berakhir setelah 30 menit tanpa aktivitas.</p>
</main></div>
@endsection
