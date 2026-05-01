<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use App\Services\AdminSearch\Providers\PageSearchProvider;
use App\Services\AdminSearch\Providers\PostSearchProvider;
use App\Services\AdminSearch\Providers\ProjectSearchProvider;
use App\Services\AdminSearch\AdminSearchManager;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Policies\ContentPolicy;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AdminSearchManager::class, fn () => new AdminSearchManager([
            app(PageSearchProvider::class),
            app(PostSearchProvider::class),
            app(ProjectSearchProvider::class),
        ]));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Gate::policy(Page::class, ContentPolicy::class);
        Gate::policy(Post::class, ContentPolicy::class);
        Gate::policy(Project::class, ContentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Implicitly grant "admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        ResetPasswordNotification::createUrlUsing(function (object $notifiable, string $token) {
            return route('auth.password.reset', [
                'locale' => app()->getLocale(),
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        $this->registerSlowQueryProfiler();
    }

    protected function registerSlowQueryProfiler(): void
    {
        if (!config('performance.query_profiling.enabled', false)) {
            return;
        }

        $threshold = max(1, (int) config('performance.query_profiling.slow_query_threshold_ms', 75));

        DB::listen(function ($query) use ($threshold) {
            if ($query->time < $threshold) {
                return;
            }

            $requestContext = [];

            if (app()->runningInConsole()) {
                $requestContext['context'] = 'console';
            } else {
                $request = request();
                $requestContext = [
                    'context' => 'http',
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'request_id' => $request->headers->get('X-Request-Id'),
                ];
            }

            Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time,
                'connection' => $query->connectionName,
                ...$requestContext,
            ]);
        });
    }
}
