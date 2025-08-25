<?php

namespace Database\Factories;

use App\Models\Database\Enums\CompetitorTypeEnum;
use App\Models\Database\Enums\EventStatusEnum;
use App\Models\Database\Event;
use App\Models\Database\League;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Random\RandomException;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        static $leagueIds;

        if ($leagueIds === null) {
            $leagueIds = League::query()->pluck('id')->all();
        }

        $name = $this->faker->unique()->words(random_int(1, 4), true);

        $scheduledAt = $this->faker->dateTimeBetween('-1 month', '+1 month');

        return [
            'league_id' => $this->faker->randomElement($leagueIds),
            'name' => $name,
            'slug' => Str::slug($name),
            'scheduled_at' => $scheduledAt,
            'status_id' => $this->statusForWhen($scheduledAt),
            'competitor_type_id' => CompetitorTypeEnum::TEAM->value,
        ];
    }

    /**
     * Decide status based on scheduled_at.
     * past  => FINISHED or CANCELLED (random)
     * today => IN_PLAY
     * future=> SCHEDULED
     */
    protected function statusForWhen(\DateTimeInterface $dateTime): string
    {
        $date = Carbon::instance($dateTime)->startOfDay();
        $today = Carbon::today();

        if ($date->lt($today)) {
            return $this->faker->randomElement([
                EventStatusEnum::FINISHED->value,
                EventStatusEnum::CANCELLED->value,
            ]);
        }

        if ($date->equalTo($today)) {
            return EventStatusEnum::IN_PLAY->value;
        }

        return EventStatusEnum::SCHEDULED->value;
    }
}
