<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjsAntreanLogsTable extends Migration
{
    public function up()
    {
        Schema::create('bpjs_antrean_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('endpoint');
            $table->json('request_payload')->nullable();
            $table->integer('status_code')->nullable();
            $table->string('metadata_code')->nullable();
            $table->text('metadata_message')->nullable();
            $table->longText('raw_response')->nullable();
            $table->longText('decoded_response')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bpjs_antrean_logs');
    }
}
?>