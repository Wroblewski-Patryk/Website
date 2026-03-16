<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_clients(): void
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.projects.clients.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Clients/Index'));
    }

    public function test_admin_can_create_client_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.projects.clients.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page->component('Admin/Clients/Edit'));
    }

    public function test_admin_can_store_client(): void
    {
        $data = [
            'title' => ['pl' => 'Testowy Klient', 'en' => 'Test Client'],
            'slug' => ['pl' => 'testowy-klient', 'en' => 'test-client'],
            'description' => ['pl' => 'Opis klienta', 'en' => 'Client description'],
            'website_url' => 'https://example.com',
            'email' => 'client@example.com',
            'phone' => '123456789',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.projects.clients.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'title->pl' => 'Testowy Klient',
            'email' => 'client@example.com',
        ]);
    }

    public function test_admin_can_update_client(): void
    {
        $client = Client::factory()->create();

        $data = [
            'title' => ['pl' => 'Nowa Nazwa', 'en' => 'New Name'],
            'slug' => ['pl' => 'nowa-nazwa', 'en' => 'new-name'],
            'description' => ['pl' => 'Nowy opis', 'en' => 'New description'],
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.projects.clients.update', $client), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'title->pl' => 'Nowa Nazwa',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.projects.clients.destroy', $client));

        $response->assertRedirect();
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
