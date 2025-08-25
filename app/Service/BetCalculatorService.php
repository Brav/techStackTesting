<?php

namespace App\Service;

use App\Models\Database\Selection;
use Illuminate\Database\Eloquent\Collection;

class BetCalculatorService
{
    /**
     * @param  Collection<int, Selection>  $selections
     *
     * @phpstan-param Collection<int, Selection> $selections
     */
    public static function combineOdds(Collection $selections): float
    {
        $odds = $selections
            ->pluck('odds')
            ->map(fn ($v) => (float) $v)
            ->filter(fn ($v) => $v > 0)
            ->reduce(fn ($acc, $o) => $acc * $o, 1.0);

        return round($odds, 2);
    }

    public static function potentialPayout(float $odds, float $stake): float
    {
        return round($odds * $stake, 2);
    }
}
