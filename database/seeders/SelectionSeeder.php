<?php

namespace Database\Seeders;

use App\Models\Database\Selection;
use Illuminate\Database\Seeder;

class SelectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Selection::factory()->count(100)->create();
    }
}
