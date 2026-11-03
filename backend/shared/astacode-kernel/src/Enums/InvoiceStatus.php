<?php

declare(strict_types=1);

namespace Astacode\Kernel\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case WAITING_MILESTONE = 'waiting_milestone';
    case READY = 'ready';
    case ISSUED = 'issued';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case VOID = 'void';
}

