<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Post\StorePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use App\Models\Post;
use App\Models\Revision;
use App\Services\AdminContentPersistenceService;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PostController extends BaseAdminContentController
{
    use HandlePublishableStatus;

    protected string $modelClass = Post::class;
    protected string $viewPath = 'Admin/Posts';
    protected string $module = 'posts';

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Post::class);

        return Inertia::render("{$this->viewPath}/Index", [
            'posts' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Post::class);

        $post = new Post();
        $locales = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();
        $emptyLocales = array_fill_keys($locales, '');
        $post->setAttribute('title', $emptyLocales);
        $post->setAttribute('slug', $emptyLocales);
        $post->setAttribute('excerpt', $emptyLocales);
        $post->setAttribute('content', []);
        $post->setAttribute('revisions', []);

        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'post' => $post,
        ], $this->getSharedProps()));
    }

    public function store(StorePostRequest $request, AdminContentPersistenceService $persistence)
    {
        Gate::authorize('create', Post::class);

        $validated = $request->validated();

        $this->applyStatusLogic(null, $validated);

        $post = $persistence->createWithTaxonomies(
            Post::class,
            $validated,
            $request,
            fn (Post $post, Request $request) => $this->syncTaxonomies($post, $request),
        );
        /** @var Post $post */

        return redirect()->route('admin.posts.edit', $post->id)->with('success', 'posts.create_success');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return Inertia::render("{$this->viewPath}/Edit", $this->getEditProps($post, [
            'title', 'slug', 'excerpt', 'featured_image', 'meta_title', 'meta_description', 'og_image'
        ]));
    }

    public function update(UpdatePostRequest $request, Post $post, AdminContentPersistenceService $persistence)
    {
        Gate::authorize('update', $post);

        $validated = $request->validated();
        $this->assertOptimisticLock($post, $request);

        $this->applyStatusLogic($post, $validated);

        $persistence->updateWithRevisionAndTaxonomies(
            $post,
            $validated,
            $request,
            fn (Post $post) => $this->saveRevision($post),
            fn (Post $post, Request $request) => $this->syncTaxonomies($post, $request),
        );

        return redirect()->back()->with('success', 'posts.update_success');
    }

    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();
        return redirect()->back()->with('success', 'posts.delete_success');
    }

    public function restoreRevision(Request $request, Post $post, Revision $revision)
    {
        Gate::authorize('update', $post);

        $this->restoreRevisionContent($post, $revision, $request);

        return redirect()->back()->with('success', 'posts.update_success');
    }

    public function bulkAction(Request $request)
    {
        Gate::authorize('viewAny', Post::class);

        return $this->handleBulkAction($request);
    }
}
