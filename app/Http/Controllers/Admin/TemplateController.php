<?php

namespace App\Http\Controllers\Admin;

use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplateController extends BaseAdminContentController
{
    protected string $modelClass = Template::class;
    protected string $viewPath = 'Admin/Templates';
    protected bool $useTaxonomies = false;
    protected bool $useSlug = false;

    public function index(Request $request)
    {
        $query = $this->getBaseIndexQuery($request);

        // Custom search for templates: also search by type
        $query->when($request->search, function ($q, $search) {
            $q->orWhere('type', 'like', "%{$search}%");
        });

        return Inertia::render("{$this->viewPath}/Index", [
            'templates' => $query->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'template' => new $this->modelClass,
        ], $this->getSharedProps()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules(), [
            'type' => 'required|in:header,footer,sidebar,page',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]));

        $template = $this->modelClass::create($validated);

        return redirect()->route('admin.templates.edit', $template->id)->with('success', 'templates.create_success');
    }

    public function edit(Template $template)
    {
        $templateData = $template->toArray();
        $translatable = ['title', 'meta_title', 'meta_description', 'og_image'];
        
        foreach ($translatable as $field) {
            $templateData[$field] = $template->getTranslations($field);
        }

        // Include revisions
        $templateData['revisions'] = $template->revisions;

        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'template' => $templateData,
        ], $this->getSharedProps()));
    }

    public function update(Request $request, Template $template)
    {
        $validated = $request->validate(array_merge($this->getBaseValidationRules($template), [
            'type' => 'required|in:header,footer,sidebar,page',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]));

        $this->saveRevision($template);

        $template->update($validated);

        return redirect()->back()->with('success', 'templates.update_success');
    }

    public function destroy(Template $template)
    {
        if ($template->is_system) {
            return redirect()->back()->with('error', 'templates.delete_system_error');
        }

        $template->delete();
        return redirect()->back()->with('success', 'templates.delete_success');
    }
}
