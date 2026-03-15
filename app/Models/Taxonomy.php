<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Taxonomy extends Model
{
    use HasTranslations;

    public $translatable = ['title', 'slug', 'description'];

    protected $guarded = [];

    /**
     * Get all posts assigned to this taxonomy.
     */
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'taxonomyable', 'taxonomy_relations');
    }

    /**
     * Get all projects assigned to this taxonomy.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'taxonomyable', 'taxonomy_relations');
    }

    /**
     * Get all pages assigned to this taxonomy.
     */
    public function pages()
    {
        return $this->morphedByMany(Page::class, 'taxonomyable', 'taxonomy_relations');
    }
}
