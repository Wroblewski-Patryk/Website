<?php

namespace App\Support;

class ReservedRouteSegments
{
    /**
     * Reserved path segments that should never be used as public content slugs.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            'admin',
            'api',
            'build',
            'dashboard',
            'lang',
            'livewire',
            'login',
            'logout',
            'register',
            'robots',
            'robots.txt',
            'sitemap',
            'sitemap.xml',
            'storage',
            'up',
        ];
    }
}
