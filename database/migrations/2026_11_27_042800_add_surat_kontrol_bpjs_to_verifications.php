<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Jhon | 2026-11-27 12:28 WITA | TAMBAH: nullable menjaga data lama tetap belum dijawab, tidak dipaksa Tidak.
class AddSuratKontrolBpjsToVerifications extends Migration
{
    public function up()
    {
        Schema::table('surat_kontrol_verifications', function (Blueprint $table) {
            $table->string('surat_kontrol_bpjs', 5)->nullable();
        });
    }
    public function down()
    {
        Schema::table('surat_kontrol_verifications', function (Blueprint $table) {
            $table->dropColumn('surat_kontrol_bpjs');
        });
    }
}
