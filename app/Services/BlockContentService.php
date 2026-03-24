<?php

namespace App\Services;

use App\Models\Template;

class BlockContentService
{
    /**
     * Recursively resolve template references in a block structure.
     */
    public function resolveReferences(array $content, int $depth = 0, ?array $templateMap = null): array
    {
        if ($depth > 5) return $content; // Prevent infinite recursion

        if ($templateMap === null) {
            $templateMap = $this->buildTemplateMap($content, 5);
        }

        return array_map(function ($block) use ($depth, $templateMap) {
            if (!is_array($block) || !isset($block['type'])) return $block;

            // 1. Resolve template references
            if ($block['type'] === 'template_reference') {
                $templateId = $block['content']['template_id'] ?? null;
                if ($templateId) {
                    $template = $templateMap[(int) $templateId] ?? null;
                    if ($template) {
                        $block['content']['template_content'] = $this->resolveReferences($template->content ?: [], $depth + 1, $templateMap);
                        $block['content']['template_title'] = $template->title;
                    }
                }
            }

            // 2. Recurse into children
            if (isset($block['children']) && is_array($block['children'])) {
                $block['children'] = $this->resolveReferences($block['children'], $depth + 1, $templateMap);
            }

            // 3. Recurse into content if it's a block list
            if (isset($block['content']) && is_array($block['content']) && $block['type'] !== 'template_reference') {
                $isBlockList = true;
                foreach($block['content'] as $item) {
                    if (!is_array($item) || !isset($item['type'])) {
                        $isBlockList = false;
                        break;
                    }
                }
                if ($isBlockList) {
                    $block['content'] = $this->resolveReferences($block['content'], $depth + 1, $templateMap);
                }
            }

            return $block;
        }, $content);
    }

    /**
     * Preload referenced templates (including nested template references) in batches.
     *
     * @return array<int, Template>
     */
    private function buildTemplateMap(array $content, int $maxDepth = 5): array
    {
        $templateMap = [];
        $pendingIds = $this->extractTemplateIds($content);
        $currentDepth = 0;

        while (!empty($pendingIds) && $currentDepth <= $maxDepth) {
            $missingIds = array_values(array_diff($pendingIds, array_keys($templateMap)));
            if (empty($missingIds)) {
                break;
            }

            $templates = Template::query()
                ->whereIn('id', $missingIds)
                ->get()
                ->keyBy('id');

            $newPendingIds = [];
            foreach ($templates as $id => $template) {
                $templateMap[(int) $id] = $template;
                $newPendingIds = array_merge($newPendingIds, $this->extractTemplateIds($template->content ?: []));
            }

            $pendingIds = array_values(array_unique($newPendingIds));
            $currentDepth++;
        }

        return $templateMap;
    }

    /**
     * Extract all template ids from nested block content.
     *
     * @return array<int, int>
     */
    private function extractTemplateIds(array $content): array
    {
        $ids = [];

        foreach ($content as $block) {
            if (!is_array($block)) {
                continue;
            }

            if (($block['type'] ?? null) === 'template_reference') {
                $templateId = $block['content']['template_id'] ?? null;
                if (is_numeric($templateId)) {
                    $ids[] = (int) $templateId;
                }
            }

            if (isset($block['children']) && is_array($block['children'])) {
                $ids = array_merge($ids, $this->extractTemplateIds($block['children']));
            }

            if (isset($block['content']) && is_array($block['content'])) {
                $isBlockList = true;
                foreach ($block['content'] as $item) {
                    if (!is_array($item) || !isset($item['type'])) {
                        $isBlockList = false;
                        break;
                    }
                }
                if ($isBlockList) {
                    $ids = array_merge($ids, $this->extractTemplateIds($block['content']));
                }
            }
        }

        return array_values(array_unique($ids));
    }
}
