<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Jhon | 2026-11-27 11:06 WITA | TAMBAH: satu verifikasi terkini per norec reservasi, bukan nomor SEP/pasien.
class CreateSuratKontrolVerificationsTable extends Migration
{
    public function up()
    {
        Schema::create('surat_kontrol_verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('norec')->unique();
            $table->string('permasalahan', 30);
            $table->text('keterangan')->nullable();
            $table->string('verified_by', 150);
            $table->string('verified_by_name', 150)->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('surat_kontrol_verifications'); }
}
