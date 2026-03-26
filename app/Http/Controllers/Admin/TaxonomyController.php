<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        $locales = $this->resolveActiveLocales();
        $rules = [
            'type' => 'required|string',
            'module' => 'required|string',
            'order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
        ];

        foreach ($locales as $locale) {
            $rules["title.$locale"] = 'nullable|string|max:255';
            $rules["slug.$locale"] = 'nullable|string|max:255';
            $rules["description.$locale"] = 'nullable|string';
        }

        $validated = $request->validate($rules);
        $primaryLocale = $this->resolveDefaultLocale($locales);
        $firstFilledTitle = collect($validated['title'] ?? [])->first(fn ($value) => filled($value));

        if (!filled($firstFilledTitle)) {
            throw ValidationException::withMessages([
                "title.$primaryLocale" => 'At least one title translation is required.',
            ]);
        }

        foreach ($locales as $locale) {
            if (empty($validated['title'][$locale])) {
                $validated['title'][$locale] = $firstFilledTitle;
            }
        }

        // Generate slugs if missing
        foreach ($locales as $locale) {
            if (empty($validated['slug'][$locale])) {
                $baseTitle = $validated['title'][$locale] ?: $firstFilledTitle;
                $validated['slug'][$locale] = Str::slug($baseTitle);
            }
        }

        Taxonomy::create($validated);

        return back()->with('success', 'admin.taxonomy.created');
    }

    public function update(Request $request, Taxonomy $taxonomy)
    {
        $locales = $this->resolveActiveLocales();
        $rules = [
            'type' => 'required|string',
            'module' => 'required|string',
            'order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
        ];

        foreach ($locales as $locale) {
            $rules["title.$locale"] = 'nullable|string|max:255';
            $rules["slug.$locale"] = 'nullable|string|max:255';
            $rules["description.$locale"] = 'nullable|string';
        }

        $validated = $request->validate($rules);
        $primaryLocale = $this->resolveDefaultLocale($locales);
        $firstFilledTitle = collect($validated['title'] ?? [])->first(fn ($value) => filled($value));

        if (!filled($firstFilledTitle)) {
            throw ValidationException::withMessages([
                "title.$primaryLocale" => 'At least one title translation is required.',
            ]);
        }

        foreach ($locales as $locale) {
            if (empty($validated['title'][$locale])) {
                $validated['title'][$locale] = $firstFilledTitle;
            }
            if (empty($validated['slug'][$locale])) {
                $baseTitle = $validated['title'][$locale] ?: $firstFilledTitle;
                $validated['slug'][$locale] = Str::slug($baseTitle);
            }
        }

        $taxonomy->update($validated);

        return back()->with('success', 'admin.taxonomy.updated');
    }

    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->delete();
        return back()->with('success', 'admin.taxonomy.deleted');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:taxonomies,id'],
            'type' => ['nullable', 'string'],
            'module' => ['nullable', 'string'],
        ]);

        $query = Taxonomy::query()->whereIn('id', $validated['ids']);

        if (filled($validated['type'] ?? null)) {
            $query->where('type', $validated['type']);
        }

        if (filled($validated['module'] ?? null)) {
            $query->where('module', $validated['module']);
        }

        $count = (clone $query)->count();

        if ($count === 0) {
            return response()->json([
                'message' => 'No matching taxonomy rows found for selected ids.',
            ], 422);
        }

        $query->delete();

        return response()->json([
            'message' => 'Taxonomies updated successfully.',
            'data' => [
                'action' => $validated['action'],
                'count' => $count,
                'ids' => $validated['ids'],
            ],
        ]);
    }

    private function resolveActiveLocales(): array
    {
        $codes = Language::query()
            ->where('is_active', true)
            ->pluck('code')
            ->filter()
            ->values()
            ->all();

        if (!empty($codes)) {
            return $codes;
        }

        return array_values(array_unique(array_filter([
            app()->getLocale(),
            config('app.fallback_locale'),
        ])));
    }

    private function resolveDefaultLocale(array $activeLocales): string
    {
        $default = Language::query()
            ->where('is_default', true)
            ->value('code');

        if ($default && in_array($default, $activeLocales, true)) {
            return $default;
        }

        return $activeLocales[0] ?? app()->getLocale() ?? 'en';
    }
}
