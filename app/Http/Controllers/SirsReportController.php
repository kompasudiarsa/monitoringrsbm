<?php
namespace App\Http\Controllers;
use App\Services\SirsReportClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// Jhon | 2026-11-25 19:51 WITA | TAMBAH: Adapter tampilan 26 laporan, whitelist dan periode bulanan sesuai API sumber.
class SirsReportController extends Controller
{
    const REPORTS = [
        'rl3-1' => 'Indikator Pelayanan',
        'rl3-2' => 'Pelayanan Rawat Inap',
        'rl3-3' => 'Pelayanan Rawat Darurat',
        'rl3-4' => 'Pengunjung',
        'rl3-5' => 'Kunjungan',
        'rl3-6' => 'Pelayanan Kebidanan',
        'rl3-7' => 'Neonatal, Bayi dan Balita',
        'rl3-8' => 'Pelayanan Laboratorium',
        'rl3-9' => 'Pelayanan Radiologi',
        'rl3-10' => 'Pelayanan Rujukan',
        'rl3-12' => 'Pelayanan Pembedahan',
        'rl3-14' => 'Pelayanan Khusus',
        'rl4-1' => 'Morbiditas Pasien Rawat Inap',
        'rl4-2' => '10 Besar Penyakit Rawat Inap',
        'rl4-3' => '10 Besar Kematian Rawat Inap',
        'rl5-1' => 'Morbiditas Pasien Rawat Jalan',
        'rl5-2' => '10 Besar Kasus Baru Rawat Jalan',
        'rl5-3' => '10 Besar Kunjungan Rawat Jalan',
        'rl3-11' => 'Pelayanan Gigi dan Mulut',
        'rl3-13' => 'Pelayanan Rehabilitasi Medik',
        'rl3-15' => 'Pelayanan Kesehatan Jiwa',
        'rl3-16' => 'Pelayanan Keluarga Berencana',
        'rl3-17' => 'Farmasi RS - Pengadaan Obat',
        'rl3-18' => 'Farmasi RS - Resep',
        'rl3-19' => 'Cara Bayar',
        'rl3-11-detail' => 'Detail Pelayanan Gigi dan Mulut',
    ];
    public function show(Request $request, SirsReportClient $client)
    {
        $report = $request->route('report');
        abort_unless(isset(self::REPORTS[$report]), 404);
        $period = $request->query('periode', now('Asia/Makassar')->format('Y-m'));
        $data = null; $error = null;
        if ($request->has('tampilkan')) {
            $validator = Validator::make(['periode'=>$period,'idkonten'=>$request->query('idkonten'),'tglAwal'=>$request->query('tglAwal'),'tglAkhir'=>$request->query('tglAkhir')], [
                'periode'=>'required|date_format:Y-m',
                'tglAwal'=>'nullable|date_format:Y-m-d', 'tglAkhir'=>'nullable|date_format:Y-m-d|after_or_equal:tglAwal',
                'idkonten'=>($report === 'rl3-11-detail' ? 'required' : 'nullable').'|integer|min:1',
            ]);
            if ($validator->fails()) $error = 'Periksa periode dan ID konten laporan.';
            else {
                [$year, $month] = explode('-', $period);
                if ((int) $year < 1900 || (int) $year > 2100) $error = 'Tahun laporan harus antara 1900–2100.';
                else {
                    $query = ['bulan'=>(int)$month,'tahun'=>(int)$year];
                    if ($report === 'rl3-11-detail') $query['idkonten'] = (int)$request->query('idkonten');
                    // Jhon | 2026-11-25 21:16 WITA | TAMBAH: periode khusus RL3.2 mengikuti filter rentang tanggal v1.
                    if ($report === 'rl3-2') foreach (['tglAwal','tglAkhir'] as $dateKey) if ($request->filled($dateKey)) $query[$dateKey] = $request->query($dateKey);
                    try { $data = $client->fetch($report, $query); }
                    catch (\DomainException $e) { $error = $e->getMessage(); }
                    catch (\Throwable $e) { $error = 'Terjadi kesalahan pemrosesan laporan. Silakan hubungi pengelola.'; }
                }
            }
        }
        return view('sirs.report', ['catalog'=>self::REPORTS, 'report'=>$report, 'title'=>self::REPORTS[$report],
            'period'=>$period, 'data'=>$data, 'error'=>$error]);
    }
}
