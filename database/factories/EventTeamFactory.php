<?php

namespace Database\Factories;

use App\Models\Database\Enums\QualifierEnum;
use App\Models\Database\Event;
use App\Models\Database\Pivots\EventTeam;
use App\Models\Database\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventTeam>
 */
class EventTeamFactory extends Factory
{
    protected $model = EventTeam::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::query()->inRandomOrder()->value('id') ?? Event::factory(),
            'team_id' => Team::query()->inRandomOrder()->value('id') ?? Team::factory(),
            'qualifier_id' => $this->faker->randomElement(QualifierEnum::values()),
        ];
    }
}
