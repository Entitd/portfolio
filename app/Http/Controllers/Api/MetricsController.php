<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class MetricsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'metrics' => [
                    'total' => Contact::query()->count(),
                ]
            ],
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }
}
