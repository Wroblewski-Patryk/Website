<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

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

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $query->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => (new Page())->setAttribute('revisions', []),
            'templates' => [
                'page' => \App\Models\Template::where('type', 'page')->get(),
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
                'sidebar' => \App\Models\Template::where('type', 'sidebar')->get(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'slug' => 'required|array',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
            'sidebar_override_id' => 'nullable|exists:templates,id',
            'template_id' => 'nullable|exists:templates,id',
        ]);

        $page = Page::create($validated);

        return redirect()->route('admin.pages.edit', $page->id)->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page->load('revisions'),
            'templates' => [
                'page' => \App\Models\Template::where('type', 'page')->get(),
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
                'sidebar' => \App\Models\Template::where('type', 'sidebar')->get(),
            ],
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'slug' => 'required|array',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
            'sidebar_override_id' => 'nullable|exists:templates,id',
            'template_id' => 'nullable|exists:templates,id',
        ]);

        // Store revision of OLD content before update
        if ($page->content) {
            $page->revisions()->create([
                'content' => $page->content,
                'user_id' => auth()->id(),
            ]);
        }

        $page->update($validated);

        return redirect()->back()->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->back()->with('message', 'Page deleted successfully');
    }
}
