<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => (new Post())->setAttribute('revisions', []),
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'slug' => 'required|array',
            'excerpt' => 'nullable|array',
            'content' => 'required|array',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|array',
        ]);

        Post::create($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post->load('revisions'),
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
            ]
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'slug' => 'required|array',
            'excerpt' => 'nullable|array',
            'content' => 'required|array',
            'is_published' => 'boolean',
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

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->back()->with('message', 'Post deleted successfully');
    }
}
