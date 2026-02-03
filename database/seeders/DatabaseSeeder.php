<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // User::factory(10)->create();

        // Create Admin User
        $user = User::factory()->create([
            'name' => 'Taoufik Admin',
            'email' => 'admin@docket.com',
            'password' => bcrypt('password'), // or default
        ]);

        $user->assignRole('owner');
    }
}
