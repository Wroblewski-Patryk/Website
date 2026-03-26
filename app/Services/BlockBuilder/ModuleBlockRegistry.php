<?php

namespace App\Services\BlockBuilder;

class ModuleBlockRegistry
{
    /**
     * @var array<string, mixed>
     */
    private array $registry;

    public function __construct()
    {
        $this->registry = (array) config('block_builder.module_registry', []);
    }

    /**
     * Resolve module categories with inheritance + block-type deduplication.
     *
     * @return array<int, array<string, mixed>>
     */
    public function resolveCategories(string $module): array
    {
        $resolved = $this->resolveModuleNode($module);
        $categories = (array) ($resolved['categories'] ?? []);

        return $this->normalizeCategories($categories);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveModuleNode(string $module): array
    {
        $node = (array) ($this->registry[$module] ?? []);
        $parentKey = isset($node['extends']) ? (string) $node['extends'] : null;

        if (!$parentKey || $parentKey === $module) {
            return $node;
        }

        $parent = $this->resolveModuleNode($parentKey);

        return [
            ...$parent,
            ...$node,
            'categories' => [
                ...(array) ($parent['categories'] ?? []),
                ...(array) ($node['categories'] ?? []),
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @return array<int, array<string, mixed>>
     */
    private function normalizeCategories(array $categories): array
    {
        $merged = [];

        foreach ($categories as $category) {
            if (!is_array($category) || empty($category['id'])) {
                continue;
            }

            $id = (string) $category['id'];
            $blocks = array_values(array_filter((array) ($category['blocks'] ?? []), fn ($block) => is_array($block) && !empty($block['type'])));

            if (!isset($merged[$id])) {
                $merged[$id] = [
                    ...$category,
                    'id' => $id,
                    'blocks' => [],
                ];
            } else {
                $merged[$id] = [
                    ...$merged[$id],
                    ...$category,
                    'id' => $id,
                    'blocks' => $merged[$id]['blocks'],
                ];
            }

            $knownTypes = [];
            foreach ($merged[$id]['blocks'] as $existingBlock) {
                if (is_array($existingBlock) && isset($existingBlock['type'])) {
                    $knownTypes[(string) $existingBlock['type']] = true;
                }
            }

            foreach ($blocks as $block) {
                $type = (string) $block['type'];
                if (isset($knownTypes[$type])) {
                    continue;
                }
                $merged[$id]['blocks'][] = $block;
                $knownTypes[$type] = true;
            }
        }

        return array_values($merged);
    }
}
