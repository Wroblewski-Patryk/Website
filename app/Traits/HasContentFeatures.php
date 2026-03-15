<?php

namespace App\Traits;

use App\Models\Revision;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContentFeatures
{

    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable')->latest();
    }

    /**
     * Scope a query to only include published content.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Help determine if the model should handle SEO.
     */
    public function hasSeoFields(): bool
    {
        return isset($this->translatable) && in_array('meta_title', $this->translatable);
    }
}
