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
            $q->where('title', 'like', "%{$search}%");
        });

        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
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
            'title' => 'required|string|max:255',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        $formModel = Form::create($validated);

        return redirect()->route('admin.forms.edit', $formModel->id)->with('success', 'Form created successfully.');
    }

    public function edit(Form $form)
    {
        return Inertia::render('Admin/Forms/Edit', [
            'formModel' => $form,
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
            'title' => 'required|string|max:255',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        $form->update($validated);

        return redirect()->back()->with('success', 'Form updated successfully.');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }
}
