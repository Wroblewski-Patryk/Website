<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThemeController extends Controller
{
    private function getThemeConfig()
    {
        return Setting::firstOrCreate(
        ['key' => 'theme_config'],
        ['value' => [
                'globals' => [
                    'colors' => [
                        'primary' => '#570df8',
                        'secondary' => '#f000b8',
                        'accent' => '#37cdbe',
                        'neutral' => '#3d4451',
                        'base-100' => '#ffffff',
                        'info' => '#3abff8',
                        'success' => '#36d399',
                        'warning' => '#fbbd23',
                        'error' => '#f87272',
                    ],
                    'fonts' => [
                        'heading' => 'Inter',
                        'body' => 'Inter'
                    ],
                    'borderRadius' => [
                        'box' => '1rem',
                        'btn' => '0.5rem',
                        'badge' => '1.9rem',
                    ],
                    'layout' => [
                        'maxWidth' => '1280px',
                    ]
                ],
                'block_defaults' => []
            ]]
        )->value;
    }

    public function colors()
    {
        return Inertia::render('Admin/ThemeConfigurator/Colors', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function fonts()
    {
        return Inertia::render('Admin/ThemeConfigurator/Fonts', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function sizes()
    {
        return Inertia::render('Admin/ThemeConfigurator/Sizes', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function blocks()
    {
        return Inertia::render('Admin/ThemeConfigurator/Blocks', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'globals' => 'required|array',
            'block_defaults' => 'required|array',
        ]);

        Setting::updateOrCreate(
        ['key' => 'theme_config'],
        ['value' => $validated]
        );

        return redirect()->back()->with('success', 'Theme configuration updated successfully.');
    }
}
