<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PublishScheduledContentObservabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_publishes_due_content_and_logs_summary(): void
    {
        Log::spy();
        $dueAt = now()->subMinute();
        $futureAt = now()->addDay();

        Page::factory()->create([
            'status' => 'planned',
            'published_at' => $dueAt,
        ]);

        Post::factory()->create([
            'status' => 'planned',
            'published_at' => $dueAt,
        ]);

        Project::factory()->create([
            'status' => 'planned',
            'published_at' => $dueAt,
        ]);

        Form::factory()->create([
            'status' => 'planned',
            'published_at' => $dueAt,
        ]);

        Page::factory()->create([
            'status' => 'planned',
            'published_at' => $futureAt,
        ]);

        $this->artisan('publish:scheduled')
            ->expectsOutput('Published 1 Page(s).')
            ->expectsOutput('Published 1 Post(s).')
            ->expectsOutput('Published 1 Project(s).')
            ->expectsOutput('Published 1 Form(s).')
            ->assertExitCode(0);

        $this->assertDatabaseCount('pages', 2);
        $this->assertDatabaseHas('pages', ['status' => 'published', 'published_at' => $dueAt]);
        $this->assertDatabaseHas('posts', ['status' => 'published']);
        $this->assertDatabaseHas('projects', ['status' => 'published']);
        $this->assertDatabaseHas('forms', ['status' => 'published']);
        $this->assertDatabaseHas('pages', ['status' => 'planned', 'published_at' => $futureAt]);

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'publish:scheduled summary'
                    && ($context['total_published'] ?? null) === 4
                    && ($context['by_model']['Page']['published'] ?? null) === 1
                    && ($context['by_model']['Post']['published'] ?? null) === 1
                    && ($context['by_model']['Project']['published'] ?? null) === 1
                    && ($context['by_model']['Form']['published'] ?? null) === 1;
            });
    }

    public function test_command_logs_zero_summary_when_nothing_is_due(): void
    {
        Log::spy();

        Page::factory()->create([
            'status' => 'planned',
            'published_at' => now()->addDay(),
        ]);

        $this->artisan('publish:scheduled')
            ->expectsOutput('No scheduled content to publish.')
            ->assertExitCode(0);

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'publish:scheduled summary'
                    && ($context['total_published'] ?? null) === 0;
            });
    }
}
