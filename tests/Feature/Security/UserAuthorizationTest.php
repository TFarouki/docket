<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeder to set up roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_unauthorized_user_cannot_access_user_management()
    {
        // Create a user with a role that DOES NOT have 'manage users' permission
        // 'clerk' role has: 'view matters', 'edit matters', 'create documents', 'view tasks', 'edit tasks'
        $user = User::factory()->create();
        $user->assignRole('clerk');

        // Try to access index
        $response = $this->actingAs($user)->get(route('users.index'));
        $response->assertStatus(403);

        // Try to access create
        $response = $this->actingAs($user)->get(route('users.create'));
        $response->assertStatus(403);

        // Try to store
        $response = $this->actingAs($user)->post(route('users.store'), [
            'first_name' => 'Bad',
            'last_name' => 'Actor',
            'email' => 'bad@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['admin']
        ]);
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_user_management()
    {
        // 'manager' role has 'manage users'
        $user = User::factory()->create();
        $user->assignRole('manager');

        $response = $this->actingAs($user)->get(route('users.index'));
        $response->assertStatus(200);
    }
}
