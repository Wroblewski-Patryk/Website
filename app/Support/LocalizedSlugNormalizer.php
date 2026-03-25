<?php

namespace App\Support;

use Illuminate\Support\Str;

class LocalizedSlugNormalizer
{
    /**
     * @param  array<string, mixed>|null  $translations
     * @return array<string, string>
     */
    public static function normalizeTranslations(null|array $translations): array
    {
        if (!is_array($translations)) {
            return [];
        }

        $normalized = [];

        foreach ($translations as $locale => $value) {
            $normalized[$locale] = self::normalizeValue($value);
        }

        return $normalized;
    }

    public static function normalizeValue(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        return Str::slug($trimmed, '-');
    }
}
