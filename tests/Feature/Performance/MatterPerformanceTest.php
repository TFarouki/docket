<?php

namespace Tests\Feature\Performance;

use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MatterPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_does_not_load_profile_photo_url_for_responsible_lawyer()
    {
        // Setup permissions
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'view matters']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $lawyer = User::factory()->create();

        $party = Party::create([
             'type' => 'client',
             'full_name' => 'Test Client',
        ]);

        Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'responsible_lawyer_id' => $lawyer->id,
            'status' => 'open',
            'type' => 'litigation',
            'description' => 'Test Description',
            'agreed_fee' => 1000,
        ]);

        $response = $this->actingAs($user)->get(route('matters.index'));

        $response->assertStatus(200);

        // Inspect the Inertia prop
        $matters = $response->viewData('page')['props']['matters']['data'];

        $this->assertNotEmpty($matters);
        $this->assertArrayHasKey('responsible_lawyer', $matters[0]);
        // This assertion should PASS after optimization
        $this->assertArrayNotHasKey('profile_photo_url', $matters[0]['responsible_lawyer'], 'profile_photo_url should be hidden');
    }

    public function test_index_does_not_have_n_plus_one_queries()
    {
        // Setup permissions
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'view matters']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        // Create multiple matters to test N+1
        $lawyer = User::factory()->create();
        $party = Party::create(['type' => 'client', 'full_name' => 'Client 1']);

        // Create 20 matters (page size is 10, so it will load 10)
        for ($i = 0; $i < 20; $i++) {
            Matter::create([
                'title' => 'Matter ' . $i,
                'party_id' => $party->id,
                'responsible_lawyer_id' => $lawyer->id,
                'status' => 'open',
                'type' => 'litigation',
            ]);
        }

        DB::enableQueryLog();

        $this->actingAs($user)->get(route('matters.index'));

        $queries = DB::getQueryLog();

        // If N+1, we would have 10 extra queries for the 10 items on page 1.
        // Base queries ~6-8.
        // N+1 queries ~16-18.

        if (count($queries) > 15) {
             dump(array_map(fn($q) => $q['query'], $queries));
        }

        $this->assertLessThan(15, count($queries), "Query count is " . count($queries) . ". Potential N+1 detected.");
    }
}
