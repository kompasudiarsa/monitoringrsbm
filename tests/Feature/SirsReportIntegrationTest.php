<?php
namespace Tests\Feature;
use App\Http\Controllers\SirsReportController;
use App\Services\SirsReportClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
// Jhon | 2026-11-25 19:53 WITA | TAMBAH: Uji kontrak GET bertanda tangan, menu terproteksi dan render fixture, tanpa pasien nyata.
class SirsReportIntegrationTest extends TestCase
{
    private function identity(): array
    {
        return ['monitoring_auth'=>['id'=>'fixture-user','expires_at'=>time()+1800,'last_activity'=>time()]];
    }
    private function configureApi(): void
    {
        config(['services.api_simrs.reports_base_url'=>'https://simrs.test','services.api_simrs.base_url'=>'https://simrs.test','services.api_simrs.token'=>'fixture-token','services.api_simrs.secret'=>'fixture-secret']);
    }
    public function test_all_catalog_routes_exist_and_guest_cannot_open_them()
    {
        foreach (SirsReportController::REPORTS as $code=>$name) {
            $this->get(route('sirs.'.$code))->assertRedirect(route('login'));
        }
    }
    public function test_initial_page_does_not_fetch_and_report_renders_fixture()
    {
        $this->configureApi();
        Http::fake(['*'=>Http::response(['metaData'=>['code'=>200],'response'=>['data'=>[['jenis_pelayanan'=>'Fixture ICU','bor'=>12.5,'detail'=>['nilai'=>7]]]]])]);
        $this->withSession($this->identity())->get(route('sirs.rl3-1'))->assertOk()->assertSee('Tampilkan Laporan');
        Http::assertNothingSent();
        $this->withSession($this->identity())->get(route('sirs.rl3-1',['periode'=>'2026-11','tampilkan'=>1]))
            ->assertOk()->assertSee('Fixture ICU')->assertSee('12.5')->assertSee('Lihat rincian');
        Http::assertSent(function ($request) {
            $timestamp=$request->header('X-Timestamp')[0];
            $uri=parse_url($request->url(), PHP_URL_PATH).'?'.parse_url($request->url(), PHP_URL_QUERY);
            parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);
            if (($query['bulan'] ?? null) != 11 || ($query['tahun'] ?? null) != 2026 || empty($query['_request_id'])) return false;
            $signature=hash_hmac('sha256',implode("\n",['GET',$uri,$timestamp,hash('sha256','')]),'fixture-secret');
            return $request->url()==='https://simrs.test'.$uri && $request->method()==='GET'
                && hash_equals($signature,$request->header('X-Signature')[0]);
        });
    }
    public function test_invalid_period_does_not_call_api()
    {
        Http::fake();
        $this->withSession($this->identity())->get(route('sirs.rl3-1',['periode'=>'invalid','tampilkan'=>1]))
            ->assertOk()->assertSee('Periksa periode');
        Http::assertNothingSent();
    }
    public function test_empty_and_failure_are_distinct_and_values_are_escaped()
    {
        $this->configureApi();
        Http::fake(['*'=>Http::response(['metaData'=>['code'=>200],'response'=>['data'=>[]]])]);
        $this->withSession($this->identity())->get(route('sirs.rl3-1',['periode'=>'2026-11','tampilkan'=>1]))
            ->assertOk()->assertSee('Tidak ada data pada bagian ini');
        Http::fake(['*'=>Http::response(['message'=>'internal detail'],503)]);
        $this->withSession($this->identity())->get(route('sirs.rl3-1',['periode'=>'2026-11','tampilkan'=>1]))
            ->assertOk()->assertSee('HTTP 503')->assertDontSee('Tidak ada data pada bagian ini')->assertDontSee('internal detail');
        Http::fake(['*'=>Http::response(['metaData'=>['code'=>200],'response'=>['data'=>[['label'=>'<script>alert(1)</script>']]]])]);
        $this->withSession($this->identity())->get(route('sirs.rl3-1',['periode'=>'2026-11','tampilkan'=>1]))
            ->assertOk()->assertSee('&lt;script&gt;',false)->assertDontSee('<script>alert(1)</script>',false);
    }
    // Jhon | 2026-11-25 21:01 WITA | TAMBAH: Prefix konfigurasi berbeda tetap mencapai endpoint yang sama, nonce berbeda setiap request.
    public function test_api_prefix_is_normalized_and_repeated_requests_have_unique_nonce()
    {
        $this->configureApi();
        Http::fake(['*'=>Http::response(['metaData'=>['code'=>200],'response'=>['data'=>[]]])]);
        $client = new SirsReportClient();
        foreach (['https://simrs.test','https://simrs.test/api','https://simrs.test/api/service'] as $base) {
            config(['services.api_simrs.reports_base_url'=>$base]);
            $client->fetch('rl3-1',['bulan'=>11,'tahun'=>2026]);
        }
        $nonces = [];
        foreach (Http::recorded() as $record) {
            $this->assertSame('/api/service/sirs/rl/rl3-1', parse_url($record[0]->url(), PHP_URL_PATH));
            parse_str(parse_url($record[0]->url(), PHP_URL_QUERY), $query);
            $nonces[]=$query['_request_id'];
        }
        $this->assertCount(3,array_unique($nonces));
    }
}
