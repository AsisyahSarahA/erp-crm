<?php

declare(strict_types=1);

namespace Astacode\Kernel\Enums;

enum ProjectStatus: string
{
    case INTAKE = 'intake';
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case UAT = 'uat';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}

