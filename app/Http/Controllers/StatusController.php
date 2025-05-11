<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatusController extends Controller
{
    public function index(): JsonResponse
    {
        $dbStatus = $this->checkDatabaseConnection();
        $lastImport = ImportHistory::latest('imported_at')->first();
        $lastCron = $lastImport
            ? Carbon::parse($lastImport->imported_at)->format('d/m/Y H:i:s')
            : 'Nunca executado';

        $uptime = now()->diffForHumans(config('app.start_time'), true);
        $memory = memory_get_usage(true) / 1024 / 1024;

        return response()->json([
            'status' => 'OK',
            'database_connection' => $dbStatus ? 'OK' : 'FAIL',
            'last_cron_run' => $lastCron,
            'uptime' => $uptime,
            'memory_usage_mb' => round($memory, 2),
        ]);
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
