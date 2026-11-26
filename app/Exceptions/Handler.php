<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    // Jhon | 2026-11-26 22:02 WITA | TAMBAH: pulihkan navigasi ketika token sesi lama ditolak; jangan ulangi POST atau melewati CSRF.
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        if ($response->getStatusCode() !== 419) return $response;
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Sesi atau token halaman kedaluwarsa. Muat ulang halaman dan login kembali.'], 419);
        }
        return redirect()->route('login')
            ->withErrors(['session' => 'Sesi atau token halaman kedaluwarsa. Silakan login kembali.'])
            ->header('Cache-Control', 'no-store, private');
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        // Jhon | 2026-11-23 20:45 WITA | TAMBAH: rahasiakan juga nama field password upstream.
        'kataSandi',
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
