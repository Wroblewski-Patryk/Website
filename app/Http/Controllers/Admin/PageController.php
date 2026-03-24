<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Page\StorePageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Models\Page;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return Inertia::render("{$this->viewPath}/Index", [
            'pages' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        $page = new Page();
        $page->setAttribute('revisions', []);
        
        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'page' => $page,
        ], $this->getSharedProps()));
    }

    public function store(StorePageRequest $request)
    {
        $validated = $request->validated();

        $this->applyStatusLogic(null, $validated);

        $page = DB::transaction(function () use ($validated, $request) {
            $page = Page::create($validated);
            $this->syncTaxonomies($page, $request);

            return $page;
        });

        return redirect()->route('admin.pages.edit', $page->id)->with('success', 'pages.create_success');
    }

    public function edit(Page $page)
    {
        return Inertia::render("{$this->viewPath}/Edit", $this->getEditProps($page, [
            'title', 'slug', 'meta_title', 'meta_description', 'og_image'
        ]));
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $validated = $request->validated();
        $this->assertOptimisticLock($page, $request);

        $this->applyStatusLogic($page, $validated);

        DB::transaction(function () use ($page, $validated, $request) {
            $this->saveRevision($page);
            $page->update($validated);
            $this->syncTaxonomies($page, $request);
        });

        return redirect()->back()->with('success', 'pages.update_success');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->back()->with('success', 'pages.delete_success');
    }
}
