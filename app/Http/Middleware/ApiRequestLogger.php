<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiRequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);
        $requestId = $request->header('X-Request-Id', (string) Str::uuid());

        try {
            $response = $next($request);

            $response->headers->set('X-Request-Id', $requestId);

            $this->logRequest($request, $response->getStatusCode(), $startedAt, $requestId);

            return $response;
        } catch (Throwable $exception) {
            $this->logRequest($request, 500, $startedAt, $requestId, $exception);

            throw $exception;
        }
    }

    private function logRequest(
        Request $request,
        int $status,
        float $startedAt,
        string $requestId,
        ?Throwable $exception = null,
    ): void 
    {
        Log::channel('api_requests')->info('API request handled', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => $request->route()?->getName(),
            'status' => $status,
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $this->safePayload($request),
            'error' => $exception?->getMessage(),
        ]);
    }

    private function safePayload(Request $request): array
    {
        return collect($request->except([
            'password',
            'password_confirmation',
            'token',
            'api_key',
            '_token',
        ]))->map(function (mixed $value) {
            return is_string($value) ? Str::limit($value, 500) : $value;
        })->all();
    }
}
