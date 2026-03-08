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
                    $sq->where('title->pl', 'like', "%{$search}%")
                        ->orWhere('title->en', 'like', "%{$search}%")
                        ->orWhere('slug->pl', 'like', "%{$search}%")
                        ->orWhere('slug->en', 'like', "%{$search}%");
                }
                );
            });

        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->sort;
            if (in_array($sort, ['title', 'slug'])) {
                $sort .= '->pl'; // Default to PL for sorting translatable fields
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
            'slug' => 'required|array',
            'excerpt' => 'nullable|array',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|array',
        ]);

        $post = Post::create($validated);

        return redirect()->route('admin.posts.edit', $post->id)->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post->load('revisions'),
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
            'slug' => 'required|array',
            'excerpt' => 'nullable|array',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|array',
        ]);

        // Store revision
        if ($post->content) {
            $post->revisions()->create([
                'content' => $post->content,
                'user_id' => auth()->id(),
            ]);
        }

        $post->update($validated);

        return redirect()->back()->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->back()->with('message', 'Post deleted successfully');
    }
}
