<?php

use App\Http\Middleware\ApiRequestLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->api(append: [
            ApiRequestLogger::class,
        ]);
    })    
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->respond(function (
            SymfonyResponse $response,
            Throwable $exception,
            Request $request,
        ): SymfonyResponse {
            if (! $request->is('api/*')) {
                return $response;
            }
    
            $status = $response->getStatusCode();
    
            $payload = [
                'message' => match ($status) {
                    400 => 'Bad request.',
                    401 => 'Unauthenticated.',
                    403 => 'Forbidden.',
                    404 => 'Resource not found.',
                    405 => 'Method not allowed.',
                    422 => 'Validation failed.',
                    429 => 'Too many requests.',
                    default => $status >= 500
                        ? 'Internal server error.'
                        : 'Request failed.',
                },
                'error' => [
                    'status' => $status,
                    'type' => class_basename($exception),
                ],
            ];
    
            if ($exception instanceof ValidationException) {
                $payload['errors'] = $exception->errors();
            }
    
            if (config('app.debug') && $status >= 500) {
                $payload['debug'] = [
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                ];
            }
    
            $json = response()->json($payload, $status);
    
            foreach (['Retry-After', 'X-RateLimit-Limit', 'X-RateLimit-Remaining'] as $header) {
                if ($response->headers->has($header)) {
                    $json->headers->set($header, $response->headers->get($header));
                }
            }
    
            return $json;
        });
    })->create();
