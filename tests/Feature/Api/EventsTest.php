<?php

use App\Models\Database\Enums\EventStatusEnum;
use App\Models\Database\Enums\QualifierEnum;
use App\Models\Database\Event;
use App\Models\Database\League;
use App\Models\Database\Market;
use App\Models\Database\Selection;
use App\Models\Database\Team;

it('lists events with pagination (happy path)', function () {
    $league = League::factory()->create();

    $event = Event::factory()->create([
        'league_id' => $league->id,
        'status_id' => EventStatusEnum::SCHEDULED,
    ]);

    $home = Team::factory()->create();
    $away = Team::factory()->create();

    $event->teams()->syncWithoutDetaching([
        $home->id => ['qualifier_id' => QualifierEnum::HOME->value],
        $away->id => ['qualifier_id' => QualifierEnum::AWAY->value],
    ]);

    // optional market + selections
    $market = Market::factory()->for($event, 'event')->create();
    Selection::factory()->count(2)->for($market, 'market')->create();

    $res = $this->getJson('/api/v1/events?per_page=10');

    $res->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'title', 'slug', 'scheduled_at', 'status',
                    'league' => ['id', 'title', 'slug'],
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
});

it('shows one event by slug (happy path)', function () {

    $this->withoutExceptionHandling();

    $league = League::factory()->create();
    $event = Event::factory()->create([
        'league_id' => $league->id,
        'status_id' => EventStatusEnum::IN_PLAY,
    ]);

    $res = $this->getJson("/api/v1/events/{$event->slug}");

    $res->assertOk()
        ->assertJsonFragment([
            'slug' => $event->slug,
        ]);
});

it('returns 404 for unknown event slug', function () {
    $this->getJson('/api/v1/events/no-such-slug')->assertNotFound();
});
