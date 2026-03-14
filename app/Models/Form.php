<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Translatable\HasTranslations;

class Form extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['title'];

    protected $fillable = [
        'title',
        'content',
        'settings',
        'status',
        'published_at'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];
}
