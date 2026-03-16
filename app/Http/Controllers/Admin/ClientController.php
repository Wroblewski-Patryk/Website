<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends BaseAdminContentController
{
    protected string $modelClass = Client::class;
    protected string $viewPath = 'Admin/Clients';
    protected bool $useTaxonomies = false;
    protected string $module = 'clients';

    public function index(Request $request)
    {
        return Inertia::render("{$this->viewPath}/Index", [
            'clients' => $this->getBaseIndexQuery($request)->paginate(10)->withQueryString()
        ]);
    }

    public function create()
    {
        $client = new Client();
        $locales = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();
        $emptyLocales = array_fill_keys($locales, '');
        $client->setAttribute('title', $emptyLocales);
        $client->setAttribute('slug', $emptyLocales);
        $client->setAttribute('description', $emptyLocales);

        return Inertia::render("{$this->viewPath}/Edit", array_merge([
            'client' => $client,
        ], $this->getSharedProps()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'logo' => 'nullable|string',
            'website_url' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $client = Client::create($validated);

        return redirect()->route('admin.projects.clients.edit', $client->id)->with('success', 'admin.clients.create_success');
    }

    public function edit(Client $client)
    {
        return Inertia::render("{$this->viewPath}/Edit", $this->getEditProps($client, [
            'title', 'slug', 'description'
        ]));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string',
            'slug' => 'required|array',
            'slug.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'logo' => 'nullable|string',
            'website_url' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);

        return redirect()->back()->with('success', 'admin.clients.update_success');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->back()->with('success', 'admin.clients.delete_success');
    }
}
