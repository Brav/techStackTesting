<?php

namespace Database\Factories;

use App\Models\Database\Market;
use App\Models\Database\Selection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Selection>
 */
class SelectionFactory extends Factory
{
    protected $model = Selection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        static $marketIds;

        if ($marketIds === null) {
            $marketIds = Market::query()->pluck('id')->all();
        }

        return [
            'market_id' => $this->faker->randomElement($marketIds),
            'name' => $this->faker->unique()->words(random_int(1, 2), true),
            'odds' => $this->faker->randomFloat(2, 1.01, 35.00),
        ];

    }
}
