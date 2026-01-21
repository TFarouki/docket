<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Client', 'slug' => 'client'],
            ['name' => 'Opponent', 'slug' => 'opponent'],
            ['name' => 'Other', 'slug' => 'other'],
        ];

        foreach ($types as $type) {
            \App\Models\PartyType::updateOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
