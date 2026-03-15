<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $query = Language::query();

        $query->when($request->search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        });

        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }
        else {
            $query->orderBy('is_default', 'desc')->orderBy('name', 'asc');
        }

        return Inertia::render('Admin/Languages/Index', [
            'languages_list' => $query->paginate(10)->withQueryString(),
            'is_creating_prop' => $request->has('create')
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.languages.index', ['create' => 1]);
    }

    public function edit(Language $language)
    {
        return Inertia::render('Admin/Languages/Index', [
            'languages_list' => Language::paginate(10), // Simplification or call index logic
            'editing_language_prop' => $language,
            'is_creating_prop' => true
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:languages',
            'name' => 'required|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validated['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($validated);
        return redirect()->back()->with('success', 'languages.create_success');
    }

    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validated['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update($validated);
        return redirect()->back()->with('success', 'languages.update_success');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return redirect()->back()->withErrors(['message', 'languages.delete_default_error']);
        }
        $language->delete();
        return redirect()->back()->with('success', 'languages.delete_success');
    }
}
