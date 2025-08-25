<?php

namespace App\Models\Database;

use Database\Factories\LeagueFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $title
 */
class League extends Model
{
    /** @use HasFactory<LeagueFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory(): LeagueFactory
    {
        return LeagueFactory::new();
    }

    /**
     * @return HasMany<Event, $this>
     */
    public function Event(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function title(): Attribute
    {
        return Attribute::get(fn () => ucwords($this->name));
    }
}
