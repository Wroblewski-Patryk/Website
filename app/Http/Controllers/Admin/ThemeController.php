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
        $defaultConfig = [
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
                    'sans' => 'Inter',
                    'serif' => 'Merriweather',
                    'mono' => 'Fira Code',
                ],
                'borderRadius' => [
                    'box' => '1rem',
                    'btn' => '0.5rem',
                    'badge' => '1.9rem',
                ],
                'layout' => [
                    'maxWidth' => '1280px',
                ],
                'advanced' => [
                    // Core Font Stacks
                    'font-sans' => 'ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    'font-serif' => 'ui-serif, Georgia, Cambria, "Times New Roman", Times, serif',
                    'font-mono' => 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace',

                    // Typography
                    'text-xs' => '0.75rem',
                    'text-sm' => '0.875rem',
                    'text-base' => '1rem',
                    'text-lg' => '1.125rem',
                    'text-xl' => '1.25rem',
                    'text-2xl' => '1.5rem',
                    'text-3xl' => '1.875rem',
                    'text-4xl' => '2.25rem',
                    'text-5xl' => '3rem',
                    'text-6xl' => '3.75rem',
                    'text-7xl' => '4.5rem',
                    'font-weight-thin' => '100',
                    'font-weight-extralight' => '200',
                    'font-weight-light' => '300',
                    'font-weight-normal' => '400',
                    'font-weight-medium' => '500',
                    'font-weight-semibold' => '600',
                    'font-weight-bold' => '700',
                    'font-weight-extrabold' => '800',
                    'font-weight-black' => '900',
                    'line-height-none' => '1',
                    'line-height-tight' => '1.25',
                    'line-height-snug' => '1.375',
                    'line-height-normal' => '1.5',
                    'line-height-relaxed' => '1.625',
                    'line-height-loose' => '2',
                    'tracking-tighter' => '-0.05em',
                    'tracking-tight' => '-0.025em',
                    'tracking-normal' => '0em',
                    'tracking-wide' => '0.025em',
                    'tracking-wider' => '0.05em',
                    'tracking-widest' => '0.1em',

                    // Spacing
                    'spacing' => '0.25rem', // Base spacing scale multiplier

                    // Visuals
                    'radius-sm' => 'calc(var(--radius) - 4px)',
                    'radius-md' => 'calc(var(--radius) - 2px)',
                    'radius-lg' => 'var(--radius)',
                    'radius-xl' => 'calc(var(--radius) + 4px)',
                    'radius-2xl' => 'calc(var(--radius) + 8px)',
                    'radius-3xl' => 'calc(var(--radius) + 12px)',
                    'radius-full' => '9999px',

                    'shadow-xs' => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                    'shadow-sm' => '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                    'shadow-md' => '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                    'shadow-lg' => '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
                    'shadow-xl' => '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
                    'shadow-2xl' => '0 25px 50px -12px rgb(0 0 0 / 0.25)',
                    'shadow-inner' => 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)',

                    'blur-sm' => '4px',
                    'blur' => '8px',
                    'blur-md' => '12px',
                    'blur-lg' => '16px',
                    'blur-xl' => '24px',
                    'blur-2xl' => '40px',
                    'blur-3xl' => '64px',

                    'perspective-dramatic' => '100px',
                    'perspective-near' => '300px',
                    'perspective-normal' => '500px',
                    'perspective-mid' => '800px',
                    'perspective-distant' => '1200px',
                ]
            ],
            'block_defaults' => []
        ];

        $setting = Setting::firstOrCreate(
        ['key' => 'theme_config'],
        ['value' => $defaultConfig]
        );

        $value = $setting->value;

        // Ensure advanced settings exist for older database records
        if (!isset($value['globals']['advanced'])) {
            $value['globals']['advanced'] = $defaultConfig['globals']['advanced'];
        }
        else {
            // Merge missing keys so new additions like 'font-sans' appear
            $value['globals']['advanced'] = array_merge($defaultConfig['globals']['advanced'], $value['globals']['advanced']);
        }

        // Ensure new fonts structure exists
        if (!isset($value['globals']['fonts']['sans'])) {
            $value['globals']['fonts'] = array_merge($defaultConfig['globals']['fonts'], $value['globals']['fonts'] ?? []);
        }

        return $value;
    }

    public function colors()
    {
        return Inertia::render('Admin/Theme/Colors', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function fonts()
    {
        return Inertia::render('Admin/Theme/Fonts', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function sizes()
    {
        return Inertia::render('Admin/Theme/Sizes', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function blocks()
    {
        return Inertia::render('Admin/Theme/Blocks', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function typography()
    {
        return Inertia::render('Admin/Theme/Typography', [
            'themeConfig' => $this->getThemeConfig()
        ]);
    }

    public function effects()
    {
        return Inertia::render('Admin/Theme/Effects', [
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

        return redirect()->back()->with('success', 'theme.update_success');
    }
}
