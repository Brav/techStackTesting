<?php

namespace Database\Seeders;

use App\Models\Database\Enums\QualifierEnum;
use App\Models\Database\Event;
use App\Models\Database\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Throwable;

class EventTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        /** @var list<int> $teamIds */
        $teamIds = Team::query()->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        Event::query()->chunkById(200, function ($events) use ($teamIds) {
            foreach ($events as $event) {

                $event->teams()->wherePivot('qualifier_id', QualifierEnum::HOME->value)->detach();
                $event->teams()->wherePivot('qualifier_id', QualifierEnum::AWAY->value)->detach();

                [$homeTeamId, $awayTeamId] = Arr::random($teamIds, 2);

                $event->teams()->syncWithoutDetaching([
                    $homeTeamId => ['qualifier_id' => QualifierEnum::HOME->value],
                    $awayTeamId => ['qualifier_id' => QualifierEnum::AWAY->value],
                ]);
            }
        });
    }
}
