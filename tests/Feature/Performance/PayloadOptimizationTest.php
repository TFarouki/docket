<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PayloadOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'create matters']);
        Permission::create(['name' => 'create appointments']);
        Permission::create(['name' => 'view appointments']);

        $role = Role::create(['name' => 'test-role']);
        $role->givePermissionTo(['create matters', 'create appointments', 'view appointments']);
    }

    public function test_create_matter_payload_does_not_contain_lawyer_profile_photos()
    {
        $user = User::factory()->create(['name' => 'Lawyer One']);
        $user->assignRole('test-role');
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
        $user = User::factory()->create();
        $user->assignRole('test-role');

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
        $user = User::factory()->create();
        $user->assignRole('test-role');
        $party = Party::create(['full_name' => 'Client', 'type' => 'client']);

        $appointment = Appointment::create([
             'title' => 'Meeting',
             'party_id' => $party->id,
             'assigned_to' => $user->id,
             'start_time' => now(),
             'status' => 'scheduled'
        ]);

        $response = $this->actingAs($user)->get(route('appointments.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Appointments/Index')
            ->has('appointments.data.0', fn (Assert $json) => $json
                ->where('id', $appointment->id)
                ->has('party') // party is still loaded
                ->missing('assignee') // assignee should be missing
                ->etc()
            )
        );
    }
}
