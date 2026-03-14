<?php

namespace App\Traits;

use Carbon\Carbon;

trait HandlePublishableStatus
{
    /**
     * Apply status-related logic before saving a publishable entity.
     *
     * - draft → clears published_at
     * - published (from non-published) → sets published_at to now()
     * - planned → keeps published_at from form (user sets future date)
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model  Existing model (null for store)
     * @param  array  $validated  Validated request data (modified by reference)
     * @return array
     */
    protected function applyStatusLogic($model, array &$validated): array
    {
        $newStatus = $validated['status'] ?? null;
        $oldStatus = $model?->status;

        if ($newStatus === 'published' && $oldStatus !== 'published') {
            // Transitioning TO published → stamp with current date/time
            $validated['published_at'] = Carbon::now();
        }

        if ($newStatus === 'draft') {
            // Draft → clear publication date
            $validated['published_at'] = null;
        }

        // For 'planned' status, published_at comes from the form (user picks future date)
        // so we don't override it here.

        return $validated;
    }
}
