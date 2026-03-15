<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use App\Models\Revision; // Added this line

class Page extends Model
{
    use HasFactory, HasTranslations, \App\Traits\HasContentFeatures;

    public $translatable = ['title', 'slug', 'meta_title', 'meta_description', 'og_image'];

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];
    public function getResolvedHeaderIdAttribute()
    {
        return $this->header_override_id ?? Template::where('type', 'header')->where('is_default', true)->value('id');
    }

    public function getResolvedFooterIdAttribute()
    {
        return $this->footer_override_id ?? Template::where('type', 'footer')->where('is_default', true)->value('id');
    }

    public function getResolvedSidebarIdAttribute()
    {
        return $this->sidebar_override_id ?? Template::where('type', 'sidebar')->where('is_default', true)->value('id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class , 'template_id');
    }

    public function headerOverride()
    {
        return $this->belongsTo(Template::class , 'header_override_id');
    }

    public function footerOverride()
    {
        return $this->belongsTo(Template::class , 'footer_override_id');
    }

    public function sidebarOverride()
    {
        return $this->belongsTo(Template::class , 'sidebar_override_id');
    }
}
