<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use App\Models\Template;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\SeoService;
use App\Services\BlockContentService;

class PostController extends Controller
{
    public function show($slug)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $isAdmin = auth()->check();
        $page404Id = $settings['page_404_id'] ?? null;
        $seoService = app(SeoService::class);
        $contentService = app(BlockContentService::class);

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        try {
            $post = Post::where(function($query) use ($locale, $fallbackLocale, $slug) {
                $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$slug])
                      ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$slug]);
            })->first();

            if (!$post || ($post->status === 'draft' && !$isAdmin)) {
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
            $postData['content'] = $contentService->resolveReferences($post->content ?: []);
            $postData['title'] = $post->title;
            $postData['slug'] = $post->slug;

            $templates = $this->resolveTemplates();
            
            return Inertia::render('Blog/Show', [
                'post' => $postData,
                'header' => $templates['header'] ? ['content' => $contentService->resolveReferences($templates['header']->content ?: [])] : null,
                'footer' => $templates['footer'] ? ['content' => $contentService->resolveReferences($templates['footer']->content ?: [])] : null,
                'sidebar' => $templates['sidebar'] ? ['content' => $contentService->resolveReferences($templates['sidebar']->content ?: [])] : null,
                'page_template' => $templates['page'] ? ['content' => $contentService->resolveReferences($templates['page']->content ?: [])] : null,
                'settings' => $settings,
                'seo' => $seoService->getMetaData($post, $blogTitle),
            ]);
        } catch (\Exception $e) {
            return $this->render404($settings, $page404Id);
        }
    }

    protected function render404($settings, $page404Id)
    {
        if ($page404Id) {
            $errorPage = Page::find($page404Id);
            if ($errorPage) {
                return (new PageController())->render404($settings, $page404Id);
            }
        }
        abort(404);
    }

    protected function getComingSoonPage($settings)
    {
        $id = $settings['coming_soon_page_id'] ?? null;
        return $id ? Page::find($id) : null;
    }

    protected function resolveTemplates($entity = null)
    {
        return [
            'header' => Template::where('type', 'header')->where('is_active', true)->where('is_default', true)->first(),
            'footer' => Template::where('type', 'footer')->where('is_active', true)->where('is_default', true)->first(),
            'sidebar' => Template::where('type', 'sidebar')->where('is_active', true)->where('is_default', true)->first(),
            'page' => Template::where('type', 'page')->where('is_active', true)->where('is_default', true)->first(),
        ];
    }
}
