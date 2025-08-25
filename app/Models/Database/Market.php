<?php

namespace App\Models\Database;

use Database\Factories\MarketFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $title
 */
class Market extends Model
{
    /** @use HasFactory<MarketFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'event_id',
    ];

    protected static function newFactory(): MarketFactory
    {
        return MarketFactory::new();
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return HasMany<Selection, $this>
     */
    public function selections(): HasMany
    {
        return $this->hasMany(Selection::class, 'market_id');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => ucwords($this->name));
    }
}
