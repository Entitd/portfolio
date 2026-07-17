<?php

$allowedOrigins = array_values(array_filter(array_map(
    static fn (string $origin): string => trim($origin),
    explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost:8000,http://127.0.0.1:8000',
    )),
)));

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => $allowedOrigins,
    'allowed_origins_patterns' => [],
    'allowed_headers' => [
        'Accept',
        'Content-Type',
        'Origin',
        'X-Requested-With',
    ],
    'exposed_headers' => [
        'X-Request-Id',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'Retry-After',
    ],
    'max_age' => 600,
    'supports_credentials' => false,
];
