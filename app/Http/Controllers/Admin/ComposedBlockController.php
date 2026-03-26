<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComposedBlock;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ComposedBlockController extends Controller
{
    public function index(Request $request)
    {
        $query = ComposedBlock::query()
            ->when($request->string('search')->toString(), function ($builder, $search) {
                $builder->where('slug', 'like', "%{$search}%")
                    ->orWhere('title->en', 'like', "%{$search}%")
                    ->orWhere('title->pl', 'like', "%{$search}%");
            })
            ->latest();

        return Inertia::render('Admin/Blocks', [
            'composed_blocks' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blocks/Edit', [
            'composed_block' => new ComposedBlock(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:composed_blocks,slug',
            'content' => 'required|array',
            'settings' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $block = ComposedBlock::create($validated);

        return redirect()->route($this->resolveRoutePrefix($request) . '.edit', $block)->with('success', 'composed_blocks.create_success');
    }

    public function edit(ComposedBlock $composedBlock)
    {
        return Inertia::render('Admin/Blocks/Edit', [
            'composed_block' => $composedBlock->load('revisions'),
        ]);
    }

    public function update(Request $request, ComposedBlock $composedBlock)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.*' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:composed_blocks,slug,' . $composedBlock->id,
            'content' => 'required|array',
            'settings' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'optimistic_lock' => 'nullable|date',
        ]);

        $this->assertOptimisticLock($composedBlock, $request);

        DB::transaction(function () use ($composedBlock, $validated) {
            if (!empty($composedBlock->content)) {
                $composedBlock->revisions()->create([
                    'content' => $composedBlock->content,
                    'user_id' => auth()->id(),
                ]);
            }

            unset($validated['optimistic_lock']);
            $composedBlock->update($validated);
        });

        return redirect()->route($this->resolveRoutePrefix($request) . '.edit', $composedBlock)->with('success', 'composed_blocks.update_success');
    }

    public function destroy(Request $request, ComposedBlock $composedBlock)
    {
        $composedBlock->delete();

        return redirect()->route($this->resolveRoutePrefix($request) . '.index')->with('success', 'composed_blocks.delete_success');
    }

    protected function assertOptimisticLock(ComposedBlock $composedBlock, Request $request): void
    {
        $lock = $request->input('optimistic_lock');
        if (!$lock) {
            return;
        }

        $updatedAt = $composedBlock->getAttribute('updated_at');
        if (!$updatedAt instanceof Carbon) {
            return;
        }

        try {
            $clientTimestamp = Carbon::parse($lock);
        } catch (\Throwable) {
            return;
        }

        if ($updatedAt->gt($clientTimestamp)) {
            throw ValidationException::withMessages([
                'optimistic_lock' => 'This composed block was updated by another user. Please refresh and retry.',
            ]);
        }
    }

    private function resolveRoutePrefix(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        if (str_starts_with($routeName, 'admin.blocks.')) {
            return 'admin.blocks';
        }

        return 'admin.composed-blocks';
    }
}
