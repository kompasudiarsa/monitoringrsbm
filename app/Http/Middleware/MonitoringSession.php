<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
// Jhon | 2026-11-23 20:45 WITA | TAMBAH: lindungi seluruh route web termasuk AJAX, tanpa membuka endpoint diagnostik.
class MonitoringSession
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('login', 'monitoring.login', 'monitoring.logout')) return $next($request);
        $identity = $request->session()->get('monitoring_auth');
        if (!is_array($identity) || empty($identity['id']) || ($identity['expires_at'] ?? 0) <= time()
            || time() - ($identity['last_activity'] ?? 0) >= 1800) {
            $request->session()->forget('monitoring_auth');
            if ($request->expectsJson() || $request->ajax()) return response()->json(['message' => 'Silakan login kembali.'], 401);
            return redirect()->route('login');
        }
        // Jhon | 2026-11-23 20:48 WITA | TAMBAH: AJAX/polling tidak memperpanjang sesi tanpa aktivitas manusia.
        if ($request->routeIs('monitoring.activity') || $request->header('Sec-Fetch-Mode') === 'navigate') {
            $request->session()->put('monitoring_auth.last_activity', time());
        }
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        return $response;
    }
}
