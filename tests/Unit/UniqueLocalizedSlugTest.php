<?php

namespace Tests\Unit;

use App\Models\Page;
use App\Rules\UniqueLocalizedSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UniqueLocalizedSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_rule_blocks_duplicate_slug_for_same_locale(): void
    {
        Page::factory()->create([
            'slug' => ['en' => 'existing-slug', 'pl' => 'istniejacy-slug'],
        ]);

        $validator = Validator::make(
            ['slug' => ['en' => 'existing-slug']],
            ['slug.en' => ['nullable', 'string', new UniqueLocalizedSlug(Page::class)]]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('slug.en', $validator->errors()->toArray());
    }

    public function test_rule_allows_same_slug_for_ignored_model_id(): void
    {
        $page = Page::factory()->create([
            'slug' => ['en' => 'existing-slug', 'pl' => 'istniejacy-slug'],
        ]);

        $validator = Validator::make(
            ['slug' => ['en' => 'existing-slug']],
            ['slug.en' => ['nullable', 'string', new UniqueLocalizedSlug(Page::class, $page->id)]]
        );

        $this->assertFalse($validator->fails());
    }
}
