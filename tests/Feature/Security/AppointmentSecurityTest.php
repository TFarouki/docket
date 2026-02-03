<?php

namespace Tests\Feature\Security;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AppointmentSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions needed for the test
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'view appointments']);
        Permission::create(['name' => 'create appointments']);
        Permission::create(['name' => 'edit appointments']);
        Permission::create(['name' => 'delete appointments']);
    }

    public function test_unauthorized_user_cannot_view_appointments_index()
    {
        $user = User::factory()->create(); // User has no permissions

        $response = $this->actingAs($user)->get(route('appointments.index'));

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_view_create_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('appointments.create'));

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_store_appointment()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('appointments.store'), [
            'title' => 'Test Appointment',
            'start_time' => now()->addDay()->toDateTimeString(),
            'status' => 'scheduled',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_edit_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::create([
            'title' => 'Test Appointment',
            'start_time' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($user)->get(route('appointments.edit', $appointment));

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_update_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::create([
            'title' => 'Test Appointment',
            'start_time' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($user)->put(route('appointments.update', $appointment), [
            'title' => 'Updated Title',
            'start_time' => now()->addDay()->toDateTimeString(),
            'status' => 'scheduled',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_delete_appointment()
    {
        $user = User::factory()->create();
        $appointment = Appointment::create([
            'title' => 'Test Appointment',
            'start_time' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($user)->delete(route('appointments.destroy', $appointment));

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_appointments()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view appointments');

        $response = $this->actingAs($user)->get(route('appointments.index'));

        $response->assertStatus(200);
    }
}
