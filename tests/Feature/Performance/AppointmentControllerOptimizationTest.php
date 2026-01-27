<?php

namespace Tests\Feature\Performance;

use App\Models\Appointment;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AppointmentControllerOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_optimizes_eager_loading()
    {
        $user = User::factory()->create();

        // Create dependencies manually if factories are missing/unreliable for them
        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '1234567890',
            // Add other required fields if any
        ]);

        $assignee = User::factory()->create();

        Appointment::create([
            'title' => 'Test Appointment',
            'party_id' => $party->id,
            'assigned_to' => $assignee->id,
            'start_time' => now(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user)
            ->get(route('appointments.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/Index')
                ->has('appointments.data.0', fn (Assert $json) => $json
                    ->where('title', 'Test Appointment')
                    ->has('party', fn (Assert $partyJson) => $partyJson
                        ->where('id', $party->id)
                        ->where('full_name', $party->full_name)
                        ->missing('address') // Should be missing after optimization
                        ->missing('email')
                    )
                    ->has('assignee', fn (Assert $assigneeJson) => $assigneeJson
                        ->where('id', $assignee->id)
                        ->where('name', $assignee->name)
                        ->missing('email') // Should be missing after optimization
                        ->etc()
                    )
                    ->etc()
                )
            );
    }

    public function test_create_optimizes_user_dropdown()
    {
        $user = User::factory()->create();
        User::factory()->count(3)->create();

        $this->actingAs($user)
            ->get(route('appointments.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Appointments/Create')
                ->has('users.0', fn (Assert $json) => $json
                    ->has('id')
                    ->has('name')
                    ->missing('profile_photo_url') // Should be missing after optimization
                    ->etc()
                )
            );
    }
}
