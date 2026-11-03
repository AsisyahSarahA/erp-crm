<?php

declare(strict_types=1);

namespace Astacode\Kernel\Enums;

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case ESCALATED = 'escalated';
}

