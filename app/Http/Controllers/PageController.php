<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Language;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Show the specified page or resolve blog/project routes dynamically.
     */
    public function show(Request $request, $localeOrPath = null, $path = null)
    {
        $activeLocales = $this->getActiveLocaleCodes();

        // Dynamiczna obsługa locale i ścieżki (ze względu na opcjonalny prefiks w web.php)
        if (in_array($localeOrPath, $activeLocales, true)) {
            $locale = $localeOrPath;
            app()->setLocale($locale);
        }
        else {
            $locale = app()->getLocale();
            $path = $localeOrPath;
        }
        
        $fallbackLocale = (string) config('app.fallback_locale', app()->getLocale());
        $actualPath = $path ?: $localeOrPath;
        $segments = explode('/', trim($actualPath, '/'));
        $firstSegment = $segments[0] ?? '';

        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {}
        $blogId = $settings['blog_page_id'] ?? null;
        $projectsId = $settings['projects_page_id'] ?? null;

        $isAdmin = auth()->check();
        $comingSoonId = $settings['coming_soon_page_id'] ?? null;
        $page404Id = $settings['page_404_id'] ?? null;

        // 1. Check if we are at root (/)
        if ($actualPath === null || $actualPath === '') {
            $homeId = $settings['home_page_id'] ?? null;
            if ($homeId) {
                $homePage = Page::find($homeId);
                if ($homePage) {
                    return $this->renderPage($homePage, $settings, $isAdmin, $comingSoonId, $page404Id);
                }
            }
            return $this->render404($settings, $page404Id);
        }

        // 1.5. Check for Taxonomy URLs (/category/{slug} or /tag/{slug})
        if (in_array($firstSegment, ['category', 'tag']) && count($segments) > 1) {
            return (new TaxonomyController())->show($firstSegment, $segments[1]);
        }

        // 2. Resolve Page or special archive
        // Try to find page by the FULL path first (for nested slugs or specific pages)
        $firstPage = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])
            ->where(function ($query) use ($locale, $fallbackLocale, $actualPath) {
                $query->where("slug->{$locale}", $actualPath)
                    ->orWhere("slug->{$fallbackLocale}", $actualPath);
            })->first();

        // If not found, try finding a parent page (like Blog or Projects archive)
        if (!$firstPage && count($segments) > 1) {
            $firstSegment = $segments[0];
            $parentPage = Page::where('status', 'published')
                ->where(function ($query) use ($firstSegment, $locale, $fallbackLocale) {
                    $query->where("slug->{$locale}", $firstSegment)
                          ->orWhere("slug->{$fallbackLocale}", $firstSegment);
                })
                ->first();

            if ($parentPage) {
                $tail = implode('/', array_slice($segments, 1));
                if ($parentPage->id == $blogId) {
                    return (new PostController())->show($tail);
                }
                if ($parentPage->id == $projectsId) {
                    return (new ProjectController())->show($tail);
                }
            }
        }

        if ($firstPage) {
            return $this->renderPage($firstPage, $settings, $isAdmin, $comingSoonId, $page404Id);
        }

        return $this->render404($settings, $page404Id);
    }

    /**
     * Helper to render a page with status checks.
     */
    private function renderPage($page, $settings, $isAdmin, $comingSoonId, $page404Id)
    {
        if (!$page) {
            return $this->render404($settings, $page404Id);
        }

        if (!$isAdmin && $page->status !== 'published') {
            $publishAt = $page->published_at;

            // Planned pages with a future publish date should route to coming soon.
            if ($page->status === 'planned' && $publishAt && $publishAt->isFuture()) {
                $soonPage = $this->getComingSoonPage($settings);
                if ($soonPage) {
                    return Inertia::render('Public/Page', [
                        'page' => $soonPage,
                        'settings' => $settings,
                        'coming_soon_countdown_to' => $publishAt->toIso8601String(),
                        'coming_soon_source' => [
                            'id' => $page->id,
                            'title' => $page->title,
                        ],
                    ]);
                }

                return Inertia::render('Public/ComingSoonFallback', [
                    'settings' => $settings,
                    'countdown_to' => $publishAt->toIso8601String(),
                ]);
            }

            // Non-public pages without a valid schedule should return 404.
            return $this->render404($settings, $page404Id);
        }

        $blogId = $settings['blog_page_id'] ?? null;
        $projectsId = $settings['projects_page_id'] ?? null;

        $component = 'Public/Page';
        $extraData = [];

        if ($page->id == $blogId) {
            $component = 'Blog/Index';
            $extraData['posts'] = Post::where('status', 'published')
                ->latest('published_at')
                ->paginate(12);
        }
        elseif ($page->id == $projectsId) {
            $component = 'Public/ProjectList';
            $extraData['projects'] = Project::where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $seoService = app(\App\Services\SeoService::class);
        $contentService = app(\App\Services\BlockContentService::class);
        
        $pageData = $page->toArray();
        $pageData['content'] = $contentService->resolveReferences($page->content ?: []); 
        $pageData['title'] = $page->title;
        $pageData['slug'] = $page->slug;

        $templates = $this->resolveTemplates($page);

        return Inertia::render($component, [
            'page' => $pageData,
            'header' => $templates['header'] ? ['content' => $contentService->resolveReferences($templates['header']->content ?: [])] : null,
            'footer' => $templates['footer'] ? ['content' => $contentService->resolveReferences($templates['footer']->content ?: [])] : null,
            'sidebar' => $templates['sidebar'] ? ['content' => $contentService->resolveReferences($templates['sidebar']->content ?: [])] : null,
            'page_template' => $templates['page'] ? ['content' => $contentService->resolveReferences($templates['page']->content ?: [])] : null,
            'settings' => $settings,
            'seo' => $seoService->getMetaData($page),
            'all_projects' => Project::all(),
            ...$extraData
        ]);
    }

    /**
     * Render a custom 404 page if configured, or abort with standard 404.
     */
    public function render404($settings, $page404Id)
    {
        try {
            if ($page404Id) {
                $errorPage = Page::with(['headerOverride', 'footerOverride'])->find($page404Id);
                if ($errorPage) {
                    $contentService = app(\App\Services\BlockContentService::class);
                    $pageData = $errorPage->toArray();
                    $pageData['content'] = $contentService->resolveReferences($errorPage->content ?: []);
                    $pageData['title'] = $errorPage->title;
                    $pageData['slug'] = $errorPage->slug;

                    $header = $errorPage->headerOverride ?? Template::where('type', 'header')->where('is_active', true)->where('is_default', true)->first();
                    $footer = $errorPage->footerOverride ?? Template::where('type', 'footer')->where('is_active', true)->where('is_default', true)->first();

                    return Inertia::render('Public/Page', [
                        'page' => $pageData,
                        'header' => $header ? ['content' => $contentService->resolveReferences($header->content ?: [])] : null,
                        'footer' => $footer ? ['content' => $contentService->resolveReferences($footer->content ?: [])] : null,
                        'settings' => $settings,
                        'seo' => app(\App\Services\SeoService::class)->getMetaData($errorPage),
                    ])->toResponse(request())->setStatusCode(404);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Database connection failed in render404: " . $e->getMessage());
        }
        abort(404);
    }

    /**
     * Resolve default or overridden templates.
     */
    private function resolveTemplates($entity = null)
    {
        $types = ['header', 'footer', 'sidebar', 'page'];
        $resolved = collect();

        $overrides = [
            'header' => ($entity && method_exists($entity, 'headerOverride')) ? $entity->headerOverride : null,
            'footer' => ($entity && method_exists($entity, 'footerOverride')) ? $entity->footerOverride : null,
            'sidebar' => ($entity && method_exists($entity, 'sidebarOverride')) ? $entity->sidebarOverride : null,
            'page' => ($entity && method_exists($entity, 'template')) ? $entity->template : null,
        ];

        foreach ($overrides as $type => $override) {
            if ($override) {
                $resolved->put($type, $override);
            }
        }

        $missingTypes = array_values(array_filter($types, fn ($type) => !$resolved->has($type)));
        if (!empty($missingTypes)) {
            $defaults = Template::query()
                ->whereIn('type', $missingTypes)
                ->where('is_active', true)
                ->where('is_default', true)
                ->get()
                ->keyBy('type');

            foreach ($missingTypes as $type) {
                $resolved->put($type, $defaults->get($type));
            }
        }

        return [
            'header' => $resolved->get('header'),
            'footer' => $resolved->get('footer'),
            'sidebar' => $resolved->get('sidebar'),
            'page' => $resolved->get('page'),
        ];
    }

    /**
     * Get the configured coming soon page.
     */
    private function getComingSoonPage($settings)
    {
        $id = $settings['coming_soon_page_id'] ?? null;
        return $id ? Page::find($id) : null;
    }

    private function getActiveLocaleCodes(): array
    {
        $codes = Language::query()
            ->where('is_active', true)
            ->pluck('code')
            ->filter()
            ->values()
            ->all();

        if (!empty($codes)) {
            return $codes;
        }

        return array_values(array_unique(array_filter([
            app()->getLocale(),
            config('app.fallback_locale'),
        ])));
    }
}
