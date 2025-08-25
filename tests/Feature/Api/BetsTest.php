<?php

use App\Models\Database\Event;
use App\Models\Database\League;
use App\Models\Database\Market;
use App\Models\Database\Selection;
use Illuminate\Testing\Fluent\AssertableJson;

// --- helpers -----------------------------------------------------------------

/**
 * Make one event with a market and N selections.
 *
 * @return array{event: Event, market: Market, selection_ids: list<int>}
 */
function makeEventWithSelections(int $count = 3): array
{
    $league = League::factory()->create();
    $event = Event::factory()->for($league, 'league')->create();
    $market = Market::factory()->for($event, 'event')->create();
    $sels = Selection::factory()->count($count)->for($market, 'market')->create();

    return [
        'event' => $event,
        'market' => $market,
        'selection_ids' => $sels->pluck('id')->map(fn ($id) => (int) $id)->all(),
    ];
}

// --- tests -------------------------------------------------------------------

it('places a bet (happy path)', function () {
    $data = makeEventWithSelections(3);
    $ids = $data['selection_ids'];

    $payload = [
        'selection_ids' => [$ids[0], $ids[1]],
        'stake' => 25, // your BetsRequest uses integer|min:1
    ];

    $res = $this->postJson('/api/v1/bets', $payload);

    $res->assertOk() // controller returns 200 OK (not 201)
        ->assertJson(fn (AssertableJson $json) => $json->where('stake', 25)
            ->hasAll(['combine_odds', 'potential_payout'])
            ->has('selections', 2, fn (AssertableJson $j) => $j->hasAll(['id', 'title', 'odds'])->etc()
            )
            ->has('event', fn (AssertableJson $j) => $j->hasAll(['id', 'title', 'slug'])->etc()
            )
            ->etc()
        );

    // Optional stronger checks:
    $res->assertJsonCount(2, 'selections');
});

it('rejects when selection_ids is missing', function () {
    $this->postJson('/api/v1/bets', ['stake' => 10])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['selection_ids']);
});

it('rejects when selection_ids is empty', function () {
    $this->postJson('/api/v1/bets', ['selection_ids' => [], 'stake' => 10])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['selection_ids']);
});

it('rejects when selection_ids contains non-existent IDs', function () {
    $this->postJson('/api/v1/bets', [
        'selection_ids' => [999999, 888888],
        'stake' => 10,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['selection_ids.0', 'selection_ids.1']);
});

it('rejects when selection_ids is not an array', function () {
    $this->postJson('/api/v1/bets', [
        'selection_ids' => 'not-an-array',
        'stake' => 10,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['selection_ids']);
});

it('rejects when selection_ids contains non-integers', function () {
    $data = makeEventWithSelections(1);
    $valid = $data['selection_ids'][0];

    $this->postJson('/api/v1/bets', [
        'selection_ids' => [$valid, 'abc'],
        'stake' => 10,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['selection_ids.1']);
});

it('rejects when selection_ids contains duplicates', function () {
    $data = makeEventWithSelections(2);
    $a = $data['selection_ids'][0];

    $this->postJson('/api/v1/bets', [
        'selection_ids' => [$a, $a], // distinct rule should fire
        'stake' => 10,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['selection_ids.1']);
});

it('rejects invalid stake values', function () {
    $data = makeEventWithSelections(2);
    $ids = $data['selection_ids'];

    // stake = 0
    $this->postJson('/api/v1/bets', [
        'selection_ids' => $ids,
        'stake' => 0,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['stake']);

    // stake = negative
    $this->postJson('/api/v1/bets', [
        'selection_ids' => $ids,
        'stake' => -5,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['stake']);

    // stake = non-numeric
    $this->postJson('/api/v1/bets', [
        'selection_ids' => $ids,
        'stake' => 'abc',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['stake']);
});
