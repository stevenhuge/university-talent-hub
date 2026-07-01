<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

$isVercel = env('VERCEL') || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || isset($_SERVER['LAMBDA_TASK_ROOT']) || str_contains(getcwd() ?: '', '/var/task');

if ($isVercel) {
    $tmpStorage = '/tmp/storage';
    $tmpBootstrapCache = '/tmp/bootstrap/cache';

    if (!is_dir($tmpStorage)) {
        mkdir($tmpStorage . '/app/public', 0777, true);
        mkdir($tmpStorage . '/framework/cache/data', 0777, true);
        mkdir($tmpStorage . '/framework/sessions', 0777, true);
        mkdir($tmpStorage . '/framework/views', 0777, true);
        mkdir($tmpStorage . '/logs', 0777, true);
    }
    
    if (!is_dir($tmpBootstrapCache)) {
        mkdir($tmpBootstrapCache, 0777, true);
    }

    $app->useStoragePath($tmpStorage);
    $app->useBootstrapPath('/tmp/bootstrap');
}

return $app;
