<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Intercept the migration route before Laravel's HTTP Kernel boots (to bypass session database checking)
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($uri === '/vercel-migrate') {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    
    try {
        $kernel->call('migrate:fresh', ['--force' => true, '--seed' => true]);
        echo "<h3>Migration and Seeding Successful!</h3><br><pre>" . htmlspecialchars($kernel->output()) . "</pre>";
    } catch (\Throwable $e) {
        echo "<h3>Migration Failed:</h3><br>" . $e->getMessage();
    }
    exit;
}

try {
    // Forward Vercel requests to normal index.php
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>Fatal Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
