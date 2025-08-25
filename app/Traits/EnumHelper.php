<?php

namespace App\Traits;

trait EnumHelper
{
    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
