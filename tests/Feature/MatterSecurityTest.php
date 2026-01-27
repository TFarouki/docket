<?php

namespace Tests\Feature;

use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MatterSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'view matters']);
        Permission::firstOrCreate(['name' => 'create matters']);
    }

    public function test_index_requires_permission()
    {
        $user = User::factory()->create();
        // User has no permissions

        $response = $this->actingAs($user)->get(route('matters.index'));

        // Expect 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_create_requires_permission()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('matters.create'));

        $response->assertStatus(403);
    }

    public function test_store_requires_permission()
    {
        $user = User::factory()->create();
        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '123456789',
            'email' => 'client@test.com'
        ]);

        $data = [
            'title' => 'Unauthorized Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ];

        $response = $this->actingAs($user)->post(route('matters.store'), $data);

        $response->assertStatus(403);
    }

    public function test_show_requires_permission()
    {
        $user = User::factory()->create();
        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '123456789',
            'email' => 'client@test.com'
        ]);

        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $response = $this->actingAs($user)->get(route('matters.show', $matter));

        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_access()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view matters');

        $response = $this->actingAs($user)->get(route('matters.index'));

        $response->assertStatus(200);
    }
}
