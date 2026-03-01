<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Projects/Index', [
            'projects' => Project::orderBy('order')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Projects/Edit', [
            'project' => null
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
            'order' => 'integer'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']['pl']);
        }

        Project::create($validated);
        return redirect()->route('admin.projects.index')->with('message', 'Project created');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Admin/Projects/Edit', [
            'project' => $project
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
            'order' => 'integer'
        ]);

        $project->update($validated);
        return redirect()->route('admin.projects.index')->with('message', 'Project updated');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('message', 'Project deleted');
    }
}
