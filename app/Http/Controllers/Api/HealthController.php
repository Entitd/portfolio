<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        try{
            DB::connection()->getPdo();
            return response()->json([
                'status' => 'ok',
                'services' => [
                    'application' => 'ok',
                    'database' => 'ok',
                ],
                'timestamp' => now()->toIso8601String(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'services' => [
                    'application' => 'ok',
                    'database' => $th->getMessage(),
                ],
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }
}
