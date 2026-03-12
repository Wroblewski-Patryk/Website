<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $query = Form::query();

        $query->when($request->search, function ($q, $search) {
            $locale = app()->getLocale();
            $q->where("title->{$locale}", 'like', "%{$search}%");
        });

        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->sort;
            if ($sort === 'title') {
                $sort .= '->' . app()->getLocale();
            }
            $query->orderBy($sort, $request->direction);
        }
        else {
            $query->latest();
        }

        return Inertia::render('Admin/Forms/Index', [
            'forms' => $query->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Forms/Edit', [
            'formModel' => new Form(),
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
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        $formModel = Form::create($validated);

        return redirect()->route('admin.forms.edit', $formModel->id)->with('success', 'forms.create_success');
    }

    public function edit(Form $form)
    {
        $formData = $form->toArray();
        $translatable = ['title'];
        
        foreach ($translatable as $field) {
            $formData[$field] = $form->getTranslations($field);
        }

        return Inertia::render('Admin/Forms/Edit', [
            'formModel' => $formData,
            'templates' => [
                'header' => \App\Models\Template::where('type', 'header')->get(),
                'footer' => \App\Models\Template::where('type', 'footer')->get(),
                'sidebar' => \App\Models\Template::where('type', 'sidebar')->get(),
                'page' => \App\Models\Template::where('type', 'page')->get(),
            ],
        ]);
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        $form->update($validated);

        return redirect()->back()->with('success', 'forms.update_success');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->route('admin.forms.index')->with('success', 'forms.delete_success');
    }
}
