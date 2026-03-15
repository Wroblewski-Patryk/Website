<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
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
        $blogId = \App\Models\Setting::where('key', 'blog_page_id')->value('value');
        $projectsId = \App\Models\Setting::where('key', 'projects_page_id')->value('value');

        // Dynamiczna obsługa locale i ścieżki (ze względu na opcjonalny prefiks w web.php)
        if (in_array($localeOrPath, ['pl', 'en'])) {
            $locale = $localeOrPath;
            app()->setLocale($locale);
        }
        else {
            $locale = app()->getLocale();
            $path = $localeOrPath;
        }
        
        $fallbackLocale = config('app.fallback_locale', 'pl');
        $actualPath = $path ?: $localeOrPath;
        $segments = explode('/', trim($actualPath, '/'));
        $firstSegment = $segments[0] ?? '';

        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {}

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
            return $this->showTaxonomy($firstSegment, $segments[1]);
        }

        // 2. Resolve Page or special archive
        // Try to find page by the FULL path first (for nested slugs or specific pages)
        $firstPage = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])
            ->where(function ($query) use ($locale, $fallbackLocale, $actualPath) {
                $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$actualPath])
                      ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$actualPath]);
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
                    return $this->showPost($tail);
                }
                if ($parentPage->id == $projectsId) {
                    return $this->showProject($tail);
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
        if (!$page || ($page->status === 'draft' && !$isAdmin)) {
            return $this->render404($settings, $page404Id);
        }

        if ($page->status === 'planned' && !$isAdmin) {
            $soonPage = $this->getComingSoonPage($settings);
            if ($soonPage) {
                return Inertia::render('Public/Page', [
                    'page' => $soonPage,
                    'settings' => $settings
                ]);
            }
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
        
        // Resolve translatable content for frontend
        $pageData = $page->toArray();
        $pageData['content'] = $contentService->resolveReferences($page->content ?: []); 
        $pageData['title'] = $page->title;
        $pageData['slug'] = $page->slug;

        // Resolve templates
        $templates = $this->resolveTemplates($page);
        $header = $templates['header'];
        $footer = $templates['footer'];
        $sidebar = $templates['sidebar'];
        $pageTemplate = $templates['page'];

        return Inertia::render($component, [
            'page' => $pageData,
            'header' => $header ? ['content' => $contentService->resolveReferences($header->content ?: [])] : null,
            'footer' => $footer ? ['content' => $contentService->resolveReferences($footer->content ?: [])] : null,
            'sidebar' => $sidebar ? ['content' => $contentService->resolveReferences($sidebar->content ?: [])] : null,
            'page_template' => $pageTemplate ? ['content' => $contentService->resolveReferences($pageTemplate->content ?: [])] : null,
            'settings' => $settings,
            'seo' => $seoService->getMetaData($page),
            'all_projects' => Project::all(),
            ...$extraData
        ]);
    }

    /**
     * Show a single blog post.
     */
    public function showPost($slug)
    {
        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {}
        
        $isAdmin = auth()->check();
        $page404Id = $settings['page_404_id'] ?? null;
        $seoService = app(\App\Services\SeoService::class);

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');
        try {
            $post = Post::where(function($query) use ($locale, $fallbackLocale, $slug) {
                $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$slug])
                      ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$slug]);
            })->first();

            if (!$post) {
                return $this->render404($settings, $page404Id);
            }

            if ($post->status === 'draft' && !$isAdmin) {
                return $this->render404($settings, $page404Id);
            }

            if ($post->status === 'planned' && !$isAdmin) {
                $soonPage = $this->getComingSoonPage($settings);
                if ($soonPage) {
                    return Inertia::render('Public/Page', [
                        'page' => $soonPage,
                        'settings' => $settings
                    ]);
                }
                return $this->render404($settings, $page404Id);
            }

            $blogId = $settings['blog_page_id'] ?? null;
            $blogTitle = $blogId ? $seoService->getEntityTitle(Page::find($blogId)) : 'Blog';

            $postData = $post->toArray();
            $postData['content'] = app(\App\Services\BlockContentService::class)->resolveReferences($post->content ?: []);
            $postData['title'] = $post->title;
            $postData['slug'] = $post->slug;

            // Resolve templates for posts
            $templates = $this->resolveTemplates();
            $header = $templates['header'];
            $footer = $templates['footer'];
            $sidebar = $templates['sidebar'];
            $pageTemplate = $templates['page'];

            $contentService = app(\App\Services\BlockContentService::class);

            return Inertia::render('Blog/Show', [
                'post' => $postData,
                'header' => $header ? ['content' => $contentService->resolveReferences($header->content ?: [])] : null,
                'footer' => $footer ? ['content' => $contentService->resolveReferences($footer->content ?: [])] : null,
                'sidebar' => $sidebar ? ['content' => $contentService->resolveReferences($sidebar->content ?: [])] : null,
                'page_template' => $pageTemplate ? ['content' => $contentService->resolveReferences($pageTemplate->content ?: [])] : null,
                'settings' => $settings,
                'seo' => $seoService->getMetaData($post, $blogTitle),
            ]);
        } catch (\Exception $e) {
            return $this->render404($settings, $page404Id);
        }
    }

    /**
     * Show content filtered by taxonomy.
     */
    public function showTaxonomy($type, $slug)
    {
        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {}
        
        $page404Id = $settings['page_404_id'] ?? null;
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        $taxonomy = \App\Models\Taxonomy::where('type', $type)
            ->where(function($query) use ($locale, $fallbackLocale, $slug) {
                $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$slug])
                      ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$slug]);
            })->first();

        if (!$taxonomy) {
            return $this->render404($settings, $page404Id);
        }

        $posts = $taxonomy->posts()->where('status', 'published')->latest('published_at')->paginate(12);
        
        // This is a bit simplified - we could use a dedicated Taxonomy/Show component
        // or just reuse Blog/Index with a "filtered by" header.
        
        $templates = $this->resolveTemplates();
        $contentService = app(\App\Services\BlockContentService::class);
        $seoService = app(\App\Services\SeoService::class);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'taxonomy' => $taxonomy,
            'header' => $templates['header'] ? ['content' => $contentService->resolveReferences($templates['header']->content ?: [])] : null,
            'footer' => $templates['footer'] ? ['content' => $contentService->resolveReferences($templates['footer']->content ?: [])] : null,
            'sidebar' => $templates['sidebar'] ? ['content' => $contentService->resolveReferences($templates['sidebar']->content ?: [])] : null,
            'page_template' => $templates['page'] ? ['content' => $contentService->resolveReferences($templates['page']->content ?: [])] : null,
            'settings' => $settings,
            'seo' => [
                'title' => $taxonomy->getTranslation('title', $locale) . ' - Blog',
                'description' => $taxonomy->getTranslation('description', $locale),
            ]
        ]);
    }

    /**
     * Show a single project.
     */
    public function showProject($slug)
    {
        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {}
        
        $isAdmin = auth()->check();
        $page404Id = $settings['page_404_id'] ?? null;
        $seoService = app(\App\Services\SeoService::class);

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');
        try {
            $project = Project::where(function($query) use ($locale, $fallbackLocale, $slug) {
                $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$slug])
                      ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$slug]);
            })->first();

            if (!$project) {
                return $this->render404($settings, $page404Id);
            }

            if ($project->status === 'draft' && !$isAdmin) {
                return $this->render404($settings, $page404Id);
            }

            if ($project->status === 'planned' && !$isAdmin) {
                $soonPage = $this->getComingSoonPage($settings);
                if ($soonPage) {
                    return Inertia::render('Public/Page', [
                        'page' => $soonPage,
                        'settings' => $settings
                    ]);
                }
                return $this->render404($settings, $page404Id);
            }

            $projectsId = $settings['projects_page_id'] ?? null;
            $projectsTitle = $projectsId ? $seoService->getEntityTitle(Page::find($projectsId)) : 'Projekty';

            $projectData = $project->toArray();
            $projectData['content'] = app(\App\Services\BlockContentService::class)->resolveReferences($project->content ?: []);
            $projectData['title'] = $project->title;
            $projectData['slug'] = $project->slug;

            // Resolve templates for projects
            $templates = $this->resolveTemplates();
            $header = $templates['header'];
            $footer = $templates['footer'];
            $sidebar = $templates['sidebar'];
            $pageTemplate = $templates['page'];

            $contentService = app(\App\Services\BlockContentService::class);

            return Inertia::render('Public/Project', [
                'project' => $projectData,
                'header' => $header ? ['content' => $contentService->resolveReferences($header->content ?: [])] : null,
                'footer' => $footer ? ['content' => $contentService->resolveReferences($footer->content ?: [])] : null,
                'sidebar' => $sidebar ? ['content' => $contentService->resolveReferences($sidebar->content ?: [])] : null,
                'page_template' => $pageTemplate ? ['content' => $contentService->resolveReferences($pageTemplate->content ?: [])] : null,
                'settings' => $settings,
                'seo' => $seoService->getMetaData($project, $projectsTitle),
            ]);
        } catch (\Exception $e) {
            return $this->render404($settings, $page404Id);
        }
    }

    /**
     * Render a custom 404 page if configured, or abort with standard 404.
     */
    private function render404($settings, $page404Id)
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
        return [
            'header' => ($entity && method_exists($entity, 'headerOverride') && $entity->headerOverride) ? $entity->headerOverride : Template::where('type', 'header')->where('is_active', true)->where('is_default', true)->first(),
            'footer' => ($entity && method_exists($entity, 'footerOverride') && $entity->footerOverride) ? $entity->footerOverride : Template::where('type', 'footer')->where('is_active', true)->where('is_default', true)->first(),
            'sidebar' => ($entity && method_exists($entity, 'sidebarOverride') && $entity->sidebarOverride) ? $entity->sidebarOverride : Template::where('type', 'sidebar')->where('is_active', true)->where('is_default', true)->first(),
            'page' => ($entity && method_exists($entity, 'template') && $entity->template) ? $entity->template : Template::where('type', 'page')->where('is_active', true)->where('is_default', true)->first(),
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
}
