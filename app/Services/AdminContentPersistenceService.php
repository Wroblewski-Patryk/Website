<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AdminContentPersistenceService
{
    private const DEFAULT_EXCEPT = [
        'optimistic_lock',
    ];
    /**
     * Persist a new content model with shared relation sync workflow.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function createWithTaxonomies(
        string $modelClass,
        array $payload,
        Request $request,
        callable $syncTaxonomies,
        array $except = []
    ): Model {
        return DB::transaction(function () use ($modelClass, $payload, $request, $syncTaxonomies, $except) {
            $excluded = array_values(array_unique([...self::DEFAULT_EXCEPT, ...$except]));

            /** @var Model $model */
            $model = $modelClass::create(Arr::except($payload, $excluded));
            $syncTaxonomies($model, $request);

            return $model;
        });
    }

    /**
     * Update an existing content model with revision + relation sync workflow.
     */
    public function updateWithRevisionAndTaxonomies(
        Model $model,
        array $payload,
        Request $request,
        callable $saveRevision,
        callable $syncTaxonomies,
        array $except = []
    ): void {
        DB::transaction(function () use ($model, $payload, $request, $saveRevision, $syncTaxonomies, $except) {
            $excluded = array_values(array_unique([...self::DEFAULT_EXCEPT, ...$except]));

            $saveRevision($model);
            $model->update(Arr::except($payload, $excluded));
            $syncTaxonomies($model, $request);
        });
    }
}
