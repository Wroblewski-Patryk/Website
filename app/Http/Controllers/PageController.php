<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Show the specified page or resolve blog/project routes dynamically.
     */
    public function show(Request $request, $localeOrPath = null, $path = null)
    {
        // Dynamiczna obsługa locale i ścieżki (ze względu na opcjonalny prefiks w web.php)
        if (in_array($localeOrPath, ['pl', 'en'])) {
            $locale = $localeOrPath;
            $path = $path;
            app()->setLocale($locale);
        }
        else {
            $locale = app()->getLocale();
            $path = $localeOrPath;
        }

        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Database connection failed in PageController: " . $e->getMessage());
            $settings = [];
        }

        $isMaintenance = ($settings['maintenance_mode'] ?? '0') === '1';
        $isAdmin = auth()->check();

        // 1. Maintenance Mode Check
        if ($isMaintenance && !$isAdmin) {
            try {
                $maintPageId = $settings['maintenance_page_id'] ?? null;
                if ($maintPageId) {
                    $maintPage = Page::find($maintPageId);
                    if ($maintPage) {
                        return Inertia::render('Public/Page', [
                            'page' => $maintPage,
                            'settings' => $settings
                        ]);
                    }
                }
            } catch (\Exception $e) {}
            abort(503, 'Site in maintenance mode.');
        }

        $homeId = $settings['home_page_id'] ?? null;
        $blogId = $settings['blog_page_id'] ?? null;
        $projectsId = $settings['projects_page_id'] ?? null;
        $page404Id = $settings['page_404_id'] ?? null;
        $comingSoonId = $settings['coming_soon_page_id'] ?? null;

        if (!$path) {
            if (!$homeId)
                abort(404);
            
            try {
                $page = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])->find($homeId);
                return $this->renderPage($page, $settings, $isAdmin, $comingSoonId, $page404Id);
            } catch (\Exception $e) {
                return $this->render404($settings, $page404Id);
            }
        }

        $segments = explode('/', $path);
        $firstSegment = $segments[0];

        // Find page by slug (checking current locale preferred, or any)
        // Wyszukiwanie priorytetowo w obecnym języku, a następnie w ustawionym fallback config['app.fallback_locale']
        $fallbackLocale = config('app.fallback_locale');
        $firstPage = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])
            ->where("slug->{$locale}", $firstSegment)
            ->orWhere("slug->{$fallbackLocale}", $firstSegment)
            ->first();

        if ($firstPage) {
            // Check if this is a special archive page (Blog)
            if ($firstPage->id == $blogId && count($segments) > 1) {
                return $this->showPost($segments[1]); // Assuming single level for now
            }

            // Check if this is a special archive page (Projects)
            if ($firstPage->id == $projectsId && count($segments) > 1) {
                return $this->showProject($segments[1]);
            }

            // Otherwise, it's just a regular page (or the archive page itself)
            return $this->renderPage($firstPage, $settings, $isAdmin, $comingSoonId, $page404Id);
        }

        // If no page found for the first segment, try finding a page by the full path (for nested static pages if any)
        $fullPage = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])
            ->where("slug->{$locale}", $path)
            ->orWhere("slug->{$fallbackLocale}", $path)
            ->first();

        if ($fullPage) {
            return $this->renderPage($fullPage, $settings, $isAdmin, $comingSoonId, $page404Id);
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
            if ($comingSoonId) {
                $soonPage = Page::find($comingSoonId);
                if ($soonPage) {
                    return Inertia::render('Public/Page', [
                        'page' => $soonPage,
                        'settings' => $settings
                    ]);
                }
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

        return Inertia::render($component, [
            'page' => $page,
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
            $post = Post::where("slug->{$locale}", $slug)
                ->orWhere("slug->{$fallbackLocale}", $slug)
                ->first();

            if (!$post || ($post->status !== 'published' && !$isAdmin)) {
                return $this->render404($settings, $page404Id);
            }

            $blogId = $settings['blog_page_id'] ?? null;
            $blogTitle = $blogId ? $seoService->getEntityTitle(Page::find($blogId)) : 'Blog';

            return Inertia::render('Blog/Show', [
                'post' => $post,
                'settings' => $settings,
                'seo' => $seoService->getMetaData($post, $blogTitle),
            ]);
        } catch (\Exception $e) {
            return $this->render404($settings, $page404Id);
        }
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
            $project = Project::where("slug->{$locale}", $slug)
                ->orWhere("slug->{$fallbackLocale}", $slug)
                ->first();

            if (!$project || ($project->status !== 'published' && !$isAdmin)) {
                return $this->render404($settings, $page404Id);
            }

            $projectsId = $settings['projects_page_id'] ?? null;
            $projectsTitle = $projectsId ? $seoService->getEntityTitle(Page::find($projectsId)) : 'Projekty';

            return Inertia::render('Public/Project', [
                'project' => $project,
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
                $errorPage = Page::find($page404Id);
                if ($errorPage) {
                    return Inertia::render('Public/Page', [
                        'page' => $errorPage,
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
}
