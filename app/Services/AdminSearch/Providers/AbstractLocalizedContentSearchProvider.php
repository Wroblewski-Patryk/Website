<?php

namespace App\Services\AdminSearch\Providers;

use App\Services\AdminSearch\AdminSearchProvider;
use App\Services\AdminSearch\AdminSearchResult;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractLocalizedContentSearchProvider implements AdminSearchProvider
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    abstract protected function resultType(): string;

    abstract protected function editRouteName(): string;

    public function search(string $query, int $limit = 5): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $locale = app()->getLocale();
        $modelClass = $this->modelClass();

        $records = $modelClass::query()
            ->where(function ($builder) use ($query, $locale) {
                $builder
                    ->where("title->{$locale}", 'like', '%' . $query . '%')
                    ->orWhere("slug->{$locale}", 'like', '%' . $query . '%');
            })
            ->latest('updated_at')
            ->limit(max(5, $limit * 4))
            ->get(['id', 'title', 'slug', 'status']);

        $results = $records
            ->map(function ($record) use ($query, $locale) {
                $title = $this->resolveLocalizedValue($record->title, $locale, "Untitled #{$record->id}");
                $slug = $this->resolveLocalizedValue($record->slug, $locale, (string) $record->id);

                return new AdminSearchResult(
                    type: $this->resultType(),
                    id: $record->id,
                    title: $title,
                    url: route($this->editRouteName(), [
                        'locale' => app()->getLocale(),
                        $this->resultType() => $record->id,
                    ]),
                    subtitle: sprintf('%s · %s', $record->status ?? 'draft', $slug),
                    score: $this->calculateScore($query, $title, $slug, (string) ($record->status ?? 'draft')),
                );
            })
            ->sortByDesc(fn (AdminSearchResult $result) => $result->score)
            ->take($limit)
            ->values()
            ->all();

        return $results;
    }

    private function calculateScore(string $query, string $title, string $slug, string $status): float
    {
        $query = mb_strtolower($query);
        $title = mb_strtolower($title);
        $slug = mb_strtolower($slug);
        $score = 0.45;

        if ($title === $query) {
            $score = 1.0;
        } elseif (str_starts_with($title, $query)) {
            $score = 0.9;
        } elseif (str_contains($title, $query)) {
            $score = 0.75;
        } elseif ($slug === $query) {
            $score = 0.65;
        } elseif (str_starts_with($slug, $query)) {
            $score = 0.55;
        }

        $statusBoost = match ($status) {
            'published' => 0.03,
            'planned' => 0.015,
            default => 0.0,
        };

        return $score + $statusBoost;
    }

    private function resolveLocalizedValue(mixed $rawValue, string $locale, string $fallback): string
    {
        if (is_string($rawValue) && $rawValue !== '') {
            return $rawValue;
        }

        if (!is_array($rawValue)) {
            return $fallback;
        }

        $localized = trim((string) ($rawValue[$locale] ?? ''));
        if ($localized !== '') {
            return $localized;
        }

        foreach ($rawValue as $value) {
            $candidate = trim((string) $value);
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return $fallback;
    }
}
