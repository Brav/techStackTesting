<?php

namespace Database\Seeders;

use App\Models\Database\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::factory()->count(32)->create();
    }
}
