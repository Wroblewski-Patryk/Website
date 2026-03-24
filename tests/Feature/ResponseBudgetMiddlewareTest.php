<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ResponseBudgetMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_response_budget_header_is_attached_when_enabled(): void
    {
        config()->set('performance.response_budget.enabled', true);
        config()->set('performance.response_budget.threshold_ms', 800);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard.index'));

        $response->assertOk();
        $response->assertHeader('X-Response-Time-Ms');
    }

    public function test_response_budget_warning_is_logged_when_threshold_exceeded(): void
    {
        config()->set('performance.response_budget.enabled', true);
        config()->set('performance.response_budget.threshold_ms', 0);
        Log::spy();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard.index'));

        $response->assertOk();

        Log::shouldHaveReceived('warning')->withArgs(function (string $message, array $context): bool {
            return $message === 'Route response budget exceeded'
                && isset($context['method'], $context['path'], $context['elapsed_ms'], $context['threshold_ms'])
                && $context['threshold_ms'] === 0;
        })->atLeast()->once();
    }
}
