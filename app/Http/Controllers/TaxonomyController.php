<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use App\Models\Template;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\SeoService;
use App\Services\BlockContentService;

class TaxonomyController extends Controller
{
    public function show($type, $slug)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $page404Id = $settings['page_404_id'] ?? null;
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        $taxonomy = Taxonomy::where('type', $type)
            ->where('module', 'posts')
            ->where(function($query) use ($locale, $fallbackLocale, $slug) {
                $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$slug])
                      ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$slug]);
            })->first();

        if (!$taxonomy) {
            return $this->render404($settings, $page404Id);
        }

        $posts = $taxonomy->posts()->where('status', 'published')->latest('published_at')->paginate(12);
        
        $templates = $this->resolveTemplates();
        $contentService = app(BlockContentService::class);

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

    protected function render404($settings, $page404Id)
    {
        return (new PageController())->render404($settings, $page404Id);
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
