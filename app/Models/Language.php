<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'is_default', 'is_active'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($language) {
            if ($language->is_default) {
                // Ensure only one default language
                static::where('id', '!=', $language->id)->update(['is_default' => false]);
                $language->is_active = true; // Default language must be active
            }
        });

        static::deleting(function ($language) {
            if ($language->is_default) {
                throw new \Exception("Cannot delete the default language.");
            }
        });
    }
}
