<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Project\StoreProjectRequest;
use App\Http\Requests\Admin\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'availableClients' => \App\Models\Client::orderBy('title')->get(),
        ], $this->getSharedProps()));
    }

    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();

        $this->applyStatusLogic(null, $validated);

        $project = DB::transaction(function () use ($validated, $request) {
            $project = Project::create(\Illuminate\Support\Arr::except($validated, ['taxonomies']));
            $this->syncTaxonomies($project, $request);

            return $project;
        });

        return redirect()->route('admin.projects.edit', $project->id)->with('success', 'admin.projects.create_success');
    }

    public function edit(Project $project)
    {
        return Inertia::render("{$this->viewPath}/Edit", array_merge(
            $this->getEditProps($project, ['title', 'slug', 'description', 'meta_title', 'meta_description', 'og_image']),
            ['availableClients' => \App\Models\Client::orderBy('title')->get()]
        ));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();
        $this->assertOptimisticLock($project, $request);

        $this->applyStatusLogic($project, $validated);

        DB::transaction(function () use ($project, $validated, $request) {
            $this->saveRevision($project);
            $project->update(\Illuminate\Support\Arr::except($validated, ['taxonomies']));
            $this->syncTaxonomies($project, $request);
        });

        return redirect()->back()->with('success', 'admin.projects.update_success');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'admin.projects.delete_success');
    }
}
