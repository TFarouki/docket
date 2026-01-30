<?php

namespace Tests\Feature\Performance;

use App\Models\CourtCase;
use App\Models\Hearing;
use App\Models\Matter;
use App\Models\Party;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class MatterShowPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create permission if it doesn't exist
        if (!Permission::where('name', 'view matters')->exists()) {
            Permission::create(['name' => 'view matters']);
        }
    }

    public function test_matter_show_loads_optimized_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view matters');

        $lawyer = User::factory()->create(['name' => 'Saul Goodman']);
        $party = Party::create(['full_name' => 'Walter White']);

        $matter = Matter::create([
            'title' => 'Meth Empire',
            'party_id' => $party->id,
            'responsible_lawyer_id' => $lawyer->id,
            'type' => 'litigation',
            'status' => 'open',
        ]);

        $courtCase = CourtCase::create([
            'matter_id' => $matter->id,
            'court_name' => 'Albuquerque Court',
            'case_number' => '12345',
            'opponent_name' => 'Hank Schrader', // Should not be loaded
        ]);

        Hearing::create([
            'court_case_id' => $courtCase->id,
            'date_time' => now(),
            'notes' => 'Confidential notes', // Should not be loaded
        ]);

        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->get(route('matters.show', $matter));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Matters/Show')
            ->has('matter', fn (Assert $json) => $json
                ->where('id', $matter->id)
                ->has('responsible_lawyer', fn (Assert $json) => $json
                    ->where('id', $lawyer->id)
                    ->where('name', 'Saul Goodman')
                    ->missing('profile_photo_url')
                    ->missing('email')
                    ->etc()
                )
                ->has('party', fn (Assert $json) => $json
                    ->where('id', $party->id)
                    ->where('full_name', 'Walter White')
                    ->missing('email')
                    ->etc()
                )
                ->has('court_cases.0', fn (Assert $json) => $json
                    ->where('id', $courtCase->id)
                    ->missing('opponent_name')
                    ->missing('created_at')
                    ->has('hearings.0', fn (Assert $json) => $json
                         ->missing('notes')
                         ->etc()
                    )
                    ->etc()
                )
                ->etc()
            )
        );
    }
}
