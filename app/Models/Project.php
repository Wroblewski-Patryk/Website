<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
    ];

    public $translatable = ['title', 'slug', 'description', 'meta_title', 'meta_description', 'og_image'];
}
