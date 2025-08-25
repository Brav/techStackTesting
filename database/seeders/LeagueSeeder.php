<?php

namespace Database\Seeders;

use App\Models\Database\League;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        League::factory()->count(5)->create();
    }
}
