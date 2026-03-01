<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Project extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'desktop_image',
        'mobile_image',
        'url',
        'category',
        'order'
    ];

    public $translatable = ['title', 'description'];
}
