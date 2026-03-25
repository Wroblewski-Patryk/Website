<?php

namespace Tests\Unit;

use App\Support\CanonicalUrlNormalizer;
use PHPUnit\Framework\TestCase;

class CanonicalUrlNormalizerTest extends TestCase
{
    public function test_it_normalizes_empty_input_to_null(): void
    {
        $this->assertNull(CanonicalUrlNormalizer::normalize('   '));
        $this->assertNull(CanonicalUrlNormalizer::normalize(null));
    }

    public function test_it_adds_https_scheme_for_bare_host_values(): void
    {
        $this->assertSame(
            'https://example.com/path',
            CanonicalUrlNormalizer::normalize('example.com/path/')
        );
    }

    public function test_it_removes_fragment_and_keeps_query(): void
    {
        $this->assertSame(
            'https://example.com/blog?ref=test',
            CanonicalUrlNormalizer::normalize('https://Example.com/blog/?ref=test#section')
        );
    }
}
