<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimationPreset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'definition' => 'array',
        'is_active' => 'boolean',
    ];
}
