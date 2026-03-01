<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Revision; // Added this line

class Page extends Model
{
    use HasTranslations;

    public function revisions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Revision::class , 'revisionable')->latest();
    }

    public $translatable = ['title', 'slug', 'meta_title', 'meta_description', 'og_image'];

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
    ];
    public function headerOverride()
    {
        return $this->belongsTo(Template::class , 'header_override_id');
    }

    public function footerOverride()
    {
        return $this->belongsTo(Template::class , 'footer_override_id');
    }
}
