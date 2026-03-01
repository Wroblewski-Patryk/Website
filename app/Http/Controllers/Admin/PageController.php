<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => (new Page())->setAttribute('revisions', []),
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
            'content' => 'required|array',
            'is_published' => 'boolean',
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
        ]);

        Page::create($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page->load('revisions'),
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
            ]
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'slug' => 'required|array',
            'content' => 'required|array',
            'is_published' => 'boolean',
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
        ]);

        // Store revision of OLD content before update
        if ($page->content) {
            $page->revisions()->create([
                'content' => $page->content,
                'user_id' => auth()->id(),
            ]);
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->back()->with('message', 'Page deleted successfully');
    }
}
