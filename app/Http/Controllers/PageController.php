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
     * Show the specified page.
     */
    public function show(Request $request, $slug = null)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $isMaintenance = ($settings['maintenance_mode'] ?? '0') === '1';
        $isAdmin = auth()->check();

        // 1. Maintenance Mode Check
        if ($isMaintenance && !$isAdmin) {
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
            abort(503, 'Site in maintenance mode.');
        }

        $homeId = $settings['home_page_id'] ?? null;
        $blogId = $settings['blog_page_id'] ?? null;
        $projectsId = $settings['projects_page_id'] ?? null;
        $page404Id = $settings['page_404_id'] ?? null;
        $comingSoonId = $settings['coming_soon_page_id'] ?? null;

        if (!$slug) {
            if (!$homeId)
                abort(404);
            $page = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])->find($homeId);
        }
        else {
            $page = Page::with(['headerOverride', 'footerOverride', 'sidebarOverride'])
                ->where('slug->en', $slug)
                ->orWhere('slug->pl', $slug)
                ->first();
        }

        // 2. Visibility & Status Check
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

        $component = 'Public/Page';
        $extraData = [];

        // Dynamic component and data injection based on Page ID match from Settings
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

        return Inertia::render($component, [
            'page' => $page,
            'settings' => $settings,
            'all_projects' => Project::all(),
            ...$extraData
        ]);
    }

    /**
     * Show a single blog post.
     */
    public function showPost($slug)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $isAdmin = auth()->check();
        $page404Id = $settings['page_404_id'] ?? null;

        $post = Post::where('slug->en', $slug)
            ->orWhere('slug->pl', $slug)
            ->first();

        if (!$post || ($post->status !== 'published' && !$isAdmin)) {
            return $this->render404($settings, $page404Id);
        }

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'settings' => $settings
        ]);
    }

    /**
     * Show a single project.
     */
    public function showProject($slug)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $isAdmin = auth()->check();
        $page404Id = $settings['page_404_id'] ?? null;

        $project = Project::where('slug', $slug)->first();

        if (!$project || ($project->status !== 'published' && !$isAdmin)) {
            return $this->render404($settings, $page404Id);
        }

        return Inertia::render('Public/Project', [
            'project' => $project,
            'settings' => $settings,
        ]);
    }

    /**
     * Render a custom 404 page if configured, or abort with standard 404.
     */
    private function render404($settings, $page404Id)
    {
        if ($page404Id) {
            $errorPage = Page::find($page404Id);
            if ($errorPage) {
                return Inertia::render('Public/Page', [
                    'page' => $errorPage,
                    'settings' => $settings
                ])->toResponse(request())->setStatusCode(404);
            }
        }
        abort(404);
    }
}
