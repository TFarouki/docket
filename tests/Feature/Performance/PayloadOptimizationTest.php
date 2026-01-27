<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class PayloadOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_matter_payload_does_not_contain_lawyer_profile_photos()
    {
        // Permission middleware might block access, so we need a user with permissions if any.
        // Assuming basic auth is enough for now based on exploration.
        // But UserController routes had 'can:manage users'. MatterController doesn't seem to have explicit middleware in the controller file itself except 'auth'.

        $user = User::factory()->create(['name' => 'Lawyer One']);
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
