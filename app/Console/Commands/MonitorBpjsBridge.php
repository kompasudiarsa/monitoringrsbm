<?php

namespace App\Console\Commands;

use App\Services\BpjsBridgeMonitoringService;
use Illuminate\Console\Command;

class MonitorBpjsBridge extends Command
{
    protected $signature = 'bpjs:monitor-bridge';

    protected $description = 'Memeriksa kesehatan bridging BPJS';

    public function handle(
        BpjsBridgeMonitoringService $monitoring
    ): int {
        $results = $monitoring->checkAll();

        foreach ($results as $result) {
            $this->line(sprintf(
                '[%s] %s | %s ms | %s',
                $result->status,
                $result->service,
                $result->response_time_ms ?? 0,
                $result->message ?? '-'
            ));
        }

        return self::SUCCESS;
    }
}