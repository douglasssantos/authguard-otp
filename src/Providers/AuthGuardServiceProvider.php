<?php

namespace Larakeeps\AuthGuard\Providers;

use Illuminate\Support\ServiceProvider;
use Larakeeps\AuthGuard\Services\AuthGuardService;
use Larakeeps\AuthGuard\Services\AuthGuardServiceInterface;

class AuthGuardServiceProvider extends ServiceProvider
{
    const ROOT_PATH = __DIR__ . '/../..';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthGuardServiceInterface::class, AuthGuardService::class);
        $this->app->singleton('AuthGuard', AuthGuardServiceInterface::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(self::ROOT_PATH . '/database/migrations');
        $this->publishes([
            self::ROOT_PATH.'/database/migrations/2023_11_17_014118_create_auth_guards_table.php' => database_path('migrations/2023_11_17_014118_create_auth_guards_table.php'),
        ], 'authguard-otp-migrations');

    }
}
