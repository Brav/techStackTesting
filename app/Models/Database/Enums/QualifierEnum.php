<?php

namespace App\Models\Database\Enums;

use App\Traits\EnumHelper;

enum QualifierEnum: string
{
    use EnumHelper;
    case HOME = 'home';
    case AWAY = 'away';
}
