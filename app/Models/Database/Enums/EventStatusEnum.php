<?php

namespace App\Models\Database\Enums;

use App\Traits\EnumHelper;

enum EventStatusEnum: string
{
    use EnumHelper;
    case SCHEDULED = 'scheduled';

    case IN_PLAY = 'in_play';

    case FINISHED = 'finished';

    case CANCELLED = 'cancelled';
}
