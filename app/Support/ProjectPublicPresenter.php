<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Support\Collection;

class ProjectPublicPresenter
{
    public function present(Project $project): array
    {
        $data = $project->toArray();
        unset($data['taxonomies']);

        $data['title'] = $project->title;
        $data['slug'] = $project->slug;
        $data['category'] = $this->resolveCategory($project);

        return $data;
    }

    /**
     * @param  iterable<Project>  $projects
     * @return array<int, array<string, mixed>>
     */
    public function presentCollection(iterable $projects): array
    {
        return Collection::make($projects)
            ->map(fn (Project $project) => $this->present($project))
            ->values()
            ->all();
    }

    protected function resolveCategory(Project $project): mixed
    {
        $taxonomies = $project->relationLoaded('taxonomies')
            ? $project->taxonomies
            : $project->taxonomies()
                ->where('type', 'category')
                ->orderBy('order')
                ->get();

        $primaryCategory = $taxonomies->firstWhere('type', 'category') ?? $taxonomies->first();

        return $primaryCategory?->getTranslations('title') ?? $project->category;
    }
}
