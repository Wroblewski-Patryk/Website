<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Revision;

class Post extends Model
{
    use HasTranslations;

    public function revisions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Revision::class , 'revisionable')->latest();
    }

    public $translatable = ['title', 'slug', 'excerpt', 'content', 'featured_image', 'meta_title', 'meta_description', 'og_image'];

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
