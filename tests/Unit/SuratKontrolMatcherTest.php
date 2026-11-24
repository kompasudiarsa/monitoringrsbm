<?php
namespace Tests\Unit;
use App\Services\SuratKontrolMatcher;
use PHPUnit\Framework\TestCase;
// Jhon | 2026-11-23 19:42 WITA | UBAH: fixture kriteria SEP/kartu dan poli, tanggal diseleksi controller.
class SuratKontrolMatcherTest extends TestCase
{
    public function test_identity_and_poli_without_number_or_doctor(): void
    {
        $m = new SuratKontrolMatcher();
        $s = ['nobpjs' => '000123', 'nosep' => 'SEP-A', 'poli' => 'PARU', 'nosurat' => 'A'];
        $b = ['noKartu' => '000123', 'noSepAsalKontrol' => 'SEP-B', 'namaPoliTujuan' => 'PARU', 'noSuratKontrol' => 'B'];
        $this->assertSame('SESUAI', $m->match($s, [$b])['status']);
        $b['noKartu'] = '999999';
        $b['noSepAsalKontrol'] = 'SEP-A';
        $this->assertSame('SESUAI', $m->match($s, [$b])['status']);
        $b['namaPoliTujuan'] = 'MATA';
        $this->assertSame('TIDAK_SESUAI', $m->match($s, [$b])['status']);
        $this->assertFalse($m->identityMatches([], []));
        $this->assertSame('TIDAK_DITEMUKAN_BPJS', $m->match($s, [])['status']);
        $this->assertSame('BPJS_GAGAL', $m->match($s, [], false)['status']);
        $b['namaPoliTujuan'] = 'PARU';
        $other = array_merge($b, ['noSuratKontrol' => 'C']);
        $this->assertSame('AMBIGU', $m->match($s, [$b, $other])['status']);
    }
}
