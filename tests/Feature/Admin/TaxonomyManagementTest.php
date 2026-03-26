<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        Language::query()->create(['code' => 'pl', 'name' => 'Polski', 'is_default' => true, 'is_active' => true]);
        Language::query()->create(['code' => 'en', 'name' => 'English', 'is_default' => false, 'is_active' => true]);
        Language::query()->create(['code' => 'de', 'name' => 'Deutsch', 'is_default' => false, 'is_active' => true]);
        Language::query()->create(['code' => 'fr', 'name' => 'Français', 'is_default' => false, 'is_active' => true]);
    }

    public function test_admin_can_store_taxonomy_when_only_default_locale_title_is_filled(): void
    {
        $payload = [
            'type' => 'category',
            'module' => 'posts',
            'order' => 1,
            'color' => '#3b82f6',
            'icon' => '',
            'title' => ['pl' => 'Nowa kategoria', 'en' => '', 'de' => '', 'fr' => ''],
            'slug' => ['pl' => '', 'en' => '', 'de' => '', 'fr' => ''],
            'description' => ['pl' => 'Opis', 'en' => '', 'de' => '', 'fr' => ''],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.taxonomies.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('taxonomies', [
            'type' => 'category',
            'module' => 'posts',
            'title->pl' => 'Nowa kategoria',
            'title->en' => 'Nowa kategoria',
            'slug->pl' => 'nowa-kategoria',
            'slug->en' => 'nowa-kategoria',
        ]);
    }

    public function test_admin_can_update_taxonomy_when_non_default_locale_titles_are_empty(): void
    {
        $taxonomy = Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'posts',
            'order' => 1,
            'title' => ['pl' => 'Stara', 'en' => 'Old', 'de' => 'Alt', 'fr' => 'Ancien'],
            'slug' => ['pl' => 'stara', 'en' => 'old', 'de' => 'alt', 'fr' => 'ancien'],
            'description' => ['pl' => '', 'en' => '', 'de' => '', 'fr' => ''],
        ]);

        $payload = [
            'type' => 'category',
            'module' => 'posts',
            'order' => 2,
            'color' => '#ef4444',
            'icon' => '',
            'title' => ['pl' => 'Zmieniona', 'en' => '', 'de' => '', 'fr' => ''],
            'slug' => ['pl' => '', 'en' => '', 'de' => '', 'fr' => ''],
            'description' => ['pl' => 'Nowy opis', 'en' => '', 'de' => '', 'fr' => ''],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.taxonomies.update', $taxonomy), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('taxonomies', [
            'id' => $taxonomy->id,
            'order' => 2,
            'title->pl' => 'Zmieniona',
            'title->en' => 'Zmieniona',
            'slug->pl' => 'zmieniona',
            'slug->en' => 'zmieniona',
        ]);
    }

    public function test_admin_can_bulk_delete_taxonomies_from_shared_resource_table_contract(): void
    {
        $toDelete = Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'posts',
            'order' => 1,
            'title' => ['pl' => 'A', 'en' => 'A'],
            'slug' => ['pl' => 'a', 'en' => 'a'],
            'description' => ['pl' => '', 'en' => ''],
        ]);

        $alsoDelete = Taxonomy::query()->create([
            'type' => 'category',
            'module' => 'posts',
            'order' => 2,
            'title' => ['pl' => 'B', 'en' => 'B'],
            'slug' => ['pl' => 'b', 'en' => 'b'],
            'description' => ['pl' => '', 'en' => ''],
        ]);

        $keep = Taxonomy::query()->create([
            'type' => 'tag',
            'module' => 'posts',
            'order' => 3,
            'title' => ['pl' => 'C', 'en' => 'C'],
            'slug' => ['pl' => 'c', 'en' => 'c'],
            'description' => ['pl' => '', 'en' => ''],
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.taxonomies.bulk-action'), [
            'action' => 'delete',
            'ids' => [$toDelete->id, $alsoDelete->id],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.action', 'delete')
            ->assertJsonPath('data.count', 2);

        $this->assertDatabaseMissing('taxonomies', ['id' => $toDelete->id]);
        $this->assertDatabaseMissing('taxonomies', ['id' => $alsoDelete->id]);
        $this->assertDatabaseHas('taxonomies', ['id' => $keep->id]);
    }
}
