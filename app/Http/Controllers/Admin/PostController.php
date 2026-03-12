<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sq) use ($search) {
                    $locale = app()->getLocale();
                    $sq->where("title->{$locale}", 'like', "%{$search}%")
                        ->orWhere("slug->{$locale}", 'like', "%{$search}%");
                }
                );
            });

        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->sort;
            if (in_array($sort, ['title', 'slug'])) {
                $sort .= '->' . app()->getLocale();
            }
            $query->orderBy($sort, $request->direction);
        }
        else {
            $query->latest();
        }

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $query->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        $post = new Post();
        $post->setAttribute('title', ['pl' => '', 'en' => '']);
        $post->setAttribute('slug', ['pl' => '', 'en' => '']);
        $post->setAttribute('excerpt', ['pl' => '', 'en' => '']);
        $post->setAttribute('content', []);
        $post->setAttribute('revisions', []);

        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post,
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
                'sidebar' => \App\Models\Template::where('type', 'sidebar')->get(),
                'page' => \App\Models\Template::where('type', 'page')->get(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'excerpt' => 'nullable|array',
            'excerpt.*' => 'nullable|string',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|array',
            'featured_image.*' => 'nullable|string',
            // SEO Fields
            'meta_title' => 'nullable|array',
            'meta_title.*' => 'nullable|string',
            'meta_description' => 'nullable|array',
            'meta_description.*' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'og_image' => 'nullable|array',
            'og_image.*' => 'nullable|string',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
        ]);



        $post = Post::create($validated);

        return redirect()->route('admin.posts.edit', $post->id)->with('success', 'posts.create_success');
    }

    public function edit(Post $post)
    {
        $postData = $post->load('revisions')->toArray();
        $translatable = ['title', 'slug', 'excerpt', 'featured_image', 'meta_title', 'meta_description', 'og_image'];
        
        foreach ($translatable as $field) {
            $postData[$field] = $post->getTranslations($field);
        }

        return Inertia::render('Admin/Posts/Edit', [
            'post' => $postData,
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
                'sidebar' => \App\Models\Template::where('type', 'sidebar')->get(),
                'page' => \App\Models\Template::where('type', 'page')->get(),
            ],
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'excerpt' => 'nullable|array',
            'excerpt.*' => 'nullable|string',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|array',
            'featured_image.*' => 'nullable|string',
            // SEO Fields
            'meta_title' => 'nullable|array',
            'meta_title.*' => 'nullable|string',
            'meta_description' => 'nullable|array',
            'meta_description.*' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'og_image' => 'nullable|array',
            'og_image.*' => 'nullable|string',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
        ]);



        // Store revision
        if ($post->content) {
            $post->revisions()->create([
                'content' => $post->content,
                'user_id' => auth()->id(),
            ]);
        }

        $post->update($validated);

        return redirect()->back()->with('success', 'posts.update_success');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->back()->with('success', 'posts.delete_success');
    }
}
