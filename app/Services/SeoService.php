<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class SeoService
{
    protected array $settings;

    public function __construct()
    {
        $this->settings = Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Build the page title based on context.
     */
    public function getTitle(string $area = 'public', string $module = '', string $action = '', $entity = null, bool $isHome = false, string $parent = ''): string
    {
        $brand = $this->getSetting('site_name', config('app.name', 'Featherly'));
        $separator = $this->getSetting('title_separator', ' - ');
        $order = $this->getSetting('title_order', 'brand_first');

        if ($area === 'admin') {
            return $this->buildAdminTitle($brand, $separator, $module, $action, $entity);
        }

        return $this->buildPublicTitle($brand, $separator, $order, $entity, $module, $isHome, $parent);
    }

    /**
     * Mapping of internal module names to human-readable titles.
     */
    public function getModuleLabel(string $module): string
    {
        $labels = [
            'posts' => 'Blog',
            'categories' => 'Kategorie',
            'projects' => 'Projekty',
            'clients' => 'Klienci',
            'pages' => 'Strony',
            'forms' => 'Formularze',
            'media' => 'Media',
            'theme' => 'Motyw',
            'settings' => 'Ustawienia',
            'users' => 'Użytkownicy',
            'translations' => 'Tłumaczenia',
            'languages' => 'Języki',
        ];

        return $labels[strtolower($module)] ?? Str::ucfirst($module);
    }

    /**
     * Build title for Admin area.
     * Pattern: {EntityName} - {Action} - {ModuleTitle} - Admin Panel - {Brand}
     */
    protected function buildAdminTitle(string $brand, string $separator, string $module, string $action, $entity = null): string
    {
        $moduleLabel = $this->getModuleLabel($module);
        $actionLabel = $action ?Str::ucfirst($action) : '';

        // Map common actions to Polish
        $actionMap = [
            'edit' => 'Edycja',
            'create' => 'Nowy',
            'index' => 'Lista',
            'show' => 'Podgląd',
        ];
        $actionLabel = $actionMap[strtolower($action)] ?? $actionLabel;

        $entityTitle = $entity ? $this->getEntityTitle($entity) : '';

        $parts = [];
        if ($entityTitle)
            $parts[] = $entityTitle;
        if ($actionLabel)
            $parts[] = $actionLabel;
        if ($moduleLabel)
            $parts[] = $moduleLabel;
        $parts[] = 'Panel Administracyjny';
        $parts[] = $brand;

        return implode($separator, array_filter($parts));
    }

    /**
     * Build title for Public area.
     * Pattern: {PageTitle} - {Parent} - {Brand}
     */
    protected function buildPublicTitle(string $brand, string $separator, string $order, $entity, string $module = '', bool $isHome = false, string $parent = ''): string
    {
        $pageTitle = $this->getEntityTitle($entity) ?: $this->getModuleLabel($module) ?: 'Untitled';

        if ($isHome) {
            $includePageTitle = $this->getSetting('homepage_include_page_title', false);
            if (!$includePageTitle) {
                return $brand;
            }
        }

        $parts = [];
        if ($order === 'page_first') {
            $parts[] = $pageTitle;
            if ($parent)
                $parts[] = $parent;
            $parts[] = $brand;
        }
        else {
            $parts[] = $brand;
            if ($parent)
                $parts[] = $parent;
            $parts[] = $pageTitle;
        }

        return implode($separator, array_filter($parts));
    }

    /**
     * Get title from entity (meta_title -> title -> module fallback).
     */
    public function getEntityTitle($entity): ?string
    {
        if (!$entity)
            return null;

        $locale = App::getLocale();
        $fallback = config('app.fallback_locale', 'pl');

        if (is_object($entity)) {
            // Priority: meta_title -> title
            $title = $entity->getTranslation('meta_title', $locale)
                ?: $entity->getTranslation('title', $locale);

            // Fallback to other locales if current is empty
            if (!$title || trim((string)$title) === '') {
                $title = $entity->getTranslation('meta_title', $fallback)
                    ?: $entity->getTranslation('title', $fallback);
            }

            // Final fallback to any translation or slug if still empty
            if (!$title || trim((string)$title) === '') {
                $translations = $entity->getTranslations('title');
                $title = !empty($translations) ? reset($translations) : null;
            }

            if (!$title || trim((string)$title) === '') {
                $title = $entity->slug ?? null;
            }

            return $title ? (string)$title : null;
        }

        if (is_array($entity)) {
            $title = $entity['meta_title'][$locale] ?? $entity['title'][$locale] ?? null;
            if (!$title || trim((string)$title) === '') {
                $title = $entity['meta_title'][$fallback] ?? $entity['title'][$fallback] ?? $entity['meta_title']['pl'] ?? $entity['title']['pl'] ?? null;
            }
            return $title ? (string)$title : null;
        }

        return $entity ? (string)$entity : null;
    }

    /**
     * Get SEO metadata for public pages.
     */
    public function getMetaData($entity = null, string $parent = ''): array
    {
        $locale = App::getLocale();
        $fallback = config('app.fallback_locale', 'pl');
        $defaultDesc = $this->getSetting('default_meta_description', '');
        $defaultOgImage = $this->getSetting('default_og_image', '');

        $data = [
            'title' => $this->getEntityTitle($entity),
            'full_title' => $this->getTitle('public', '', '', $entity, false, $parent),
            'parent' => $parent,
            'description' => $defaultDesc,
            'og_image' => $defaultOgImage,
            'canonical' => url()->current(),
            'robots' => 'index, follow',
        ];

        if ($entity && is_object($entity)) {
            $data['description'] = $entity->getTranslation('meta_description', $locale) ?: $data['description'];
            $data['og_image'] = $entity->getTranslation('og_image', $locale) ?: $data['og_image'];
            $data['canonical'] = $entity->canonical_url ?: $data['canonical'];

            $index = $entity->seo_index ?? true;
            $follow = $entity->seo_follow ?? true;
            $data['robots'] = ($index ? 'index' : 'noindex') . ', ' . ($follow ? 'follow' : 'nofollow');
        }

        return $data;
    }

    /**
     * Helper to get localized setting.
     */
    protected function getSetting(string $key, $default = null)
    {
        $value = $this->settings[$key] ?? $default;

        if (is_array($value)) {
            return $value[App::getLocale()] ?? $value[config('app.fallback_locale')] ?? $default;
        }

        return $value;
    }
}
