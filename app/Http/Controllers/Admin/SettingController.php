<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        // Fetch all templates to ensure dropdowns have what they need
        $templates = Template::select('id', 'name', 'type')->get();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'templates' => $templates
        ]);
    }

    public function store(Request $request)
    {
        // Don't save internal inertia inertia props
        $data = $request->except(['_method', '_token']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect()->back()->with('message', 'Settings saved successfully');
    }
}
