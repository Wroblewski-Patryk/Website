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
            'auth' => [
                'user' => $request->user() ? $request->user()->only('id', 'name', 'email') : null,
            ],
            'header' => $header ? $header->content : null,
            'footer' => $footer ? $footer->content : null,
            'locale' => app()->getLocale(),
            'all_projects' => \App\Models\Project::orderBy('order')->get(),
            'theme_config' => isset($settings['theme_config']) ? 
            (is_array($settings['theme_config']) ? $settings['theme_config'] : json_decode($settings['theme_config'], true))
            : null,
            'seo_settings' => [
                'site_name' => $settings['site_name'] ?? ['pl' => config('app.name'), 'en' => config('app.name')],
                'title_separator' => $settings['title_separator'] ?? ' - ',
                'title_order' => $settings['title_order'] ?? 'brand_first',
            ],
            'admin_seo' => $this->getAdminSeoContext($request),
        ];
    }

    /**
     * Helper to get admin SEO context from route.
     */
    protected function getAdminSeoContext(Request $request): array
    {
        $routeName = $request->route() ? $request->route()->getName() : '';
        $parts = explode('.', (string)$routeName);

        $context = [
            'module' => null,
            'action' => null,
            'module_label' => null,
            'action_label' => null,
        ];

        if (count($parts) >= 2 && $parts[0] === 'dashboard') {
            $context['module'] = $parts[1];
            $context['action'] = $parts[2] ?? 'index';

            $seoService = app(\App\Services\SeoService::class);
            $context['module_label'] = $seoService->getModuleLabel($context['module']);

            $actionMap = [
                'edit' => 'Edycja',
                'create' => 'Nowy',
                'index' => 'Lista',
                'show' => 'Podgląd',
            ];
            $context['action_label'] = $actionMap[strtolower($context['action'])] ?? \Illuminate\Support\Str::ucfirst($context['action']);
        }

        return $context;
    }
}
