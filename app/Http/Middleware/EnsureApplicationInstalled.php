<?php

namespace App\Http\Middleware;

use App\Support\InstallationState;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationInstalled
{
    public function __construct(private readonly InstallationState $installationState)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = $this->installationState->isInstalled();

        if ($isInstalled && $request->routeIs('install.*')) {
            return redirect()->route('auth.login', ['locale' => app()->getLocale()]);
        }

        if ($isInstalled) {
            return $next($request);
        }

        if ($request->routeIs('install.*')) {
            return $next($request);
        }

        return redirect()->route('install.index', ['locale' => app()->getLocale()]);
    }
}
