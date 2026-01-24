<?php

namespace Tests\Feature\Performance;

use App\Models\Appointment;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PayloadOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_matters_create_payload_excludes_profile_photo_url(): void
    {
        $user = User::factory()->create();
        User::factory()->count(3)->create();
        Party::create(['full_name' => 'Test Party', 'type' => 'client']);

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

    public function test_appointments_create_payload_excludes_profile_photo_url(): void
    {
        $user = User::factory()->create();
        User::factory()->count(3)->create();
        Party::create(['full_name' => 'Test Party', 'type' => 'client']);

        $response = $this->actingAs($user)->get(route('appointments.create'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Appointments/Create')
            ->has('users', 4)
            ->has('users.0', fn (Assert $json) => $json
                ->has('id')
                ->has('name')
                ->missing('profile_photo_url')
                ->etc()
            )
        );
    }
}
