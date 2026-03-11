<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Default locale for all route() calls during tests
        \Illuminate\Support\Facades\URL::defaults(['locale' => 'en']);
    }
}
