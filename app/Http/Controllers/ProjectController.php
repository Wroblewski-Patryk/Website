<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Page;
use App\Models\Template;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\SeoService;
use App\Services\BlockContentService;
use App\Support\ProjectPublicPresenter;

class ProjectController extends Controller
{
    public function show($slug)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $isAdmin = auth()->check();
        $page404Id = $settings['page_404_id'] ?? null;
        $seoService = app(SeoService::class);
        $contentService = app(BlockContentService::class);
        $projectPresenter = app(ProjectPublicPresenter::class);

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        try {
            $project = Project::query()
                ->with(['taxonomies' => fn ($query) => $query
                    ->where('type', 'category')
                    ->orderBy('order')])
                ->where(function ($query) use ($locale, $fallbackLocale, $slug) {
                    $query->whereRaw("json_unquote(json_extract(slug, '$.$locale')) = ?", [$slug])
                        ->orWhereRaw("json_unquote(json_extract(slug, '$.$fallbackLocale')) = ?", [$slug]);
                })
                ->first();

            if (!$project || ($project->status === 'draft' && !$isAdmin)) {
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

            $projectData = $projectPresenter->present($project);
            $projectData['content'] = $contentService->resolveReferences($project->content ?: []);

            $templates = $this->resolveTemplates();

            return Inertia::render('Public/Project', [
                'project' => $projectData,
                'header' => $templates['header'] ? ['content' => $contentService->resolveReferences($templates['header']->content ?: [])] : null,
                'footer' => $templates['footer'] ? ['content' => $contentService->resolveReferences($templates['footer']->content ?: [])] : null,
                'sidebar' => $templates['sidebar'] ? ['content' => $contentService->resolveReferences($templates['sidebar']->content ?: [])] : null,
                'page_template' => $templates['page'] ? ['content' => $contentService->resolveReferences($templates['page']->content ?: [])] : null,
                'settings' => $settings,
                'seo' => $seoService->getMetaData($project, $projectsTitle),
            ]);
        } catch (\Exception $e) {
            return $this->render404($settings, $page404Id);
        }
    }

    protected function render404($settings, $page404Id)
    {
        return (new PageController())->render404($settings, $page404Id);
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
