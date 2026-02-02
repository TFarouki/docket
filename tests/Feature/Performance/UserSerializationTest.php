<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSerializationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'create appointments']);
        Permission::create(['name' => 'create matters']);
        $role = Role::create(['name' => 'test-role']);
        $role->givePermissionTo(['create appointments', 'create matters']);
    }

    public function test_user_dropdowns_do_not_contain_profile_photo_url()
    {
        $role = Role::create(['name' => 'root']);
        $permission1 = Permission::create(['name' => 'create appointments']);
        $permission2 = Permission::create(['name' => 'create matters']);
        $role->givePermissionTo([$permission1, $permission2]);

        $user = User::factory()->create();
        $user->assignRole('test-role');

        // Create some users to populate the dropdown
        User::factory()->count(3)->create();

        // Appointments Create
        $response = $this->actingAs($user)->get(route('appointments.create'));
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Appointments/Create')
            ->has('users', 4) // The creating user + 3 others
            ->has('users.0', fn (Assert $json) => $json
                ->has('id')
                ->has('name')
                ->missing('profile_photo_url')
                ->etc()
            )
        );

        // Matters Create
        $response = $this->actingAs($user)->get(route('matters.create'));
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Matters/Create')
            ->has('lawyers', 4)
            ->has('lawyers.0', fn (Assert $json) => $json
                ->has('id')
                ->has('name')
                ->missing('profile_photo_url')
                ->etc()
            )
        );
    }
}
