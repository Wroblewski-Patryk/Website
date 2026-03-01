<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        $headerId = $settings['default_header_id'] ?? null;
        $footerId = $settings['default_footer_id'] ?? null;

        $header = $headerId
            ?\App\Models\Template::find($headerId)
            : \App\Models\Template::where('type', 'header')->where('is_active', true)->first();

        $footer = $footerId
            ?\App\Models\Template::find($footerId)
            : \App\Models\Template::where('type', 'footer')->where('is_active', true)->first();

        return [
            ...parent::share($request),
            'header' => $header ? $header->content : null,
            'footer' => $footer ? $footer->content : null,
            'locale' => app()->getLocale(),
            'all_projects' => \App\Models\Project::orderBy('order')->get(),
            'site_settings' => [
                'color_primary' => $settings['brand_primary_color'] ?? '#000000',
                'color_secondary' => $settings['brand_secondary_color'] ?? '#ffffff',
                'font_heading' => $settings['brand_font_family'] ?? 'Titillium Web',
                'font_body' => $settings['brand_font_family'] ?? 'Titillium Web',
            ]
        ];
    }
}
