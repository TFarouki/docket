<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class UserSerializationTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointments_create_excludes_profile_photo_url_in_users_list()
    {
        $user = User::factory()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($user)->get(route('appointments.create'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Appointments/Create')
            ->has('users', 6)
            ->has('users.0', fn (Assert $json) => $json
                ->missing('profile_photo_url') // Assert it MISSING
                ->etc()
            )
        );
    }

    public function test_matters_create_excludes_profile_photo_url_in_lawyers_list()
    {
        $user = User::factory()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($user)->get(route('matters.create'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Matters/Create')
            ->has('lawyers', 6)
            ->has('lawyers.0', fn (Assert $json) => $json
                ->missing('profile_photo_url') // Assert it MISSING
                ->etc()
            )
        );
    }
}
