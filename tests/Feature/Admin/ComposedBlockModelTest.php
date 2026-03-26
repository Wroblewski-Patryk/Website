<?php

namespace Tests\Feature\Admin;

use App\Models\ComposedBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComposedBlockModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_composed_block_can_be_persisted_with_translatable_title_and_block_content(): void
    {
        $block = ComposedBlock::create([
            'title' => ['en' => 'Hero Section', 'pl' => 'Sekcja Hero'],
            'slug' => 'hero-section',
            'content' => [
                [
                    'id' => 'container-1',
                    'type' => 'container',
                    'children' => [
                        ['id' => 'heading-1', 'type' => 'heading'],
                        ['id' => 'paragraph-1', 'type' => 'paragraph'],
                    ],
                ],
            ],
            'settings' => [
                'category' => 'marketing',
            ],
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('composed_blocks', [
            'id' => $block->id,
            'slug' => 'hero-section',
        ]);
        $this->assertSame('Hero Section', $block->getTranslation('title', 'en'));
        $this->assertSame('container', $block->content[0]['type']);
        $this->assertTrue($block->is_active);
    }
}

