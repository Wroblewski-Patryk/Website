<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TaxonomyController extends Controller
{
    public function index(Request $request)
    {
        $routeName = $request->route()->getName();
        $segments = explode('.', $routeName);
        
        // Expected format: admin.{module}.{type}s.index
        // e.g. admin.posts.categories.index
        $module = $segments[1] ?? 'posts';
        $typeShort = $segments[2] ?? 'categories';
        $type = Str::singular($typeShort); // categories -> category, tags -> tag

        $taxonomies = Taxonomy::where('type', $type)
            ->where('module', $module)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Taxonomy/Index', [
            'taxonomies' => $taxonomies,
            'currentType' => $type,
            'currentModule' => $module
        ]);
    }

    public function store(Request $request)
    {
        $locales = config('app.locales', ['pl', 'en']);
        $rules = [
            'type' => 'required|string',
            'module' => 'required|string',
            'order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
        ];

        foreach ($locales as $locale) {
            $rules["title.$locale"] = 'required|string|max:255';
            $rules["slug.$locale"] = 'nullable|string|max:255';
            $rules["description.$locale"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Generate slugs if missing
        foreach ($locales as $locale) {
            if (empty($validated['slug'][$locale])) {
                $validated['slug'][$locale] = Str::slug($validated['title'][$locale]);
            }
        }

        Taxonomy::create($validated);

        return back()->with('success', 'admin.taxonomy.created');
    }

    public function update(Request $request, Taxonomy $taxonomy)
    {
        $locales = config('app.locales', ['pl', 'en']);
        $rules = [
            'type' => 'required|string',
            'module' => 'required|string',
            'order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
        ];

        foreach ($locales as $locale) {
            $rules["title.$locale"] = 'required|string|max:255';
            $rules["slug.$locale"] = 'required|string|max:255';
            $rules["description.$locale"] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $taxonomy->update($validated);

        return back()->with('success', 'admin.taxonomy.updated');
    }

    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->delete();
        return back()->with('success', 'admin.taxonomy.deleted');
    }
}
