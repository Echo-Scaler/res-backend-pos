<?php

declare(strict_types=1);

/**
 * Vercel Serverless Entrypoint for Laravel 11
 *
 * In Vercel's serverless AWS Lambda environment, only `/tmp` is writable.
 * We ensure compiled blade views and session directories exist in `/tmp`
 * before delegating the request to Laravel's standard `public/index.php`.
 */
$serverlessDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($serverlessDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy pre-seeded SQLite database to writable /tmp directory if not using remote DB_HOST
$tmpDb = '/tmp/database.sqlite';
$bundledDb = __DIR__.'/../database/database.sqlite';

if (! file_exists($tmpDb) && file_exists($bundledDb)) {
    @copy($bundledDb, $tmpDb);
}

// Auto-inject default environment variables so deployment works without manual Vercel UI configuration
$defaultEnvs = [
    'APP_NAME' => 'Restaurant POS',
    'APP_ENV' => 'production',
    'APP_KEY' => 'base64:qR/4eW/gtmnqdjqj9xnIEXrGB4K9aWOD63ss0qwMZ5c=',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'https://res-backend-pos-khaki.vercel.app',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
];

foreach ($defaultEnvs as $key => $val) {
    if (getenv($key) === false || getenv($key) === '') {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

// If DB_HOST is not provided, fall back to SQLite in /tmp
if (getenv('DB_HOST') === false || getenv('DB_HOST') === '') {
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=/tmp/database.sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = '/tmp/database.sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = '/tmp/database.sqlite';
}

// Enforce HTTPS server variables when running behind Vercel edge proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = '443';
}

// Forward execution to standard Laravel entry point
require __DIR__.'/../public/index.php';
