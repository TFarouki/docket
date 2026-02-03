<?php

namespace Tests\Feature\Performance;

use App\Models\Appointment;
use App\Models\CourtCase;
use App\Models\Hearing;
use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CalendarOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'view appointments']);
        Permission::create(['name' => 'view matters']);
        $role = Role::create(['name' => 'test-role']);
        $role->givePermissionTo(['view appointments', 'view matters']);
    }

    public function test_calendar_index_selects_specific_columns()
    {
        $user = User::factory()->create();
        $user->assignRole('test-role');

        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '1234567890',
        ]);

        $appointment = Appointment::create([
            'title' => 'Test Appointment',
            'party_id' => $party->id,
            'assigned_to' => $user->id,
            'start_time' => now()->startOfMonth()->addDay(),
            'end_time' => now()->startOfMonth()->addDay()->addHour(),
            'status' => 'scheduled',
            'notes' => 'This is a very long note that should not be loaded.',
        ]);

        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $courtCase = CourtCase::create([
            'matter_id' => $matter->id,
            'court_name' => 'Supreme Court',
            'case_number' => '123/2026',
        ]);

        $hearing = Hearing::create([
            'court_case_id' => $courtCase->id,
            'date_time' => now()->startOfMonth()->addDays(2),
            'notes' => 'Another very long note that should not be loaded.',
        ]);

        DB::enableQueryLog();

        $this->actingAs($user)
            ->get(route('calendar.index', ['month' => now()->month, 'year' => now()->year]));

        $queries = DB::getQueryLog();

        $appointmentQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'select') && str_contains($query['query'], 'appointments');
        });

        $hearingQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'select') && str_contains($query['query'], 'hearings');
        });

        // Currently, it should be selecting * (or at least containing notes implied by *)
        // Since we are asserting the CURRENT state (which is unoptimized), we expect this to FAIL if we asserted 'not contains notes'
        // But for TDD, I'll assert the FAILURE condition (i.e. that it DOES select *).
        // Wait, normally I should write a test that fails NOW and passes LATER.

        // So I will assert that 'notes' is NOT in the query.
        // This test should FAIL now.

        // However, standard "select *" queries don't explicitly list columns in the SQL string usually, they say "select * from ...".
        // Or "select `appointments`.* from ...".

        $this->assertNotNull($appointmentQuery, 'Appointment query not found');
        $this->assertStringNotContainsString('*', $appointmentQuery['query'], 'Appointment query should specific columns');

        // Also check if we are selecting specific columns
        $this->assertStringContainsString('id', $appointmentQuery['query']);
        $this->assertStringContainsString('title', $appointmentQuery['query']);

        $this->assertNotNull($hearingQuery, 'Hearing query not found');
        $this->assertStringNotContainsString('*', $hearingQuery['query'], 'Hearing query should specific columns');
    }
}
