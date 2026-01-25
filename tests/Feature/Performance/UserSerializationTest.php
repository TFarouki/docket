<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class UserSerializationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_dropdowns_do_not_contain_profile_photo_url()
    {
        $user = User::factory()->create();

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
