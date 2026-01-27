<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'manage users']);
    }

    public function test_unauthorized_user_cannot_access_user_management()
    {
        $user = User::factory()->create();
        // User has no permissions by default

        $response = $this->actingAs($user)->get(route('users.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('users.create'));
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_create_users()
    {
        $user = User::factory()->create();

        // Try to create a new admin user
        $data = [
            'first_name' => 'Hacker',
            'last_name' => 'User',
            'email' => 'hacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['admin'] // Attempting privilege escalation
        ];

        $response = $this->actingAs($user)->post(route('users.store'), $data);
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_delete_users()
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('users.destroy', $targetUser));
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_user_management()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage users');

        $response = $this->actingAs($user)->get(route('users.index'));
        $response->assertStatus(200);
    }
}
