<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Models\SuratKontrolVerification;
// Jhon | 2026-11-27 11:06 WITA | TAMBAH: token terikat sesi dan norec mencegah pengguna mengganti identitas reservasi lewat payload.
class SuratKontrolVerificationController extends Controller
{
    public function save(Request $request)
    {
        // Jhon | 2026-11-27 11:21 WITA | UBAH: terima kategori Lainnya dan Kontrol Internal sesuai pilihan form.
        $input = $request->validate(['reservation_token'=>'required|string|max:4096','permasalahan'=>'required|in:rujukan,sep,jadwal_poli,lainnya,kontrol_internal','keterangan'=>'nullable|string|max:2000']);
        try { $grant = json_decode(Crypt::decryptString($input['reservation_token']), true); }
        catch (\Throwable $e) { return response()->json(['message'=>'Identitas reservasi tidak valid. Muat ulang daftar.'],422); }
        if (!is_array($grant) || ($grant['expires'] ?? 0) < time()
            || !hash_equals((string) ($grant['session'] ?? ''), hash('sha256', $request->session()->getId()))
            || Validator::make(['norec'=>$grant['norec'] ?? null], ['norec'=>'required|uuid'])->fails()) {
            return response()->json(['message'=>'Identitas reservasi kedaluwarsa atau tidak valid. Muat ulang daftar.'],422);
        }
        try {
            SuratKontrolVerification::updateOrCreate(['norec'=>$grant['norec']], [
                'permasalahan'=>$input['permasalahan'], 'keterangan'=>$input['keterangan'] ?? null,
                'verified_by'=>(string) $request->session()->get('monitoring_auth.id'),
                'verified_by_name'=>(string) $request->session()->get('monitoring_auth.name'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message'=>'Verifikasi belum tersimpan. Periksa kesiapan tabel dan koneksi database monitoring.'],503);
        }
        // Jhon | 2026-11-27 11:14 WITA | UBAH: status sukses hanya dikirim sesudah penyimpanan berhasil.
        return response()->json(['message'=>'Verifikasi berhasil disimpan.','verified'=>true,'norec'=>$grant['norec']])->header('Cache-Control','private, no-store');
    }
}
