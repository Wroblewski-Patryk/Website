<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Policies\ContentPolicy;

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
        Gate::policy(Page::class, ContentPolicy::class);
        Gate::policy(Post::class, ContentPolicy::class);
        Gate::policy(Project::class, ContentPolicy::class);

        // Implicitly grant "admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
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
