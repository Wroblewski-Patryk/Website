<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Translation;

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
        $settings = [];
        $header = null;
        $footer = null;
        $languages = collect();
        $allProjects = collect();
        $themeConfig = null;

        try {
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

            $headerId = $settings['default_header_id'] ?? null;
            $footerId = $settings['default_footer_id'] ?? null;

            $header = $headerId
                ?\App\Models\Template::find($headerId)
                : \App\Models\Template::where('type', 'header')->where('is_active', true)->first();

            $footer = $footerId
                ?\App\Models\Template::find($footerId)
                : \App\Models\Template::where('type', 'footer')->where('is_active', true)->first();

            $languages = \App\Models\Language::where('is_active', true)->orderBy('is_default', 'desc')->get();
            $allProjects = \App\Models\Project::orderBy('order')->get();
            
            $themeConfig = isset($settings['theme_config']) ? 
                (is_array($settings['theme_config']) ? $settings['theme_config'] : json_decode($settings['theme_config'], true))
                : null;

            $translations = Translation::all()->reduce(function ($carry, $translation) {
                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale', 'en');
                $text = $translation->getTranslation('text', $locale, false) ?: $translation->getTranslation('text', $fallback, false);
                $carry[$translation->group . '.' . $translation->key] = $text;
                return $carry;
            }, []);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Database connection failed in HandleInertiaRequests: " . $e->getMessage());
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->only('id', 'name', 'email') : null,
            ],
            'header' => $header ? $header->content : null,
            'footer' => $footer ? $footer->content : null,
            'locale' => app()->getLocale(),
            'languages' => $languages,
            'all_projects' => $allProjects,
            'theme_config' => $themeConfig,
            'translations' => $translations ?? [],
            'seo_settings' => [
                'site_name' => $this->translateValue($settings['site_name'] ?? config('app.name')),
                'title_separator' => $settings['title_separator'] ?? ' - ',
                'title_order' => $settings['title_order'] ?? 'brand_first',
            ],
            'admin_seo' => $this->getAdminSeoContext($request),
            'archive_slugs' => [
                'blog' => ($settings['blog_page_id'] ?? null) ? \App\Models\Page::find($settings['blog_page_id'])->getTranslation('slug', app()->getLocale()) : 'blog',
                'projects' => ($settings['projects_page_id'] ?? null) ? \App\Models\Page::find($settings['projects_page_id'])->getTranslation('slug', app()->getLocale()) : 'projects',
            ]
        ];
    }

    /**
     * Helper to translate values shared with Inertia.
     */
    protected function translateValue($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            return $value[app()->getLocale()] ?? $value[config('app.fallback_locale')] ?? reset($value) ?? '';
        }

        return $value;
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

        if (count($parts) >= 2 && $parts[0] === 'admin') {
            // e.g. admin.pages.index or admin.dashboard.index
            $context['module'] = $parts[1] === 'dashboard' ? 'dashboard' : $parts[1];
            
            // Handle cases where route might be admin.dashboard.index or just admin.something
            if ($parts[1] === 'dashboard' && isset($parts[2])) {
                $context['action'] = $parts[2];
            } else {
                $context['action'] = $parts[2] ?? 'index';
            }

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
