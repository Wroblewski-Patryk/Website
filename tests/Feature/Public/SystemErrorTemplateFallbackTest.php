<?php

namespace Tests\Feature\Public;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SystemErrorTemplateFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_503_page_setting_is_used_during_maintenance_mode(): void
    {
        config(['app.debug' => false]);

        $errorPage = Page::factory()->create([
            'status' => 'published',
            'title' => ['en' => 'Error 503', 'pl' => 'Blad 503'],
            'slug' => ['en' => 'error-503', 'pl' => 'blad-503'],
        ]);

        Setting::updateOrCreate(['key' => 'page_503_id'], ['value' => $errorPage->id]);

        Artisan::call('down');
        $response = $this->get('/en');
        Artisan::call('up');

        $response->assertStatus(503);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Page')
            ->where('page.id', $errorPage->id)
        );
    }
}
