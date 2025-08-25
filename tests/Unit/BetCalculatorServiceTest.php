<?php

use App\Service\BetCalculatorService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Helper: build an Eloquent collection shaped like your selections.
 *
 * @param  array<int, float|string>  $odds
 */
function selections(array $odds): EloquentCollection
{
    return new EloquentCollection(array_map(static fn ($o) => ['odds' => $o], $odds));
}

it('combines odds (happy path)', function () {
    $sel = selections([2.0, 1.5, 1.2]); // 2 * 1.5 * 1.2 = 3.6
    $res = BetCalculatorService::combineOdds($sel);
    expect($res)->toEqualWithDelta(3.6, 1e-9);
});

it('ignores non-positive odds and casts strings', function () {
    $sel = selections(['2.5', 0, -1, 1.4]); // 2.5 * 1.4 = 3.5
    $res = BetCalculatorService::combineOdds($sel);
    expect($res)->toEqualWithDelta(3.5, 1e-9);
});

it('rounds combined odds to two decimals', function () {
    $sel = selections([1.235, 1.9]); // 1.235 * 1.9 = 2.3465 => 2.35
    $res = BetCalculatorService::combineOdds($sel);
    expect($res)->toEqualWithDelta(2.35, 1e-9);
});

it('returns 1.0 for empty selections (multiplicative identity)', function () {
    $sel = selections([]);
    $res = BetCalculatorService::combineOdds($sel);
    expect($res)->toEqualWithDelta(1.0, 1e-9);
});

it('computes potential payout (happy path)', function () {
    $res = BetCalculatorService::potentialPayout(3.5, 10);
    expect($res)->toEqualWithDelta(35.00, 1e-9);
});

it('rounds potential payout to two decimals', function () {
    $res = BetCalculatorService::potentialPayout(1.2346, 10);
    expect($res)->toEqualWithDelta(12.35, 1e-9);
});
