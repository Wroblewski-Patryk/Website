<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Grupa Auth (np. /pl/login)
            Route::middleware(['web', 'locale'])
                ->prefix('{locale}')
                ->group(base_path('routes/auth.php'));

            // Grupa Admin (np. /pl/admin)
            Route::middleware(['web', 'auth', 'locale'])
                ->prefix('{locale}/admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // Grupa Public (Catch-all dla podstron serwisu)
            Route::middleware(['web', 'locale'])
                ->prefix('{locale}')
                ->where(['locale' => '[a-z]{2}'])
                ->group(base_path('routes/public.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(prepend: [
            \App\Http\Middleware\RequestCorrelationIdMiddleware::class,
        ], append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        
        $middleware->alias([
            'locale' => \App\Http\Middleware\LocaleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('auth.login', ['locale' => app()->getLocale()]));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard.index', ['locale' => app()->getLocale()]));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
