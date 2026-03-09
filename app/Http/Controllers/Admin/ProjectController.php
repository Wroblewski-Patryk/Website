<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sq) use ($search) {
                    $sq->where('title->pl', 'like', "%{$search}%")
                        ->orWhere('title->en', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                }
                );
            });

        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->sort;
            if (in_array($sort, ['title'])) {
                $sort .= '->pl'; // Default to PL for sorting translatable fields
            }
            $query->orderBy($sort, $request->direction);
        }
        else {
            $query->orderBy('order');
        }

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $query->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        $project = new Project();
        $project->setAttribute('title', ['pl' => '', 'en' => '']);
        $project->setAttribute('description', ['pl' => '', 'en' => '']);

        return Inertia::render('Admin/Projects/Edit', [
            'project' => $project,
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
            'title.pl' => 'required|string',
            'description' => 'nullable|array',
            'slug' => 'nullable|string|unique:projects',
            'desktop_image' => 'nullable|string',
            'mobile_image' => 'nullable|string',
            'url' => 'nullable|string',
            'category' => 'nullable|string',
            'order' => 'integer',
            'content' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            // SEO Fields
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'og_image' => 'nullable|string',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
        ]);

        // Map flat strings to translatable arrays (default to pl)
        foreach (['meta_title', 'meta_description', 'og_image'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = ['pl' => $validated[$field]];
            }
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']['pl']);
        }

        $project = Project::create($validated);
        return redirect()->route('admin.projects.edit', $project->id)->with('message', 'Project created');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Admin/Projects/Edit', [
            'project' => $project,
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
                'sidebar' => \App\Models\Template::where('type', 'sidebar')->get(),
                'page' => \App\Models\Template::where('type', 'page')->get(),
            ],
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'description' => 'nullable|array',
            'slug' => 'required|string|unique:projects,slug,' . $project->id,
            'desktop_image' => 'nullable|string',
            'mobile_image' => 'nullable|string',
            'url' => 'nullable|string',
            'category' => 'nullable|string',
            'order' => 'integer',
            'content' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            // SEO Fields
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'og_image' => 'nullable|string',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
        ]);

        // Map flat strings to translatable arrays (default to pl)
        foreach (['meta_title', 'meta_description', 'og_image'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = ['pl' => $validated[$field]];
            }
        }

        $project->update($validated);
        return redirect()->back()->with('message', 'Project updated');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('message', 'Project deleted');
    }
}
