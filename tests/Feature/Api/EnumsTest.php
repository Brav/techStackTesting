<?php

it('lists event statuses (happy path)', function () {
    $this->getJson('/api/v1/event-statuses')
        ->assertOk()
        ->assertJson(fn ($json) => $json->has('data')
            ->has('data.0', fn ($j) => $j->hasAll(['name', 'value', 'label'])
            )
        );
});
