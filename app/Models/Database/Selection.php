<?php

namespace App\Models\Database;

use Database\Factories\SelectionFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $title
 */
class Selection extends Model
{
    /** @use HasFactory<SelectionFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'market_id',
        'name',
        'odds',
    ];

    protected function casts(): array
    {
        return [
            'odds' => 'decimal:2',
        ];
    }

    protected static function newFactory(): SelectionFactory
    {
        return SelectionFactory::new();
    }

    /** @return BelongsTo<Market, $this> */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * @param  list<int|string>  $selectionIds
     * @return Collection<int, Selection>
     */
    public function getSelections(array $selectionIds): Collection
    {
        return static::whereIn('id', $selectionIds)
            ->with('market', 'market.event')
            ->get();
    }

    /**
     * @return Attribute<string, never>
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => ucwords($this->name));
    }
}
