<?php

namespace App\Models\Database\Enums;

use App\Traits\EnumHelper;

enum CompetitorTypeEnum: string
{
    use EnumHelper;
    case TEAM = 'team';
    case PLAYER = 'player';
}
