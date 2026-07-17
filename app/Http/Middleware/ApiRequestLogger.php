<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiRequestLogger
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);
        $requestId = (string) Str::uuid();

        $request->attributes->set('request_id', $requestId);

        try {
            $response = $next($request);

            $response->headers->set('X-Request-Id', $requestId);

            $this->logRequest($request, $response->getStatusCode(), $startedAt, $requestId);

            return $response;
        } catch (Throwable $exception) {
            $status = match (true) {
                $exception instanceof ValidationException => 422,
                $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
                default => 500,
            };

            $this->logRequest($request, $status, $startedAt, $requestId, $exception);

            throw $exception;
        }
    }

    private function logRequest(
        Request $request,
        int $status,
        float $startedAt,
        string $requestId,
        ?Throwable $exception = null,
    ): void {
        Log::channel('api_requests')->info('API request handled', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => $request->route()?->getName(),
            'status' => $status,
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255),
            'error_type' => $exception ? $exception::class : null,
        ]);
    }
}
