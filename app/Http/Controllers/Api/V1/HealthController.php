<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'app' => 'ok',
            'database' => 'failed',
            'redis' => 'failed',
        ];

        $status = 200;

        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Throwable $e) {
            $status = 503;
        }

        try {
            Redis::ping();
            $checks['redis'] = 'ok';
        } catch (\Throwable $e) {
            $status = 503;
        }

        return response()->json([
            'status' => $status === 200 ? 'ok' : 'failed',
            'checks' => $checks,
        ], $status);
    }
}