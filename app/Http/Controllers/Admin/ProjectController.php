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
                    $locale = app()->getLocale();
                    $sq->where("title->{$locale}", 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                }
                );
            });

        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->sort;
            if (in_array($sort, ['title'])) {
                $sort .= '->' . app()->getLocale();
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
            'title.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'slug' => 'nullable|array',
            'slug.*' => 'nullable|string',
            'desktop_image' => 'nullable|string',
            'mobile_image' => 'nullable|string',
            'url' => 'nullable|string',
            'category' => 'nullable|string',
            'order' => 'integer',
            'content' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
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

        if (empty($validated['slug'])) {
            $validated['slug'] = [app()->getLocale() => Str::slug($validated['title'][app()->getLocale()] ?? $validated['title']['pl'] ?? '')];
        }

        $project = Project::create($validated);
        return redirect()->route('admin.projects.edit', $project->id)->with('success', 'projects.create_success');
    }

    public function edit(Project $project)
    {
        $projectData = $project->toArray();
        $translatable = ['title', 'slug', 'description', 'meta_title', 'meta_description', 'og_image'];
        
        foreach ($translatable as $field) {
            $projectData[$field] = $project->getTranslations($field);
        }

        return Inertia::render('Admin/Projects/Edit', [
            'project' => $projectData,
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
            'title.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'desktop_image' => 'nullable|string',
            'mobile_image' => 'nullable|string',
            'url' => 'nullable|string',
            'category' => 'nullable|string',
            'order' => 'integer',
            'content' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
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



        $project->update($validated);
        return redirect()->back()->with('success', 'projects.update_success');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'projects.delete_success');
    }
}
