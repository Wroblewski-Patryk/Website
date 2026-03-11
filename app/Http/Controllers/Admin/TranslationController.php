<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $query = Translation::query();

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('key', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");

                try {
                    $activeCodes = \App\Models\Language::where('is_active', true)->pluck('code');
                    foreach ($activeCodes as $code) {
                        $sub->orWhere("text->{$code}", 'like', "%{$search}%");
                    }
                } catch (\Exception $e) {
                    // Fallback if DB doesn't support JSON search or languages table missing
                }
            });
        });

        if ($request->filled('sort') && $request->filled('direction')) {
            $sort = $request->sort;
            if (str_contains($sort, '.')) {
                $sort = str_replace('.', '->', $sort);
            }
            $query->orderBy($sort, $request->direction);
        } else {
            $query->orderBy('group')->orderBy('key');
        }

        return Inertia::render('Admin/Translations/Index', [
            'translations_list' => $query->paginate(20)->withQueryString()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string',
            'key' => 'required|string|unique:translations,key,NULL,id,group,' . $request->group,
            'text' => 'required|array',
        ]);


        Translation::create($validated);

        return redirect()->back()->with('success', 'Translation created.');
    }

    public function update(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'text' => 'required|array',
        ]);

        $translation->update($validated);

        return redirect()->back()->with('success', 'Translation updated.');
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();
        return redirect()->back()->with('success', 'Translation deleted.');
    }
}
