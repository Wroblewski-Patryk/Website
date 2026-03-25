<?php

namespace Tests\Unit;

use App\Rules\TranslationKeyConsistency;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TranslationKeyConsistencyTest extends TestCase
{
    #[Test]
    public function it_accepts_dot_namespaced_keys_with_valid_segments(): void
    {
        $rule = new TranslationKeyConsistency('frontend');
        $failed = false;

        $rule->validate('key', 'home.hero_title', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    #[Test]
    public function it_rejects_keys_without_namespace_segment(): void
    {
        $rule = new TranslationKeyConsistency('frontend');
        $failed = false;

        $rule->validate('key', 'homepage', function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    #[Test]
    public function it_rejects_admin_prefix_inside_admin_group(): void
    {
        $rule = new TranslationKeyConsistency('admin');
        $failed = false;

        $rule->validate('key', 'admin.menu.dashboard', function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    #[Test]
    public function it_rejects_admin_prefix_in_non_admin_groups(): void
    {
        $rule = new TranslationKeyConsistency('frontend');
        $failed = false;

        $rule->validate('key', 'admin.menu.dashboard', function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
