<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ModuleBlockCategoriesShareTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_pages_create_shares_module_categories_prop(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pages.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Pages/Edit')
            ->where('moduleCategories', [])
        );
    }

    public function test_posts_create_shares_posts_module_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.posts.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Posts/Edit')
            ->where('moduleCategories.0.id', 'extended')
            ->where('moduleCategories.0.blocks.0.type', 'posts_list')
        );
    }

    public function test_projects_create_shares_projects_module_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.projects.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Projects/Edit')
            ->where('moduleCategories.0.id', 'extended')
            ->where('moduleCategories.0.blocks.0.type', 'projects_list')
        );
    }

    public function test_templates_create_shares_templates_module_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.templates.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Templates/Edit')
            ->where('moduleCategories.0.id', 'building')
            ->where('moduleCategories.0.blocks.0.type', 'template_reference')
        );
    }

    public function test_forms_create_shares_forms_module_categories(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.forms.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Forms/Edit')
            ->where('moduleCategories.0.id', 'data_input')
            ->where('moduleCategories.0.blocks.0.type', 'form')
        );
    }
}

