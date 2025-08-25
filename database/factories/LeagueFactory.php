<?php

namespace Database\Factories;

use App\Models\Database\League;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Random\RandomException;

/**
 * @extends Factory<League>
 */
class LeagueFactory extends Factory
{
    protected $model = League::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {

        $name = $this->faker->unique()->words(random_int(1, 4), true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
