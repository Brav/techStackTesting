<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        fake()->unique(true, 100000000);
        $this->call([
            LeagueSeeder::class,
            EventSeeder::class,
            TeamSeeder::class,
            MarketSeeder::class,
            SelectionSeeder::class,
            EventTeamSeeder::class,
        ]);
    }
}
