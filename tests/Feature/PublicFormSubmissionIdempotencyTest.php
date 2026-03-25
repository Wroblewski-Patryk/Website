<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFormSubmissionIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_submission_with_idempotency_key_is_saved_once(): void
    {
        $form = Form::factory()->create([
            'status' => 'published',
            'content' => [
                ['type' => 'text', 'id' => 'full_name', 'required' => true],
            ],
            'settings' => ['success_message' => 'Thanks!'],
        ]);

        $route = route('forms.submit', ['locale' => 'en', 'form' => $form]);
        $payload = ['field_full_name' => 'Ada'];

        $this->from('/en/contact')->withHeaders(['Idempotency-Key' => 'abc-123'])->post($route, $payload)
            ->assertRedirect('/en/contact')
            ->assertSessionHas('success', 'Thanks!');

        $this->from('/en/contact')->withHeaders(['Idempotency-Key' => 'abc-123'])->post($route, $payload)
            ->assertRedirect('/en/contact')
            ->assertSessionHas('success', 'Thanks!');

        $this->assertSame(1, FormSubmission::where('form_id', $form->id)->count());
    }

    public function test_form_submission_without_idempotency_key_keeps_current_behavior(): void
    {
        $form = Form::factory()->create([
            'status' => 'published',
            'content' => [
                ['type' => 'text', 'id' => 'full_name', 'required' => true],
            ],
        ]);

        $route = route('forms.submit', ['locale' => 'en', 'form' => $form]);
        $payload = ['field_full_name' => 'Ada'];

        $this->post($route, $payload)->assertRedirect();
        $this->post($route, $payload)->assertRedirect();

        $this->assertSame(2, FormSubmission::where('form_id', $form->id)->count());
    }
}
