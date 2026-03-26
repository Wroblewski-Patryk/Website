<?php

namespace App\Services\AdminSearch\Providers;

use App\Models\Project;

class ProjectSearchProvider extends AbstractLocalizedContentSearchProvider
{
    public function key(): string
    {
        return 'projects';
    }

    protected function modelClass(): string
    {
        return Project::class;
    }

    protected function resultType(): string
    {
        return 'project';
    }

    protected function editRouteName(): string
    {
        return 'admin.projects.edit';
    }
}
