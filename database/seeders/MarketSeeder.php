<?php

namespace Database\Seeders;

use App\Models\Database\Market;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Market::factory()->count(40)->create();
    }
}
