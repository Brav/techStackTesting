<?php

use App\Http\Controllers\BetsController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SelectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('events', EventController::class)
        ->only(['index', 'show'])
        ->scoped(['event' => 'slug']);

    Route::get('/event-statuses', [EnumController::class, 'eventStatuses']);

    Route::apiResource('selections', SelectionController::class)
        ->only(['index']);

    Route::apiResource('bets', BetsController::class)
        ->only(['store']);
});
Route::fallback(static fn () => response()->json(['message' => 'Not Found.'], 404));
