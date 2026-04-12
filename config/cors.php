<?php

$csv = static function (?string $value): array {
    if (! is_string($value) || trim($value) === '') {
        return [];
    }

    return array_values(array_filter(array_map(
        static fn (string $item): string => trim($item),
        explode(',', $value)
    )));
};

$localOrigins = [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
    'http://localhost:3000',
];

$frontendUrl = env('FRONTEND_URL');
$allowedOrigins = $csv(env('CORS_ALLOWED_ORIGINS'));
$allowedOriginPatterns = $csv(env('CORS_ALLOWED_ORIGIN_PATTERNS'));

if ($frontendUrl) {
    array_unshift($allowedOrigins, $frontendUrl);
}

if (env('APP_ENV', 'production') === 'local') {
    $allowedOrigins = [...$allowedOrigins, ...$localOrigins];
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_filter($allowedOrigins))),
    'allowed_origins_patterns' => array_values(array_unique(array_filter($allowedOriginPatterns))),
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
