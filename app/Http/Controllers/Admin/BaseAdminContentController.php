<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Revision;
use App\Models\Taxonomy;
use App\Models\Template;
use App\Rules\UniqueLocalizedSlug;
use App\Services\BlockBuilder\ModuleBlockRegistry;
use App\Support\AuditLogger;
use App\Support\CanonicalUrlNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

abstract class BaseAdminContentController extends Controller
{
    /**
     * The model class name.
     */
    protected string $modelClass;

    /**
     * The Inertia view base path.
     */
    protected string $viewPath;

    /**
     * Whether this content type uses taxonomies.
     */
    protected bool $useTaxonomies = true;

    /**
     * The module name for taxonomies.
     */
    protected string $module = 'posts';

    /**
     * Whether this content type uses slugs.
     */
    protected bool $useSlug = true;

    /**
     * Shared logic for listing content.
     */
    protected function getBaseIndexQuery(Request $request)
    {
        $query = $this->modelClass::query();
        $locale = app()->getLocale();

        $query->when($request->search, function ($q, $search) use ($locale) {
            $this->applyLocaleJsonLikeSearch($q, (string) $search, $locale);
        });

        if ($request->has('sort') && $request->has('direction')) {
            $sort = $request->sort;
            if (in_array($sort, ['title', 'slug'])) {
                if ($sort === 'slug' && !$this->useSlug) {
                    $query->latest();
                    return $query;
                }
                $sort .= '->' . $locale;
            }
            $query->orderBy($sort, $request->direction);
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Apply locale-scoped translated JSON search for index listings.
     */
    protected function applyLocaleJsonLikeSearch($query, string $search, string $locale): void
    {
        $query->where(function ($sq) use ($search, $locale) {
            $sq->where("title->{$locale}", 'like', "%{$search}%");

            if ($this->useSlug) {
                $sq->orWhere("slug->{$locale}", 'like', "%{$search}%");
            }
        });
    }

    /**
     * Shared props for create/edit views.
     */
    protected function getSharedProps(): array
    {
        $props = [
            'templates' => [
                'header' => Template::where('type', 'header')->get(),
                'footer' => Template::where('type', 'footer')->get(),
                'sidebar' => Template::where('type', 'sidebar')->get(),
                'page' => Template::where('type', 'page')->get(),
            ],
            'languages' => Language::where('is_active', true)->get(),
            'moduleCategories' => app(ModuleBlockRegistry::class)->resolveCategories($this->module),
        ];

        if ($this->useTaxonomies) {
            $props['availableTaxonomies'] = Taxonomy::where('module', $this->module ?? 'posts')
                ->orderBy('order')
                ->get();
        }

        return $props;
    }

    /**
     * Synchronize taxonomies for a model.
     */
    protected function syncTaxonomies($model, Request $request)
    {
        if (!$this->useTaxonomies || !method_exists($model, 'taxonomies')) {
            return;
        }
        if ($request->has('taxonomies')) {
            $model->taxonomies()->sync($request->taxonomies);
        }
    }

    /**
     * Save a content revision.
     */
    protected function saveRevision(Model $model): void
    {
        if ($model->content) {
            $model->revisions()->create([
                'content' => $model->content,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Restore model content from a specific revision with safety checks.
     */
    protected function restoreRevisionContent(Model $model, Revision $revision, Request $request): void
    {
        $sameModel = $revision->revisionable_type === $model::class
            && (int) $revision->revisionable_id === (int) $model->getKey();

        if (!$sameModel) {
            abort(404);
        }

        $this->assertOptimisticLock($model, $request);

        $restoredContent = $revision->content;

        \Illuminate\Support\Facades\DB::transaction(function () use ($model, $restoredContent) {
            // Keep a backup snapshot of current content before applying restore.
            $this->saveRevision($model);
            $model->update([
                'content' => $restoredContent,
            ]);
        });
    }

    /**
     * Get taxonomy IDs for a model if supported.
     */
    protected function getModelTaxonomyIds(Model $model): array
    {
        if ($this->useTaxonomies && method_exists($model, 'taxonomies')) {
            return $model->taxonomies->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * Prepare shared props for editing a model.
     */
    protected function getEditProps(Model $model, array $translatableFields): array
    {
        $modelData = $model->load('revisions')->toArray();

        foreach ($translatableFields as $field) {
            $modelData[$field] = $model->getTranslations($field);
        }

        return array_merge([
            $model->getTable() === 'posts' ? 'post' : ($model->getTable() === 'projects' ? 'project' : ($model->getTable() === 'pages' ? 'page' : 'item')) => $modelData,
            'taxonomies' => $this->getModelTaxonomyIds($model),
        ], $this->getSharedProps());
    }

    /**
     * Ensure the incoming write request is not based on stale data.
     */
    protected function assertOptimisticLock(Model $model, Request $request): void
    {
        $lock = $request->input('optimistic_lock');
        if (!$lock) {
            return;
        }

        $updatedAt = $model->getAttribute('updated_at');
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
                'optimistic_lock' => 'This content was updated by another user. Please refresh and retry.',
            ]);
        }
    }

    /**
     * Build shared validation rules.
     */
    protected function getBaseValidationRules(?Model $model = null): array
    {
        $rules = [
            'title' => 'required|array',
            'title.*' => 'nullable|string',
        ];

        if ($this->useSlug) {
            $rules['slug'] = 'required|array';
            $rules['slug.*'] = [
                'nullable',
                'string',
                new UniqueLocalizedSlug($this->modelClass, $model?->id)
            ];
        }

        $rules = array_merge($rules, [
            'content' => 'required|array',
            'status' => 'nullable|string',
            'published_at' => 'nullable|date',
            // SEO
            'meta_title' => 'nullable|array',
            'meta_title.*' => 'nullable|string',
            'meta_description' => 'nullable|array',
            'meta_description.*' => 'nullable|string',
            'canonical_url' => 'nullable|string|max:2048|url:http,https',
            'og_image' => 'nullable|array',
            'og_image.*' => 'nullable|string',
            'seo_index' => 'nullable|boolean',
            'seo_follow' => 'nullable|boolean',
        ]);

        if ($this->useTaxonomies) {
            $rules['taxonomies'] = 'nullable|array';
            $rules['taxonomies.*'] = 'exists:taxonomies,id';
        }

        return $rules;
    }

    /**
     * Normalize SEO canonical URL payload before validation/persistence.
     */
    protected function normalizeCanonicalUrlPayload(Request $request): void
    {
        if (!$request->has('canonical_url')) {
            return;
        }

        $request->merge([
            'canonical_url' => CanonicalUrlNormalizer::normalize($request->input('canonical_url')),
        ]);
    }

    /**
     * Shared bulk action contract for base content modules.
     */
    protected function handleBulkAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:publish,unpublish,archive,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|min:1',
        ]);

        $action = $validated['action'];
        $ids = collect($validated['ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $models = $this->modelClass::query()->whereIn('id', $ids)->get();

        if ($models->count() !== $ids->count()) {
            return response()->json([
                'message' => 'Some resources were not found.',
                'code' => 'bulk_action_missing_resources',
            ], 422);
        }

        foreach ($models as $model) {
            Gate::authorize($action === 'delete' ? 'delete' : 'update', $model);
        }

        DB::transaction(function () use ($models, $action) {
            foreach ($models as $model) {
                if ($action === 'delete') {
                    $model->delete();
                    continue;
                }

                $model->update([
                    'status' => match ($action) {
                        'publish' => 'published',
                        'unpublish' => 'draft',
                        'archive' => 'archived',
                    },
                ]);
            }
        });

        AuditLogger::log('content.bulk_action', [
            'module' => $this->module,
            'model' => $this->modelClass,
            'action' => $action,
            'ids' => $ids->all(),
            'count' => $models->count(),
        ]);

        return response()->json([
            'message' => 'Bulk action completed.',
            'data' => [
                'action' => $action,
                'count' => $models->count(),
                'ids' => $ids->all(),
            ],
        ]);
    }
}
