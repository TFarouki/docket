<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Hearing;
use App\Models\CourtCase;
use App\Models\Matter;
use App\Models\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class CalendarSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'view appointments']);
        Permission::create(['name' => 'view matters']);
    }

    public function test_unauthorized_user_sees_empty_calendar()
    {
        // 1. Setup data
        $party = Party::create(['full_name' => 'Test Party']);

        $appointment = Appointment::create([
            'title' => 'Secret Appointment',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'status' => 'scheduled',
            'party_id' => $party->id,
        ]);

        $matter = Matter::create([
            'title' => 'Secret Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $courtCase = CourtCase::create([
            'matter_id' => $matter->id,
            'case_number' => '123/2026',
            'court_name' => 'High Court',
        ]);

        $hearing = Hearing::create([
            'court_case_id' => $courtCase->id,
            'date_time' => now(),
        ]);

        // 2. Create unauthorized user
        $user = User::factory()->create(); // No permissions

        // 3. Act
        $response = $this->actingAs($user)->get(route('calendar.index'));

        // 4. Assert
        $response->assertStatus(200);

        $events = $response->inertiaProps('events');
        $this->assertEmpty($events, 'Unauthorized user should see no events');
    }

    public function test_authorized_user_sees_calendar_events()
    {
        // 1. Setup data
        $party = Party::create(['full_name' => 'Authorized Party']);

        $appointment = Appointment::create([
            'title' => 'Visible Appointment',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'status' => 'scheduled',
            'party_id' => $party->id,
        ]);

        $matter = Matter::create([
            'title' => 'Visible Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $courtCase = CourtCase::create([
            'matter_id' => $matter->id,
            'case_number' => '456/2026',
            'court_name' => 'Supreme Court',
        ]);

        $hearing = Hearing::create([
            'court_case_id' => $courtCase->id,
            'date_time' => now(),
        ]);

        // 2. Create authorized user
        $user = User::factory()->create();
        $user->givePermissionTo('view appointments');
        $user->givePermissionTo('view matters');

        // 3. Act
        $response = $this->actingAs($user)->get(route('calendar.index'));

        // 4. Assert
        $response->assertStatus(200);

        $events = $response->inertiaProps('events');
        $this->assertNotEmpty($events);

        $hasAppointment = collect($events)->contains('title', 'Visible Appointment');
        $hasHearing = collect($events)->contains(function ($event) {
            return str_contains($event['title'], 'Hearing: Visible Matter');
        });

        $this->assertTrue($hasAppointment, 'Authorized user should see appointment');
        $this->assertTrue($hasHearing, 'Authorized user should see hearing');
    }

    public function test_partially_authorized_user_sees_only_allowed_events()
    {
        // 1. Setup data
        $party = Party::create(['full_name' => 'Partial Party']);

        $appointment = Appointment::create([
            'title' => 'Only Appointment',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'status' => 'scheduled',
            'party_id' => $party->id,
        ]);

        $matter = Matter::create([
            'title' => 'Hidden Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $courtCase = CourtCase::create([
            'matter_id' => $matter->id,
            'case_number' => '789/2026',
            'court_name' => 'District Court',
        ]);

        $hearing = Hearing::create([
            'court_case_id' => $courtCase->id,
            'date_time' => now(),
        ]);

        // 2. Create user with ONLY appointment permission
        $user = User::factory()->create();
        $user->givePermissionTo('view appointments');
        // No 'view matters'

        // 3. Act
        $response = $this->actingAs($user)->get(route('calendar.index'));

        // 4. Assert
        $response->assertStatus(200);

        $events = $response->inertiaProps('events');

        $hasAppointment = collect($events)->contains('title', 'Only Appointment');
        $hasHearing = collect($events)->contains(function ($event) {
            return str_contains($event['title'], 'Hearing: Hidden Matter');
        });

        $this->assertTrue($hasAppointment, 'User with view appointments should see appointment');
        $this->assertFalse($hasHearing, 'User without view matters should NOT see hearing');
    }
}
