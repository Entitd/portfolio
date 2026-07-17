<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PortfolioController;

Route::get('/', [PortfolioController::class, 'show'])
    ->name('portfolio.show');

Route::get('/docs', function() {
    return view('swagger');
})->name('docs');
