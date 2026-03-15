<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use App\Models\Revision;

class Post extends Model
{
    use HasFactory, HasTranslations, \App\Traits\HasContentFeatures, \App\Traits\HasTaxonomies;

    public $translatable = ['title', 'slug', 'excerpt', 'featured_image', 'meta_title', 'meta_description', 'og_image'];

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];
}
