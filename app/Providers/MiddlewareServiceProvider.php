<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use Illuminate\Contracts\Http\Kernel;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('admin', function ($app) {
            return new AdminMiddleware();
        });

        $this->app->singleton('customer', function ($app) {
            return new CustomerMiddleware();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];

        // Register middleware aliases
        $router->aliasMiddleware('admin', AdminMiddleware::class);
        $router->aliasMiddleware('customer', CustomerMiddleware::class);
    }
}
