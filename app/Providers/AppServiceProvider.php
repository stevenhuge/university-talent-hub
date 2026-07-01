<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isVercel = env('VERCEL') || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || isset($_SERVER['LAMBDA_TASK_ROOT']) || str_contains(getcwd(), '/var/task');

        if ($isVercel) {
            // Set storage path to /tmp for read-only Vercel environment
            config(['view.compiled' => '/tmp/storage/framework/views']);
            
            config(['session.files' => '/tmp/storage/framework/sessions']);
            config(['cache.stores.file.path' => '/tmp/storage/framework/cache']);
            
            // Ensure directories exist in /tmp
            if (!is_dir('/tmp/storage/framework/views')) {
                mkdir('/tmp/storage/framework/views', 0755, true);
            }
            if (!is_dir('/tmp/storage/framework/sessions')) {
                mkdir('/tmp/storage/framework/sessions', 0755, true);
            }
            if (!is_dir('/tmp/storage/framework/cache')) {
                mkdir('/tmp/storage/framework/cache', 0755, true);
            }
        }
    }
}
