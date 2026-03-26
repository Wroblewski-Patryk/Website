<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimationPreset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnimationPresetController extends Controller
{
    public function index(Request $request)
    {
        $query = AnimationPreset::query()
            ->when($request->string('search')->toString(), function ($builder, $search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->latest();

        return Inertia::render('Admin/AnimationPresets/Index', [
            'animation_presets' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/AnimationPresets/Edit', [
            'animation_preset' => new AnimationPreset([
                'definition' => [
                    'trigger' => 'onEnter',
                    'preset' => 'fade-up',
                    'duration' => 0.8,
                    'delay' => 0,
                    'ease' => 'power2.out',
                    'once' => true,
                    'timeline_id' => '',
                    'tween' => [
                        'from' => [],
                        'to' => [],
                    ],
                ],
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        AnimationPreset::create($validated);

        return redirect()->route('admin.animation-presets.index')->with('success', 'Animation preset created.');
    }

    public function edit(AnimationPreset $animationPreset)
    {
        return Inertia::render('Admin/AnimationPresets/Edit', [
            'animation_preset' => $animationPreset,
        ]);
    }

    public function update(Request $request, AnimationPreset $animationPreset)
    {
        $validated = $this->validatePayload($request, $animationPreset->id);
        $animationPreset->update($validated);

        return redirect()->route('admin.animation-presets.index')->with('success', 'Animation preset updated.');
    }

    public function destroy(AnimationPreset $animationPreset)
    {
        $animationPreset->delete();

        return redirect()->route('admin.animation-presets.index')->with('success', 'Animation preset deleted.');
    }

    protected function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:120|alpha_dash|unique:animation_presets,slug,' . (int) $ignoreId,
            'is_active' => 'nullable|boolean',
            'definition' => 'required|array',
            'definition.trigger' => 'required|string|in:onEnter,onLoad,onScroll',
            'definition.preset' => 'required|string|max:64',
            'definition.duration' => 'nullable|numeric|min:0|max:30',
            'definition.delay' => 'nullable|numeric|min:0|max:30',
            'definition.ease' => 'nullable|string|max:64',
            'definition.once' => 'nullable|boolean',
            'definition.timeline_id' => 'nullable|string|max:120',
            'definition.tween' => 'nullable|array',
            'definition.tween.from' => 'nullable|array',
            'definition.tween.to' => 'nullable|array',
        ]);
    }
}
