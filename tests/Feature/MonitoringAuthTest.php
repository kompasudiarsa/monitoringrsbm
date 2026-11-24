<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
// Jhon | 2026-11-23 20:46 WITA | TAMBAH: fixture autentikasi, tamu AJAX, sesi, logout, respons tidak sah.
class MonitoringAuthTest extends TestCase
{
    // Jhon | 2026-11-23 20:49 WITA | TAMBAH: fixture CAPTCHA sekali pakai tanpa kredensial nyata.
    private function loginPost(array $input)
    {
        return $this->withSession(['monitoring_captcha' => ['id' => 'dummy-challenge', 'answer' => 7, 'expires' => time() + 300]])
            ->post('/login', array_merge($input, ['captcha_id' => 'dummy-challenge', 'captcha' => '7']));
    }
    public function test_wrong_captcha_is_consumed_without_upstream_request(): void
    {
        Http::fake();
        $this->withSession(['monitoring_captcha' => ['id' => 'dummy', 'answer' => 7, 'expires' => time() + 300]])
            ->post('/login', ['namaUser' => 'dummy', 'password' => 'dummy', 'captcha_id' => 'dummy', 'captcha' => '8'])
            ->assertSessionHasErrors('captcha')->assertSessionMissing('monitoring_captcha');
        Http::assertNothingSent();
    }
    public function test_idle_session_rejected_even_when_absolute_expiry_is_valid(): void
    {
        $this->withSession(['monitoring_auth' => ['id' => 123, 'expires_at' => time() + 3600, 'last_activity' => time() - 1801]])
            ->get('/')->assertRedirect('/login');
    }
    public function test_guests_cannot_open_monitoring_or_ajax(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->getJson('/bpjs-antrean/cek-surat-kontrol/ajax/no-kartu')->assertStatus(401);
        $this->get('/login')->assertOk()->assertSee('Login Monitoring BPJS');
    }
    public function test_successful_login_and_logout(): void
    {
        Http::fake(['*' => Http::response(['metaData' => ['code' => 200], 'response' => [
            'token' => 'dummy-only', 'expired' => now()->addHour()->toDateTimeString(),
            'data' => ['id' => 123, 'namaUser' => 'dummy-user'],
        ]], 200)]);
        $this->loginPost(['namaUser' => 'dummy-user', 'password' => 'dummy-password'])
            ->assertRedirect('/')->assertSessionHas('monitoring_auth.id', 123);
        Http::assertSent(function ($request) {
            return $request->url() === config('services.monitoring_auth.url')
                && $request['namaUser'] === 'dummy-user' && $request['kataSandi'] === 'dummy-password';
        });
        $this->get('/')->assertOk();
        $this->post('/logout')->assertRedirect('/login')->assertSessionMissing('monitoring_auth');
        $this->get('/')->assertRedirect('/login');
    }
    public function test_failure_does_not_establish_session(): void
    {
        Http::fake(['*' => Http::response(['metaData' => ['code' => 401]], 200)]);
        $this->loginPost(['namaUser' => 'dummy', 'password' => 'dummy-password'])
            ->assertSessionHasErrors('namaUser')->assertSessionMissing('monitoring_auth');
        $this->assertNull(session()->getOldInput('password'));
    }
    public function test_expired_session_is_rejected(): void
    {
        $this->withSession(['monitoring_auth' => ['id' => 123, 'expires_at' => time() - 1]])
            ->get('/')->assertRedirect('/login');
    }
}
