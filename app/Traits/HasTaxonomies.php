<?php

namespace App\Traits;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTaxonomies
{
    /**
     * Get all taxonomies assigned to this content.
     */
    public function taxonomies(): MorphToMany
    {
        return $this->morphToMany(Taxonomy::class, 'taxonomyable', 'taxonomy_relations');
    }
}
