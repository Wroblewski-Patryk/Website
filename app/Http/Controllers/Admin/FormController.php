<?php

namespace App\Http\Controllers\Admin;

use App\Models\Form;
use App\Traits\HandlePublishableStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormController extends BaseAdminContentController
{
    use HandlePublishableStatus;

    protected string $modelClass = Form::class;
    protected string $viewPath = 'Admin/Forms';
    protected string $module = 'forms';
    protected bool $useTaxonomies = false;
    protected bool $useSlug = false;

    public function index(Request $request)
    {
        $query = $this->getBaseIndexQuery($request);
        
        return Inertia::render("{$this->viewPath}/Index", [
            'forms' => $query->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'formModel' => new $this->modelClass,
        ], $this->getSharedProps()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules(), [
            'settings' => 'nullable|array',
        ]));

        $this->applyStatusLogic(null, $validated);

        $form = $this->modelClass::create($validated);

        return redirect()->route('admin.forms.edit', $form->id)->with('success', 'forms.create_success');
    }

    public function edit(Form $form)
    {
        $formData = $form->toArray();
        $translatable = ['title'];
        
        foreach ($translatable as $field) {
            $formData[$field] = $form->getTranslations($field);
        }

        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'formModel' => $formData,
        ], $this->getSharedProps()));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules($form), [
            'settings' => 'nullable|array',
        ]));

        $this->applyStatusLogic($form, $validated);

        $form->update($validated);

        return redirect()->back()->with('success', 'forms.update_success');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->route('admin.forms.index')->with('success', 'forms.delete_success');
    }
}
