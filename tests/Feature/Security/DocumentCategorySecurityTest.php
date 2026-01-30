<?php

namespace Tests\Feature\Security;

use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentCategorySecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'view documents']);
        Permission::firstOrCreate(['name' => 'create documents']);
    }

    public function test_user_cannot_create_category_without_permission()
    {
        $user = User::factory()->create();
        // No permissions given

        $response = $this->actingAs($user)->postJson(route('document-categories.store'), [
            'name' => 'New Category',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_create_category_with_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create documents');

        $response = $this->actingAs($user)->postJson(route('document-categories.store'), [
            'name' => 'Allowed Category',
        ]);

        $response->assertStatus(200); // Controller returns JSON with 200 by default (or 201)
        $this->assertDatabaseHas('document_categories', ['name' => 'Allowed Category']);
    }

    public function test_user_cannot_list_categories_without_permission()
    {
        $user = User::factory()->create();
        // No permissions

        DocumentCategory::create(['name' => 'Existing Category']);

        $response = $this->actingAs($user)->getJson(route('document-categories.index'));

        $response->assertStatus(403);
    }

    public function test_user_can_list_categories_with_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view documents');

        DocumentCategory::create(['name' => 'Existing Category']);

        $response = $this->actingAs($user)->getJson(route('document-categories.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Existing Category']);
    }
}
