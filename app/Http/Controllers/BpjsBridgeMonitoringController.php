<?php

namespace App\Http\Controllers;

use App\Models\BpjsBridgeMonitoring;
use App\Services\BpjsBridgeMonitoringService;
use Illuminate\Http\Request;

class BpjsBridgeMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $latestIds = BpjsBridgeMonitoring::query()
            ->selectRaw('MAX(id) AS id')
            ->groupBy('service')
            ->pluck('id');

        $latest = BpjsBridgeMonitoring::query()
            ->whereIn('id', $latestIds)
            ->orderBy('service')
            ->get();

        if ($latest->contains('status', 'DOWN')) {
            $overallStatus = 'DOWN';
        } elseif ($latest->contains('status', 'DEGRADED')) {
            $overallStatus = 'DEGRADED';
        } elseif ($latest->isNotEmpty()) {
            $overallStatus = 'UP';
        } else {
            $overallStatus = 'UNKNOWN';
        }

        $history = BpjsBridgeMonitoring::query()
            ->when(
                $request->filled('service'),
                fn ($query) => $query->where(
                    'service',
                    $request->service
                )
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->status
                )
            )
            ->orderByDesc('checked_at')
            ->paginate(50)
            ->withQueryString();

        $services = BpjsBridgeMonitoring::query()
            ->select('service')
            ->distinct()
            ->orderBy('service')
            ->pluck('service');

        return view(
            'bpjs-monitoring.index',
            compact(
                'latest',
                'history',
                'services',
                'overallStatus'
            )
        );
    }

    public function checkNow(
        BpjsBridgeMonitoringService $monitoring
    ) {
        $monitoring->checkAll();

        return redirect()
            ->route('bpjs-monitoring.index')
            ->with(
                'success',
                'Pengecekan bridging BPJS berhasil dijalankan.'
            );
    }
}