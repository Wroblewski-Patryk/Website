<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use App\Models\Translation;
use Tests\TestCase;
use App\Models\User;
use App\Models\Language;

class TranslationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_languages(): void
    {
        Language::create(['name' => 'Polish', 'code' => 'pl', 'is_active' => true, 'is_default' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.languages.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Languages/Index'));
    }

    public function test_admin_can_store_language(): void
    {
        $data = [
            'name' => 'English',
            'code' => 'en',
            'is_active' => true,
            'is_default' => false,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.languages.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('languages', ['code' => 'en']);
    }

    public function test_translations_index_exposes_coverage_summary(): void
    {
        Language::create(['name' => 'Polish', 'code' => 'pl', 'is_active' => true, 'is_default' => true]);
        Language::create(['name' => 'English', 'code' => 'en', 'is_active' => true, 'is_default' => false]);

        Translation::create([
            'group' => 'admin',
            'key' => 'menu.dashboard',
            'text' => ['pl' => 'Panel', 'en' => 'Dashboard'],
        ]);

        Translation::create([
            'group' => 'frontend',
            'key' => 'home.title',
            'text' => ['pl' => 'Strona glowna', 'en' => ''],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.translations.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Translations/Index')
            ->where('coverage.total_keys', 2)
            ->where('coverage.fully_covered_languages', 1)
            ->where('coverage.languages.0.code', 'pl')
            ->where('coverage.languages.0.coverage_percent', 100)
            ->where('coverage.languages.1.code', 'en')
            ->where('coverage.languages.1.coverage_percent', 50)
            ->where('coverage.groups.0.group', 'admin')
            ->where('coverage.groups.0.keys', 1)
        );
    }
}
