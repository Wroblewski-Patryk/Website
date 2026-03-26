<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;
use App\Models\Revision;

class ComposedBlock extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'title',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable')->latest();
    }
}
