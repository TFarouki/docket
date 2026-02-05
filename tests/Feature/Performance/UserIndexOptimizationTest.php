<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserIndexOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure permission exists
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'manage users']);
    }

    public function test_user_index_selects_specific_columns()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('manage users');

        // Create some users to list
        User::factory()->count(3)->create();

        DB::enableQueryLog();

        $this->actingAs($admin)->get(route('users.index'));

        $queries = DB::getQueryLog();

        // Find the query selecting from users table (ignoring the auth user check)
        $userQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'select') &&
                   str_contains($query['query'], 'from "users"') &&
                   !str_contains($query['query'], 'limit 1'); // Exclude auth check
        });

        // If pagination is used, it might be a count query or the select query.
        // We look for the main select query which usually has 'limit' and 'offset' (for pagination)
        $userQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'select') &&
                   str_contains($query['query'], 'from "users"') &&
                   str_contains($query['query'], 'limit 10');
        });

        $this->assertNotNull($userQuery, 'Main user select query not found.');

        // Assert that we are selecting specific columns, not *
        $this->assertStringContainsString('"id"', $userQuery['query']);
        $this->assertStringContainsString('"name"', $userQuery['query']);
        $this->assertStringContainsString('"email"', $userQuery['query']);
        $this->assertStringNotContainsString('"address"', $userQuery['query']);
        $this->assertStringNotContainsString('"phone1"', $userQuery['query']);
        $this->assertStringNotContainsString('"phone2"', $userQuery['query']);
        $this->assertStringNotContainsString('*', $userQuery['query']);
    }

    public function test_roles_eager_loading_is_optimized()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('manage users');

        User::factory()->count(1)->create();

        DB::enableQueryLog();

        $this->actingAs($admin)->get(route('users.index'));

        $queries = DB::getQueryLog();

        // Find roles query
        $rolesQuery = collect($queries)->first(function ($query) {
            return str_contains($query['query'], 'from "roles"');
        });

        $this->assertNotNull($rolesQuery, 'Roles query not found.');

        // Assert selecting specific columns on roles
        $this->assertStringContainsString('"id"', $rolesQuery['query']);
        $this->assertStringContainsString('"name"', $rolesQuery['query']);
        $this->assertStringNotContainsString('*', $rolesQuery['query']);
    }
}
