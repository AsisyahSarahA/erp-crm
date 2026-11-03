<?php

declare(strict_types=1);

namespace Astacode\Kernel;

use Astacode\Kernel\Http\Controllers\HealthController;
use Astacode\Kernel\Http\Middleware\CorrelationId;
use Astacode\Kernel\Http\Middleware\IdempotencyMiddleware;
use Astacode\Kernel\Http\Middleware\ResolveInternalUser;
use Astacode\Kernel\Http\Middleware\VerifyServiceToken;
use Astacode\Kernel\Services\AuditClient;
use Astacode\Kernel\Services\NumberingClient;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * ServiceProvider AstacodeKernelServiceProvider
 * Registers ASTACODE ERP shared kernel bindings, migrations, and middleware.
 */
class AstacodeKernelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NumberingClient::class, fn () => new NumberingClient());
        $this->app->singleton(AuditClient::class, fn () => new AuditClient());
    }

    public function boot(Router $router): void
    {
        // Register middleware aliases
        $router->aliasMiddleware('service.token', VerifyServiceToken::class);
        $router->aliasMiddleware('correlation.id', CorrelationId::class);
        $router->aliasMiddleware('internal.user', ResolveInternalUser::class);
        $router->aliasMiddleware('idempotent', IdempotencyMiddleware::class);

        // Load migrations from kernel
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Automatically register internal health route
        Route::get('/internal/health', [HealthController::class, 'health'])
            ->name('internal.health');
    }
}

