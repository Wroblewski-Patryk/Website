<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Project\StoreProjectRequest;
use App\Http\Requests\Admin\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Services\AdminContentPersistenceService;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProjectController extends BaseAdminContentController
{
    use HandlePublishableStatus;

    protected string $modelClass = Project::class;
    protected string $viewPath = 'Admin/Projects';
    protected string $module = 'projects';

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Project::class);

        return Inertia::render("{$this->viewPath}/Index", [
            'projects' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Project::class);

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

    public function store(StoreProjectRequest $request, AdminContentPersistenceService $persistence)
    {
        Gate::authorize('create', Project::class);

        $validated = $request->validated();

        $this->applyStatusLogic(null, $validated);

        $project = $persistence->createWithTaxonomies(
            Project::class,
            $validated,
            $request,
            fn (Project $project, Request $request) => $this->syncTaxonomies($project, $request),
            ['taxonomies'],
        );
        /** @var Project $project */

        return redirect()->route('admin.projects.edit', $project->id)->with('success', 'admin.projects.create_success');
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return Inertia::render("{$this->viewPath}/Edit", array_merge(
            $this->getEditProps($project, ['title', 'slug', 'description', 'meta_title', 'meta_description', 'og_image']),
            ['availableClients' => \App\Models\Client::orderBy('title')->get()]
        ));
    }

    public function update(UpdateProjectRequest $request, Project $project, AdminContentPersistenceService $persistence)
    {
        Gate::authorize('update', $project);

        $validated = $request->validated();
        $this->assertOptimisticLock($project, $request);

        $this->applyStatusLogic($project, $validated);

        $persistence->updateWithRevisionAndTaxonomies(
            $project,
            $validated,
            $request,
            fn (Project $project) => $this->saveRevision($project),
            fn (Project $project, Request $request) => $this->syncTaxonomies($project, $request),
            ['taxonomies'],
        );

        return redirect()->back()->with('success', 'admin.projects.update_success');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();
        return redirect()->back()->with('success', 'admin.projects.delete_success');
    }
}
