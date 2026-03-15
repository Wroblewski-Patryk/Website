<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Translation extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['group', 'key', 'text'];

    public $translatable = ['text'];

    protected static function booted()
    {
        static::saved(function () {
            static::clearTranslationCache();
        });

        static::deleted(function () {
            static::clearTranslationCache();
        });
    }

    protected static function clearTranslationCache()
    {
        // Get all active languages to clear their specific caches
        try {
            $locales = \App\Models\Language::where('is_active', true)->pluck('code')->toArray();
            foreach ($locales as $locale) {
                \Illuminate\Support\Facades\Cache::forget('translations.' . $locale);
            }
        } catch (\Exception $e) {
            // Fallback if languages table is not available yet or other error
            \Illuminate\Support\Facades\Log::warning("Could not clear translation cache: " . $e->getMessage());
        }
    }
}
