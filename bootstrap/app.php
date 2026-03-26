<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
            \App\Http\Middleware\ResponseBudgetMiddleware::class,
            \App\Http\Middleware\EnsureApplicationInstalled::class,
        ], append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
        
        $middleware->alias([
            'locale' => \App\Http\Middleware\LocaleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'api_token_scope' => \App\Http\Middleware\EnsureScopedApiToken::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('auth.login', ['locale' => app()->getLocale()]));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard.index', ['locale' => app()->getLocale()]));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $settingKey = match ($status) {
                404 => 'page_404_id',
                500 => 'page_500_id',
                503 => 'page_503_id',
                default => null,
            };

            if (!$settingKey) {
                return null;
            }

            try {
                $pageId = \App\Models\Setting::query()->where('key', $settingKey)->value('value');
                if (!$pageId) {
                    return null;
                }

                $page = \App\Models\Page::with(['headerOverride', 'footerOverride'])->find($pageId);
                if (!$page) {
                    return null;
                }

                $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                $contentService = app(\App\Services\BlockContentService::class);

                $pageData = $page->toArray();
                $pageData['content'] = $contentService->resolveReferences($page->content ?: []);
                $pageData['title'] = $page->title;
                $pageData['slug'] = $page->slug;

                $header = $page->headerOverride ?? \App\Models\Template::where('type', 'header')->where('is_active', true)->where('is_default', true)->first();
                $footer = $page->footerOverride ?? \App\Models\Template::where('type', 'footer')->where('is_active', true)->where('is_default', true)->first();

                return Inertia::render('Public/Page', [
                    'page' => $pageData,
                    'header' => $header ? ['content' => $contentService->resolveReferences($header->content ?: [])] : null,
                    'footer' => $footer ? ['content' => $contentService->resolveReferences($footer->content ?: [])] : null,
                    'settings' => $settings,
                    'seo' => app(\App\Services\SeoService::class)->getMetaData($page),
                ])->toResponse($request)->setStatusCode($status);
            } catch (\Throwable) {
                return null;
            }
        });
    })->create();
