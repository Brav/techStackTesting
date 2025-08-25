<?php

namespace Database\Factories;

use App\Models\Database\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Random\RandomException;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

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
