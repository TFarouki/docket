<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RolesAndPermissionsSeeder;

class SystemLocaleSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and permissions
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_unauthorized_user_cannot_update_system_locale()
    {
        // 'clerk' role does not have 'access settings' permission
        $user = User::factory()->create();
        $user->assignRole('clerk');

        // Ensure system locale is initially 'en' (or whatever default)
        Setting::updateOrCreate(['key' => 'system_locale'], ['value' => 'en']);

        $response = $this->actingAs($user)->from('/dashboard')->post(route('settings.updateLocale'), [
            'system_locale' => 'fr',
        ]);

        $response->assertStatus(403);

        // Verify database was NOT updated
        $this->assertEquals('en', Setting::where('key', 'system_locale')->value('value'));
    }

    public function test_authorized_user_can_update_system_locale()
    {
        // 'admin' role has 'access settings' permission
        $user = User::factory()->create();
        $user->assignRole('admin');

        Setting::updateOrCreate(['key' => 'system_locale'], ['value' => 'en']);

        $response = $this->actingAs($user)->from('/dashboard')->post(route('settings.updateLocale'), [
            'system_locale' => 'fr',
        ]);

        $response->assertStatus(302);

        // Verify database WAS updated
        $this->assertEquals('fr', Setting::where('key', 'system_locale')->value('value'));
    }
}
