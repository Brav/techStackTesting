<?php

namespace Database\Factories;

use App\Models\Database\Event;
use App\Models\Database\Market;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Market>
 */
class MarketFactory extends Factory
{
    protected $model = Market::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        static $eventIds;

        if ($eventIds === null) {
            $eventIds = Event::query()->pluck('id')->all();
        }

        return [
            'event_id' => $this->faker->randomElement($eventIds),
            'name' => $this->faker->unique()->words(random_int(1, 4), true),
        ];
    }
}
