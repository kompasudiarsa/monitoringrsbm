<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjsBridgeMonitoringsTable extends Migration
{
    public function up()
    {
        Schema::create('bpjs_bridge_monitorings', function (Blueprint $table) {
            $table->id();

            $table->string('service', 100);
            $table->string('endpoint', 255)->nullable();

            // UP, DEGRADED, DOWN
            $table->string('status', 20)->default('DOWN');

            $table->string('bpjs_code', 20)->nullable();
            $table->text('message')->nullable();

            $table->unsignedInteger('response_time_ms')->nullable();
            $table->unsignedInteger('payload_count')->nullable();

            $table->longText('error_detail')->nullable();
            $table->timestamp('checked_at')->nullable();

            $table->timestamps();

            $table->index(['service', 'checked_at']);
            $table->index(['status', 'checked_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bpjs_bridge_monitorings');
    }
}