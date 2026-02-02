<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Party;
use App\Models\Matter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PayloadOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_matter_payload_does_not_contain_lawyer_profile_photos()
    {
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'create matters']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create(['name' => 'Lawyer One']);
        $user->assignRole($role);

        $otherUser = User::factory()->create(['name' => 'Lawyer Two']);

        $response = $this->actingAs($user)->get(route('matters.create'));

        $response->assertStatus(200);

        // Inertia testing
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Matters/Create')
            ->has('lawyers', 2)
            ->has('lawyers.0', fn (Assert $json) => $json
                ->where('name', 'Lawyer One')
                ->where('id', $user->id)
                ->missing('profile_photo_url')
                ->etc()
            )
        );
    }

    public function test_create_appointment_payload_does_not_contain_user_profile_photos()
    {
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'create appointments']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get(route('appointments.create'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Appointments/Create')
            ->has('users', 1)
            ->has('users.0', fn (Assert $json) => $json
                ->missing('profile_photo_url')
                ->etc()
            )
        );
    }

    public function test_appointment_index_does_not_load_unused_assignee_relation()
    {
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'view appointments']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $party = Party::create(['full_name' => 'Client', 'type' => 'client']);

        $appointment = Appointment::create([
             'title' => 'Meeting',
             'party_id' => $party->id,
             'assigned_to' => $user->id,
             'start_time' => now(),
             'status' => 'scheduled',
             'notes' => 'Some long notes here',
        ]);

        $response = $this->actingAs($user)->get(route('appointments.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Appointments/Index')
            ->has('appointments.data.0', fn (Assert $json) => $json
                ->where('id', $appointment->id)
                ->has('party') // party is still loaded
                ->missing('assignee') // assignee should be missing
                ->missing('notes') // notes should be missing (optimization)
                ->etc()
            )
        );
    }

    public function test_matter_index_payload_does_not_contain_unused_fields()
    {
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'view matters']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $party = Party::create(['full_name' => 'Client', 'type' => 'client']);

        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'status' => 'open',
            'type' => 'litigation',
            'description' => 'A very long description that should not be loaded in the index view.',
            'agreed_fee' => 5000.00,
            'reference_number' => 'REF123',
        ]);

        $response = $this->actingAs($user)->get(route('matters.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Matters/Index')
            ->has('matters.data.0', fn (Assert $json) => $json
                ->where('id', $matter->id)
                ->where('title', 'Test Matter')
                ->where('reference_number', 'REF123')
                ->missing('description')
                ->missing('agreed_fee')
                ->etc()
            )
        );
    }

    public function test_party_index_payload_does_not_contain_unused_fields()
    {
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'view parties']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $party = Party::create([
            'full_name' => 'Test Party',
            'type' => 'client',
            'phone' => '123456789',
            'national_id' => 'AB123456',
            'email' => 'test@example.com',
            'address' => '123 Main St, Some City',
            'notes' => 'Some notes about this party.',
        ]);

        $response = $this->actingAs($user)->get(route('parties.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Parties/Index')
            ->has('parties.data.0', fn (Assert $json) => $json
                ->where('id', $party->id)
                ->where('full_name', 'Test Party')
                ->where('phone', '123456789')
                ->missing('email')
                ->missing('address')
                ->missing('notes')
                ->missing('type')
                ->etc()
            )
        );
    }
}
