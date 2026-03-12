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
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
            'sidebar_override_id' => 'nullable|exists:templates,id',
            'template_id' => 'nullable|exists:templates,id',
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



        $page = Page::create($validated);

        return redirect()->route('admin.pages.edit', $page->id)->with('success', 'pages.create_success');
    }

    public function edit(Page $page)
    {
        $page->getTranslations(); // This might prime them if using specific traits, but let's be explicit
        
        $pageData = $page->load('revisions')->toArray();
        $translatable = ['title', 'slug', 'meta_title', 'meta_description', 'og_image'];
        
        foreach ($translatable as $field) {
            $pageData[$field] = $page->getTranslations($field);
        }

        return Inertia::render('Admin/Pages/Edit', [
            'page' => $pageData,
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
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            'header_override_id' => 'nullable|exists:templates,id',
            'footer_override_id' => 'nullable|exists:templates,id',
            'sidebar_override_id' => 'nullable|exists:templates,id',
            'template_id' => 'nullable|exists:templates,id',
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



        // Store revision of OLD content before update
        if ($page->content) {
            $page->revisions()->create([
                'content' => $page->content,
                'user_id' => auth()->id(),
            ]);
        }

        $page->update($validated);

        return redirect()->back()->with('success', 'pages.update_success');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->back()->with('success', 'pages.delete_success');
    }
}
