<?php

namespace Tests\Unit;

use App\Services\BlockBuilder\ModuleBlockRegistry;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ModuleBlockRegistryTest extends TestCase
{
    public function test_it_resolves_posts_categories_from_registry(): void
    {
        $registry = new ModuleBlockRegistry();

        $categories = $registry->resolveCategories('posts');

        $this->assertSame('extended', $categories[0]['id']);
        $this->assertSame('posts_list', $categories[0]['blocks'][0]['type']);
    }

    public function test_it_resolves_template_building_blocks(): void
    {
        $registry = new ModuleBlockRegistry();

        $categories = $registry->resolveCategories('templates');

        $this->assertSame('building', $categories[0]['id']);
        $this->assertSame('template_reference', $categories[0]['blocks'][0]['type']);
        $this->assertSame(['page'], $categories[0]['blocks'][1]['template_types']);
    }

    public function test_it_returns_empty_categories_for_unknown_module(): void
    {
        $registry = new ModuleBlockRegistry();

        $categories = $registry->resolveCategories('unknown_module');

        $this->assertSame([], $categories);
    }

    public function test_it_merges_inheritance_and_deduplicates_overridden_block_types(): void
    {
        Config::set('block_builder.module_registry', [
            'base' => [
                'categories' => [
                    [
                        'id' => 'extended',
                        'blocks' => [
                            ['type' => 'posts_list', 'label' => 'Posts'],
                            ['type' => 'projects_list', 'label' => 'Projects'],
                        ],
                    ],
                ],
            ],
            'child' => [
                'extends' => 'base',
                'categories' => [
                    [
                        'id' => 'extended',
                        'blocks' => [
                            ['type' => 'posts_list', 'label' => 'Posts (Override)'],
                            ['type' => 'text_rotate', 'label' => 'Text Rotate'],
                        ],
                    ],
                ],
            ],
        ]);

        $registry = new ModuleBlockRegistry();
        $categories = $registry->resolveCategories('child');

        $this->assertCount(1, $categories);
        $this->assertSame('extended', $categories[0]['id']);
        $this->assertSame('posts_list', $categories[0]['blocks'][0]['type']);
        $this->assertSame('projects_list', $categories[0]['blocks'][1]['type']);
        $this->assertSame('text_rotate', $categories[0]['blocks'][2]['type']);
    }
}
