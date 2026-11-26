<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\MonitoringLoginService;
// Jhon | 2026-11-23 20:45 WITA | TAMBAH: login sesi, validasi lokal dan logout POST dengan CSRF.
class MonitoringAuthController extends Controller
{
    public function form(Request $request)
    {
        // Jhon | 2026-11-26 22:02 WITA | UBAH: samakan validitas login dengan middleware agar sesi idle tidak berputar login-dashboard.
        $identity = $request->session()->get('monitoring_auth');
        if (is_array($identity) && !empty($identity['id'])
            && ($identity['expires_at'] ?? 0) > time()
            && time() - ($identity['last_activity'] ?? 0) < 1800) return redirect('/');
        $request->session()->forget('monitoring_auth');
        // Jhon | 2026-11-23 20:48 WITA | TAMBAH: challenge acak lima menit, jawaban hanya di sesi server.
        $left = random_int(2, 15);
        $right = random_int(2, 15);
        $challengeId = bin2hex(random_bytes(16));
        $request->session()->put('monitoring_captcha', ['id' => $challengeId, 'answer' => $left + $right, 'expires' => time() + 300]);
        return response()->view('auth.monitoring-login', ['challengeId' => $challengeId, 'question' => "$left + $right"])->header('Cache-Control', 'no-store');
    }
    public function login(Request $request, MonitoringLoginService $service)
    {
        // Jhon | 2026-11-23 20:48 WITA | TAMBAH: konsumsi CAPTCHA sebelum validasi/login, termasuk jawaban salah dan replay.
        $challenge = $request->session()->pull('monitoring_captcha');
        $answer = (string) $request->input('captcha', '');
        if (!is_array($challenge) || ($challenge['expires'] ?? 0) <= time()
            || !hash_equals((string) ($challenge['id'] ?? ''), (string) $request->input('captcha_id', ''))
            || !preg_match('/^[0-9]{1,3}$/', $answer) || (int) $answer !== ($challenge['answer'] ?? null)) {
            return redirect()->route('login')->withErrors(['captcha' => 'CAPTCHA salah atau kedaluwarsa. Silakan jawab pertanyaan baru.'])->withInput($request->only('namaUser'));
        }
        $request->validate(['namaUser' => 'required|string|max:150', 'password' => 'required|string|max:512']);
        try { $identity = $service->authenticate($request->input('namaUser'), $request->input('password')); }
        catch (\Throwable $e) {
            return back()->withErrors(['namaUser' => 'Layanan login belum dapat dihubungi. Silakan coba lagi.'])->withInput($request->only('namaUser'));
        }
        if (!$identity) return back()->withErrors(['namaUser' => 'Login gagal. Periksa akun atau hubungi administrator.'])->withInput($request->only('namaUser'));
        $request->session()->regenerate();
        // Jhon | 2026-11-23 20:48 WITA | TAMBAH: awal waktu aktivitas sesi.
        $identity['last_activity'] = time();
        $request->session()->put('monitoring_auth', $identity);
        return redirect('/');
    }
    // Jhon | 2026-11-23 20:48 WITA | TAMBAH: heartbeat hanya dikirim setelah aktivitas pengguna.
    public function activity(Request $request)
    {
        return response()->json(['ok' => true]);
    }
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
