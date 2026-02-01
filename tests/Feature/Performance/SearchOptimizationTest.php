<?php

namespace Tests\Feature\Performance;

use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SearchOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'view parties']);
        $role = Role::create(['name' => 'test-role']);
        $role->givePermissionTo('view parties');
    }

    public function test_party_search_respects_type_filter_with_or_conditions()
    {
        // Create an admin user to access the route
        $user = User::factory()->create();
        $user->assignRole('test-role');

        // Create a 'Lead' named 'John Doe'
        Party::create([
            'full_name' => 'John Doe',
            'type' => 'lead',
        ]);

        // Create a 'Client' named 'John Smith'
        Party::create([
            'full_name' => 'John Smith',
            'type' => 'client',
        ]);

        // Search for 'John' and filter by type 'client'
        // The query is: WHERE (name LIKE %John% OR ...) AND type = 'client'
        // Before fix: WHERE name LIKE %John% OR ... AND type = 'client' -> Returns both
        $response = $this->actingAs($user)->get(route('parties.index', [
            'search' => 'John',
            'type' => 'client',
        ]));

        $response->assertStatus(200);

        // Verify that only the Client is returned
        $response->assertSee('John Smith');
        $response->assertDontSee('John Doe');
    }
}
