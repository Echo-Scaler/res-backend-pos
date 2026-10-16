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

// Forward execution to standard Laravel entry point
require __DIR__.'/../public/index.php';
