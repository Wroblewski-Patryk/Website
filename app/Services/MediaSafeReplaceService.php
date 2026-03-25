<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Template;
use Illuminate\Database\Eloquent\Model;

class MediaSafeReplaceService
{
    /**
     * Replace media path references across content-bearing models.
     *
     * @return array<string, int>
     */
    public function replacePathReferences(string $sourcePath, string $targetPath): array
    {
        $replacements = $this->buildReplacementMap($sourcePath, $targetPath);

        return [
            'pages' => $this->replaceForModel(Page::query()->get(), ['content', 'settings', 'og_image'], $replacements),
            'posts' => $this->replaceForModel(Post::query()->get(), ['content', 'featured_image', 'og_image'], $replacements),
            'projects' => $this->replaceForModel(Project::query()->get(), ['content', 'desktop_image', 'mobile_image', 'og_image'], $replacements),
            'templates' => $this->replaceForModel(Template::query()->get(), ['content', 'settings'], $replacements),
            'forms' => $this->replaceForModel(Form::query()->get(), ['content', 'settings'], $replacements),
        ];
    }

    /**
     * @param array<string, string> $replacements
     * @param list<string> $fields
     */
    private function replaceForModel(iterable $items, array $fields, array $replacements): int
    {
        $updated = 0;

        foreach ($items as $item) {
            $changed = false;

            foreach ($fields as $field) {
                if (!$item->offsetExists($field)) {
                    continue;
                }

                $value = $item->getAttribute($field);
                [$newValue, $replaceCount] = $this->replaceInValue($value, $replacements);

                if ($replaceCount > 0) {
                    $item->setAttribute($field, $newValue);
                    $changed = true;
                }
            }

            if ($changed) {
                $item->save();
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * @param array<string, string> $replacements
     * @return array{0: mixed, 1: int}
     */
    private function replaceInValue(mixed $value, array $replacements): array
    {
        if (is_string($value)) {
            return $this->replaceInString($value, $replacements);
        }

        if (is_array($value)) {
            $count = 0;
            $result = [];
            foreach ($value as $key => $nestedValue) {
                [$result[$key], $nestedCount] = $this->replaceInValue($nestedValue, $replacements);
                $count += $nestedCount;
            }

            return [$result, $count];
        }

        return [$value, 0];
    }

    /**
     * @param array<string, string> $replacements
     * @return array{0: string, 1: int}
     */
    private function replaceInString(string $value, array $replacements): array
    {
        $count = 0;
        $result = $value;

        foreach ($replacements as $from => $to) {
            $matches = substr_count($result, $from);
            if ($matches > 0) {
                $result = str_replace($from, $to, $result);
                $count += $matches;
            }
        }

        return [$result, $count];
    }

    /**
     * @return array<string, string>
     */
    private function buildReplacementMap(string $sourcePath, string $targetPath): array
    {
        $sourceStoragePath = 'storage/' . ltrim($sourcePath, '/');
        $targetStoragePath = 'storage/' . ltrim($targetPath, '/');

        $sourcePublicStoragePath = '/' . $sourceStoragePath;
        $targetPublicStoragePath = '/' . $targetStoragePath;

        $sourceUrl = asset($sourceStoragePath);
        $targetUrl = asset($targetStoragePath);

        return [
            $sourceUrl => $targetUrl,
            $sourcePublicStoragePath => $targetPublicStoragePath,
            $sourceStoragePath => $targetStoragePath,
            $sourcePath => $targetPath,
        ];
    }
}
