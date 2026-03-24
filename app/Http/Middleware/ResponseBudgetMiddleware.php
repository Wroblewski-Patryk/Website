<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResponseBudgetMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('performance.response_budget.enabled', false)) {
            return $next($request);
        }

        $start = hrtime(true);
        $response = $next($request);
        $elapsedMs = (hrtime(true) - $start) / 1_000_000;
        $thresholdMs = max(0, (int) config('performance.response_budget.threshold_ms', 800));

        $response->headers->set('X-Response-Time-Ms', number_format($elapsedMs, 2, '.', ''));

        if ($elapsedMs > $thresholdMs) {
            Log::warning('Route response budget exceeded', [
                'request_id' => $request->attributes->get('request_id'),
                'method' => $request->method(),
                'path' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'elapsed_ms' => round($elapsedMs, 2),
                'threshold_ms' => $thresholdMs,
            ]);
        }

        return $response;
    }
}
