<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Client extends Model
{
    use HasFactory, HasTranslations, \App\Traits\HasContentFeatures;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $translatable = ['title', 'slug', 'description'];

    /**
     * Get the projects for the client.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
