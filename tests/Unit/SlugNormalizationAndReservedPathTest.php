<?php

namespace Tests\Unit;

use App\Rules\NotReservedRouteSlug;
use App\Support\LocalizedSlugNormalizer;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SlugNormalizationAndReservedPathTest extends TestCase
{
    public function test_slug_normalizer_slugifies_localized_values(): void
    {
        $normalized = LocalizedSlugNormalizer::normalizeTranslations([
            'pl' => '  Zażółć Gęślą  ',
            'en' => 'Hello World!',
        ]);

        $this->assertSame('zazolc-gesla', $normalized['pl']);
        $this->assertSame('hello-world', $normalized['en']);
    }

    public function test_reserved_slug_rule_rejects_system_paths(): void
    {
        $validator = Validator::make(
            ['slug' => ['en' => 'admin']],
            ['slug.en' => ['nullable', 'string', new NotReservedRouteSlug()]]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('slug.en', $validator->errors()->toArray());
    }
}
