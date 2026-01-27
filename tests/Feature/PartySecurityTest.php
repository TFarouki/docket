<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PartySecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'view parties']);
        Permission::firstOrCreate(['name' => 'create parties']);
        Permission::firstOrCreate(['name' => 'edit parties']);
        Permission::firstOrCreate(['name' => 'delete parties']);
    }

    public function test_unauthorized_user_cannot_view_parties()
    {
        $user = User::factory()->create();
        // No permissions

        $response = $this->actingAs($user)->get(route('parties.index'));

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_view_parties()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view parties');

        $response = $this->actingAs($user)->get(route('parties.index'));

        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_create_party()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('parties.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post(route('parties.store'), [
            'type' => 'lead',
            'full_name' => 'Test Client',
        ]);
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_create_party()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create parties');
        // 'view parties' is also needed usually for index redirect, but let's see.
        // Redirect logic depends on controller.

        $response = $this->actingAs($user)->get(route('parties.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post(route('parties.store'), [
            'type' => 'lead',
            'full_name' => 'Test Client',
        ]);
        $response->assertRedirect(route('parties.index'));
    }

    public function test_unauthorized_user_cannot_edit_party()
    {
        $user = User::factory()->create();
        $party = Party::create([
            'type' => 'lead',
            'full_name' => 'Test Client',
        ]);

        $response = $this->actingAs($user)->get(route('parties.edit', $party));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->put(route('parties.update', $party), [
            'type' => 'lead',
            'full_name' => 'Updated Name',
        ]);
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_edit_party()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit parties');

        $party = Party::create([
            'type' => 'lead',
            'full_name' => 'Test Client',
        ]);

        $response = $this->actingAs($user)->get(route('parties.edit', $party));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->put(route('parties.update', $party), [
            'type' => 'lead',
            'full_name' => 'Updated Name',
        ]);
        $response->assertRedirect(route('parties.index'));
    }

    public function test_unauthorized_user_cannot_delete_party()
    {
        $user = User::factory()->create();
        $party = Party::create([
            'type' => 'lead',
            'full_name' => 'Test Client',
        ]);

        $response = $this->actingAs($user)->delete(route('parties.destroy', $party));
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_delete_party()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete parties');

        $party = Party::create([
            'type' => 'lead',
            'full_name' => 'Test Client',
        ]);

        $response = $this->actingAs($user)->delete(route('parties.destroy', $party));
        $response->assertRedirect(route('parties.index'));
    }
}
