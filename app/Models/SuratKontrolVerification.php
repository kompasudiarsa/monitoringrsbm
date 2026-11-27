<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
// Jhon | 2026-11-27 11:06 WITA | TAMBAH: penyimpanan verifikasi lokal monitoring, tidak mengubah reservasi SIMRS/BPJS.
class SuratKontrolVerification extends Model
{
    protected $fillable = ['norec','permasalahan','keterangan','verified_by','verified_by_name'];
}
