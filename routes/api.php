<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MetricsController;

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

Route::get('/health', [HealthController::class, 'index'])
    ->name('health');

Route::get('/metrics', [MetricsController::class, 'index'])
    ->name('metrics');
