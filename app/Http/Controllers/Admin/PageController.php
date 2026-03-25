<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Page\StorePageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Models\Page;
use App\Models\Revision;
use App\Services\AdminContentPersistenceService;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PageController extends BaseAdminContentController
{
    use HandlePublishableStatus;

    protected string $modelClass = Page::class;
    protected string $viewPath = 'Admin/Pages';
    protected string $module = 'pages';
    protected bool $useTaxonomies = false;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Page::class);

        return Inertia::render("{$this->viewPath}/Index", [
            'pages' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Page::class);

        $page = new Page();
        $page->setAttribute('revisions', []);
        
        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'page' => $page,
        ], $this->getSharedProps()));
    }

    public function store(StorePageRequest $request, AdminContentPersistenceService $persistence)
    {
        Gate::authorize('create', Page::class);

        $validated = $request->validated();

        $this->applyStatusLogic(null, $validated);

        $page = $persistence->createWithTaxonomies(
            Page::class,
            $validated,
            $request,
            fn (Page $page, Request $request) => $this->syncTaxonomies($page, $request),
        );
        /** @var Page $page */

        return redirect()->route('admin.pages.edit', $page->id)->with('success', 'pages.create_success');
    }

    public function edit(Page $page)
    {
        Gate::authorize('update', $page);

        return Inertia::render("{$this->viewPath}/Edit", $this->getEditProps($page, [
            'title', 'slug', 'meta_title', 'meta_description', 'og_image'
        ]));
    }

    public function update(UpdatePageRequest $request, Page $page, AdminContentPersistenceService $persistence)
    {
        Gate::authorize('update', $page);

        $validated = $request->validated();
        $this->assertOptimisticLock($page, $request);

        $this->applyStatusLogic($page, $validated);

        $persistence->updateWithRevisionAndTaxonomies(
            $page,
            $validated,
            $request,
            fn (Page $page) => $this->saveRevision($page),
            fn (Page $page, Request $request) => $this->syncTaxonomies($page, $request),
        );

        return redirect()->back()->with('success', 'pages.update_success');
    }

    public function destroy(Page $page)
    {
        Gate::authorize('delete', $page);

        $page->delete();
        return redirect()->back()->with('success', 'pages.delete_success');
    }

    public function restoreRevision(Request $request, Page $page, Revision $revision)
    {
        Gate::authorize('update', $page);

        $this->restoreRevisionContent($page, $revision, $request);

        return redirect()->back()->with('success', 'pages.update_success');
    }
}
