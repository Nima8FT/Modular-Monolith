<?php

namespace Features\Auth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerJwt();
    }

    public function boot(): void
    {
        $this->registerAuthGuard();
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__.'/../../routes/api.php');
    }

    protected function registerJwt(): void
    {
        $this->app->register(
            \Tymon\JWTAuth\Providers\LaravelServiceProvider::class
        );
    }

    protected function registerAuthGuard(): void
    {
        config([
            'auth.guards.jwt' => [
                'driver' => 'jwt',
                'provider' => 'users',
            ],
        ]);
    }
}
