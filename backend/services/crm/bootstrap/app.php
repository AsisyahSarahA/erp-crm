<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Override App\ and Database\Seeders\ PSR-4 namespace to point to this service's local directory
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'App\\')) {
        $relativeClass = substr($class, 4);
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    if (str_starts_with($class, 'Database\\Seeders\\')) {
        $relativeClass = substr($class, 17);
        $file = __DIR__ . '/../database/seeders/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    if (str_starts_with($class, 'Database\\Factories\\')) {
        $relativeClass = substr($class, 19);
        $file = __DIR__ . '/../database/factories/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    return false;
}, true, true);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            if (file_exists(__DIR__.'/../routes/internal.php')) {
                Route::prefix('internal')
                    ->group(__DIR__.'/../routes/internal.php');
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Astacode\Kernel\Http\Middleware\CorrelationId::class);
        $middleware->append(\Astacode\Kernel\Http\Middleware\ResolveInternalUser::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->is('internal/*') || $request->expectsJson(),
        );
    })->create();
