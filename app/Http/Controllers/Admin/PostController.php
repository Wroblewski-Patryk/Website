<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends BaseAdminContentController
{
    use HandlePublishableStatus;

    protected string $modelClass = Post::class;
    protected string $viewPath = 'Admin/Posts';

    public function index(Request $request)
    {
        return Inertia::render("{$this->viewPath}/Index", [
            'posts' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
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

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules(), [
            'excerpt' => 'nullable|array',
            'excerpt.*' => 'nullable|string',
            'featured_image' => 'nullable|array',
            'featured_image.*' => 'nullable|string',
        ]));

        $this->applyStatusLogic(null, $validated);

        $post = Post::create($validated);
        $this->syncTaxonomies($post, $request);

        return redirect()->route('admin.posts.edit', $post->id)->with('success', 'posts.create_success');
    }

    public function edit(Post $post)
    {
        return Inertia::render("{$this->viewPath}/Edit", $this->getEditProps($post, [
            'title', 'slug', 'excerpt', 'featured_image', 'meta_title', 'meta_description', 'og_image'
        ]));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules($post), [
            'excerpt' => 'nullable|array',
            'excerpt.*' => 'nullable|string',
            'featured_image' => 'nullable|array',
            'featured_image.*' => 'nullable|string',
        ]));

        $this->applyStatusLogic($post, $validated);

        $this->saveRevision($post);

        $post->update($validated);
        $this->syncTaxonomies($post, $request);

        return redirect()->back()->with('success', 'posts.update_success');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->back()->with('success', 'posts.delete_success');
    }
}
