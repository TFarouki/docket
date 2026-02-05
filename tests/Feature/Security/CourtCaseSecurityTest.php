<?php

namespace Tests\Feature\Security;

use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CourtCaseSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'edit matters']);
        Permission::firstOrCreate(['name' => 'create matters']);
    }

    public function test_create_court_case_requires_permission()
    {
        $user = User::factory()->create();
        // User has NO permissions

        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '123456789',
            'email' => 'client@test.com',
        ]);

        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $response = $this->actingAs($user)->get(route('court-cases.create', ['matter_id' => $matter->id]));

        $response->assertStatus(403);
    }

    public function test_store_court_case_requires_permission()
    {
        $user = User::factory()->create();

        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '123456789',
            'email' => 'client@test.com',
        ]);

        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $data = [
            'matter_id' => $matter->id,
            'court_name' => 'Supreme Court',
            'case_number' => 'CASE-123-NEW',
            'judge_name' => 'Judge Dredd',
        ];

        $response = $this->actingAs($user)->post(route('court-cases.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('court_cases', ['case_number' => 'CASE-123-NEW']);
    }

    public function test_authorized_user_can_create_court_case()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit matters');

        $party = Party::create([
            'full_name' => 'Test Client',
            'type' => 'client',
            'phone' => '123456789',
            'email' => 'client@test.com',
        ]);

        $matter = Matter::create([
            'title' => 'Test Matter',
            'party_id' => $party->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        // Test Create Page
        $response = $this->actingAs($user)->get(route('court-cases.create', ['matter_id' => $matter->id]));
        $response->assertStatus(200);

        // Test Store Action
        $data = [
            'matter_id' => $matter->id,
            'court_name' => 'Supreme Court',
            'case_number' => 'CASE-AUTHORIZED',
            'judge_name' => 'Judge Dredd',
        ];

        $response = $this->actingAs($user)->post(route('court-cases.store'), $data);

        $response->assertStatus(302); // Redirects to matters.show
        $this->assertDatabaseHas('court_cases', ['case_number' => 'CASE-AUTHORIZED']);
    }
}
