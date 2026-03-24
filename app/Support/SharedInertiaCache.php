<?php

namespace App\Support;

use App\Models\Language;
use App\Models\Template;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SharedInertiaCache
{
    private const KEY_VERSION = 'v1';

    private static function namespaced(string $key): string
    {
        return 'shared:' . self::KEY_VERSION . ':' . $key;
    }

    public static function keySettings(): string
    {
        return self::namespaced('global_settings');
    }

    public static function keyHeader(string|int $variant): string
    {
        return self::namespaced('global_header_' . $variant);
    }

    public static function keyFooter(string|int $variant): string
    {
        return self::namespaced('global_footer_' . $variant);
    }

    public static function keyLanguages(): string
    {
        return self::namespaced('active_languages');
    }

    public static function keyProjects(): string
    {
        return self::namespaced('all_projects');
    }

    public static function keyTranslations(string $locale): string
    {
        return self::namespaced('translations.' . $locale);
    }

    public static function forgetSettings(): void
    {
        Cache::forget(self::keySettings());
    }

    public static function forgetHeaderFooter(?int $templateId = null): void
    {
        Cache::forget(self::keyHeader('default'));
        Cache::forget(self::keyFooter('default'));

        if ($templateId) {
            Cache::forget(self::keyHeader($templateId));
            Cache::forget(self::keyFooter($templateId));
        }
    }

    public static function forgetAllHeaderFooterVariants(): void
    {
        self::forgetHeaderFooter();
        Template::query()->pluck('id')->each(function ($id) {
            Cache::forget(self::keyHeader($id));
            Cache::forget(self::keyFooter($id));
        });
    }

    public static function forgetLanguages(): void
    {
        Cache::forget(self::keyLanguages());
    }

    public static function forgetProjects(): void
    {
        Cache::forget(self::keyProjects());
    }

    public static function forgetTranslationsForActiveLocales(): void
    {
        try {
            $locales = Language::query()->where('is_active', true)->pluck('code')->all();
            foreach ($locales as $locale) {
                Cache::forget(self::keyTranslations($locale));
            }
        } catch (\Throwable $e) {
            Log::warning('Could not clear translation cache: ' . $e->getMessage());
        }
    }

    public static function forgetSettingsAndTemplateDrivenSharedData(): void
    {
        self::forgetSettings();
        self::forgetAllHeaderFooterVariants();
    }
}
