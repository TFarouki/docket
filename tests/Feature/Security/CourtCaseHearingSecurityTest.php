<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Matter;
use App\Models\Party;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CourtCaseHearingSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions needed for the test
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'edit matters']);
    }

    protected function createMatter()
    {
        $party = Party::create([
            'type' => 'client',
            'full_name' => 'Test Client',
            'email' => 'client@example.com',
        ]);

        return Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);
    }

    public function test_unauthorized_user_cannot_view_create_court_case_page()
    {
        $user = User::factory()->create(); // User has no permissions
        $matter = $this->createMatter();

        $response = $this->actingAs($user)->get(route('court-cases.create', ['matter_id' => $matter->id]));

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_store_court_case()
    {
        $user = User::factory()->create();
        $matter = $this->createMatter();

        $response = $this->actingAs($user)->post(route('court-cases.store'), [
            'matter_id' => $matter->id,
            'court_name' => 'High Court',
            'case_number' => '123/2026',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_view_create_hearing_page()
    {
        $user = User::factory()->create();
        $matter = $this->createMatter();

        // We need a court case first to create a hearing
        // Since we can't use the route (unauthorized), we use Model::create directly
        $courtCase = \App\Models\CourtCase::create([
            'matter_id' => $matter->id,
            'court_name' => 'High Court',
            'case_number' => '123/2026',
        ]);

        $response = $this->actingAs($user)->get(route('hearings.create', ['court_case_id' => $courtCase->id]));

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_store_hearing()
    {
        $user = User::factory()->create();
        $matter = $this->createMatter();
        $courtCase = \App\Models\CourtCase::create([
            'matter_id' => $matter->id,
            'court_name' => 'High Court',
            'case_number' => '123/2026',
        ]);

        $response = $this->actingAs($user)->post(route('hearings.store'), [
            'court_case_id' => $courtCase->id,
            'session_date' => now()->addDay()->toDateTimeString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_court_cases()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit matters');
        $matter = $this->createMatter();

        $response = $this->actingAs($user)->get(route('court-cases.create', ['matter_id' => $matter->id]));

        $response->assertStatus(200);
    }

    public function test_authorized_user_can_access_hearings()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit matters');
        $matter = $this->createMatter();
        $courtCase = \App\Models\CourtCase::create([
            'matter_id' => $matter->id,
            'court_name' => 'High Court',
            'case_number' => '123/2026',
        ]);

        $response = $this->actingAs($user)->get(route('hearings.create', ['court_case_id' => $courtCase->id]));

        $response->assertStatus(200);
    }
}
