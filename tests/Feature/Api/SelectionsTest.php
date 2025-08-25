<?php

use App\Models\Database\Event;
use App\Models\Database\League;
use App\Models\Database\Market;
use App\Models\Database\Selection;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns selections list (happy path)', function () {

    $league = League::factory()->create();
    $event = Event::factory()->for($league, 'league')->create();
    $market = Market::factory()->for($event, 'event')->create();
    Selection::factory()->count(5)->for($market, 'market')->create();

    $this->getJson('/api/v1/selections')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->has('data', 5, fn (AssertableJson $j) => $j->hasAll(['id', 'title', 'odds'])->etc()
        )
        );
});

it('rejects invalid filter params', function () {
    // Adjust based on your request validation, e.g. invalid status
    $this->getJson('/api/v1/events?status_id=NOT_A_STATUS')
        ->assertStatus(422); // or ->assertStatus(422) if you return validation errors
});
