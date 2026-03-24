<?php

namespace App\Http\Middleware;

use App\Models\Page;
use App\Support\SharedInertiaCache;
use Illuminate\Support\Collection;
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
        $archivePages = collect();

        try {
            $settings = \Illuminate\Support\Facades\Cache::rememberForever(SharedInertiaCache::keySettings(), function () {
                return \App\Models\Setting::pluck('value', 'key')->toArray();
            });

            $headerId = $settings['default_header_id'] ?? null;
            $footerId = $settings['default_footer_id'] ?? null;

            $header = \Illuminate\Support\Facades\Cache::rememberForever(SharedInertiaCache::keyHeader($headerId ?: 'default'), function () use ($headerId) {
                return $headerId
                    ? \App\Models\Template::find($headerId)
                    : \App\Models\Template::where('type', 'header')->where('is_active', true)->first();
            });

            $footer = \Illuminate\Support\Facades\Cache::rememberForever(SharedInertiaCache::keyFooter($footerId ?: 'default'), function () use ($footerId) {
                return $footerId
                    ? \App\Models\Template::find($footerId)
                    : \App\Models\Template::where('type', 'footer')->where('is_active', true)->first();
            });

            $languages = \Illuminate\Support\Facades\Cache::rememberForever(SharedInertiaCache::keyLanguages(), function () {
                return \App\Models\Language::where('is_active', true)->orderBy('is_default', 'desc')->get();
            });
            $allProjects = \Illuminate\Support\Facades\Cache::rememberForever(SharedInertiaCache::keyProjects(), function () {
                return \App\Models\Project::query()
                    ->select(['id', 'title', 'slug', 'category', 'desktop_image', 'mobile_image', 'order'])
                    ->orderBy('order')
                    ->get();
            });

            $archivePageIds = collect([
                $settings['blog_page_id'] ?? null,
                $settings['projects_page_id'] ?? null,
            ])->filter(fn ($id) => !empty($id))->unique()->values();

            if ($archivePageIds->isNotEmpty()) {
                $archivePages = Page::query()
                    ->select(['id', 'slug'])
                    ->whereIn('id', $archivePageIds->all())
                    ->get()
                    ->keyBy('id');
            }
            
            $themeColors = isset($settings['theme_colors']) ? (is_array($settings['theme_colors']) ? $settings['theme_colors'] : json_decode($settings['theme_colors'], true)) : [];
            $themeRadius = isset($settings['theme_radius']) ? (is_array($settings['theme_radius']) ? $settings['theme_radius'] : json_decode($settings['theme_radius'], true)) : [];
            $themeTypography = isset($settings['theme_typography']) ? (is_array($settings['theme_typography']) ? $settings['theme_typography'] : json_decode($settings['theme_typography'], true)) : [];

            $themeConfig = [
                'globals' => [
                    'colors' => $themeColors,
                    'borderRadius' => $themeRadius,
                    'typography' => $themeTypography,
                    'fonts' => [
                        'sans' => $themeTypography['fontSans'] ?? 'Inter',
                        'serif' => $themeTypography['fontSerif'] ?? 'Merriweather',
                        'mono' => $themeTypography['fontMono'] ?? 'Fira Code',
                    ],
                    'advanced' => [
                        'font-sans' => 'var(--font-sans)',
                        'font-serif' => 'var(--font-serif)',
                        'font-mono' => 'var(--font-mono)',
                    ]
                ],
                // Add defaults for DynamicBlock to use
                'block_defaults' => [
                    'paragraph' => ['fontSize' => '1.125rem', 'lineHeight' => '1.75'],
                    'heading' => ['fontWeight' => '900', 'letterSpacing' => '-0.05em'],
                ]
            ];



        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Database connection failed in HandleInertiaRequests: " . $e->getMessage());
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->getRoleNames()->first(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->mapWithKeys(function ($permission) {
                        return ['can_' . str_replace('-', '_', $permission) => true];
                    })->toArray(),
                ] : null,
            ],
            'header' => fn () => $header ? $header->content : null,
            'footer' => fn () => $footer ? $footer->content : null,
            'locale' => fn () => app()->getLocale(),
            'languages' => fn () => $languages,
            'all_projects' => fn () => $allProjects,
            'theme_config' => fn () => $themeConfig,
            'menus' => fn () => [], // Safe historical fallback
            'translations' => fn () => \Illuminate\Support\Facades\Cache::remember(SharedInertiaCache::keyTranslations(app()->getLocale()), 3600, function () {
                return Translation::query()->select(['key', 'text'])->get()->reduce(function ($carry, $translation) {
                    $locale = app()->getLocale();
                    $fallback = config('app.fallback_locale', 'en');
                    $text = $translation->getTranslation('text', $locale, false) ?: $translation->getTranslation('text', $fallback, false);
                    $carry[$translation->key] = $text;
                    return $carry;
                }, []);
            }),
            'seo_settings' => fn () => [
                'site_name' => $this->translateValue($settings['site_name'] ?? config('app.name')),
                'title_separator' => $settings['title_separator'] ?? ' - ',
                'title_order' => $settings['title_order'] ?? 'brand_first',
            ],
            'admin_seo' => fn () => $this->getAdminSeoContext($request),
            'archive_slugs' => fn () => [
                'blog' => $this->resolveArchiveSlug($archivePages, $settings['blog_page_id'] ?? null, 'blog'),
                'projects' => $this->resolveArchiveSlug($archivePages, $settings['projects_page_id'] ?? null, 'projects'),
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
            $context['action_label'] = $seoService->getModuleLabel($context['action']); // This is slightly wrong as it uses module label logic, but I will fix SeoService to handle both or just use module label for now if actions are also in menu.*
        }

        return $context;
    }

    protected function resolveArchiveSlug(Collection $pages, mixed $pageId, string $fallback): string
    {
        if (empty($pageId)) {
            return $fallback;
        }

        $page = $pages->get((int) $pageId);

        if (!$page) {
            return $fallback;
        }

        return (string) $page->getTranslation('slug', app()->getLocale());
    }
}
