<?php

namespace App\Providers;

use App\Contracts\AiAnalyzerInterface;
use App\Services\Ai\OllamaAnalyzer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AiAnalyzerInterface::class, OllamaAnalyzer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('contact', function (Request $request) {
            $key = $request->ip();

            return [
                Limit::perMinute((int) env('CONTACT_RATE_LIMIT_PER_MINUTE', 3))->by($key),
                Limit::perHour((int) env('CONTACT_RATE_LIMIT_PER_HOUR', 20))->by($key),
            ];
        });
    }
}
