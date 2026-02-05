<?php

namespace Tests\Feature\Security;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CalendarSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'view appointments']);
        Permission::firstOrCreate(['name' => 'view matters']);
    }

    public function test_unauthorized_user_cannot_see_appointments_in_calendar()
    {
        $user = User::factory()->create(); // No permissions

        $appointment = Appointment::create([
            'title' => 'Secret Appointment',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($user)->get(route('calendar.index'));

        $response->assertStatus(200);

        // This assertion will fail if the vulnerability exists
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Calendar/Index')
            ->has('events', 0)
        );
    }

    public function test_authorized_user_can_see_appointments_in_calendar()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view appointments');

        $appointment = Appointment::create([
            'title' => 'Visible Appointment',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($user)->get(route('calendar.index'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Calendar/Index')
            ->has('events', 1)
            ->where('events.0.title', 'Visible Appointment')
        );
    }
}
