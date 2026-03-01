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

        $headerId = $settings['global_header_id'] ?? null;
        $footerId = $settings['global_footer_id'] ?? null;

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
            'site_settings' => [
                'color_primary' => $settings['color_primary'] ?? '#000000',
                'color_secondary' => $settings['color_secondary'] ?? '#ffffff',
                'font_heading' => $settings['font_heading'] ?? 'Titillium Web',
                'font_body' => $settings['font_body'] ?? 'Titillium Web',
            ]
        ];
    }
}
