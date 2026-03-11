<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?? $request->segment(1) ?? Session::get('locale') ?? config('app.locale');
        
        // Log for debugging
        \Illuminate\Support\Facades\Log::info("Path: " . $request->path() . " | Determined locale: " . $locale);

        try {
            $activeLanguages = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();
            if (empty($activeLanguages)) {
                $activeLanguages = [config('app.locale')];
            }

            if (in_array($locale, $activeLanguages)) {
                \Illuminate\Support\Facades\Log::info("Setting locale to: " . $locale);
                App::setLocale($locale);
                Session::put('locale', $locale);
                \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);
            } else {
                // Return default if locale is invalid
                App::setLocale(config('app.locale'));
                \Illuminate\Support\Facades\URL::defaults(['locale' => config('app.locale')]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Database connection failed in LocaleMiddleware: " . $e->getMessage());
            $default = config('app.locale');
            App::setLocale($default);
            \Illuminate\Support\Facades\URL::defaults(['locale' => $default]);
        }

        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
