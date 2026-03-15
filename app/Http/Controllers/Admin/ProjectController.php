<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends BaseAdminContentController
{
    use HandlePublishableStatus;

    protected string $modelClass = Project::class;
    protected string $viewPath = 'Admin/Projects';
    protected string $module = 'projects';

    public function index(Request $request)
    {
        return Inertia::render("{$this->viewPath}/Index", [
            'projects' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        $project = new Project();
        $locales = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();
        $emptyLocales = array_fill_keys($locales, '');
        $project->setAttribute('title', $emptyLocales);
        $project->setAttribute('slug', $emptyLocales);
        $project->setAttribute('description', $emptyLocales);
        $project->setAttribute('content', []);
        
        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'project' => $project,
        ], $this->getSharedProps()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules(), [
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'client_name' => 'nullable|string',
            'project_url' => 'nullable|url',
            'completion_date' => 'nullable|date',
            'featured_image' => 'nullable|string',
        ]));

        $this->applyStatusLogic(null, $validated);

        $project = Project::create($validated);
        $this->syncTaxonomies($project, $request);

        return redirect()->route('admin.projects.edit', $project->id)->with('success', 'projects.create_success');
    }

    public function edit(Project $project)
    {
        return Inertia::render("{$this->viewPath}/Edit", $this->getEditProps($project, [
            'title', 'slug', 'description', 'meta_title', 'meta_description', 'og_image'
        ]));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules($project), [
            'description' => 'required|array',
            'description.*' => 'nullable|string',
            'client_name' => 'nullable|string',
            'project_url' => 'nullable|url',
            'completion_date' => 'nullable|date',
            'featured_image' => 'nullable|string',
        ]));

        $this->applyStatusLogic($project, $validated);

        $this->saveRevision($project);

        $project->update($validated);
        $this->syncTaxonomies($project, $request);

        return redirect()->back()->with('success', 'projects.update_success');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'projects.delete_success');
    }
}
