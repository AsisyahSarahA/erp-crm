<?php

declare(strict_types=1);

namespace Astacode\Kernel\Enums;

/**
 * Enum PipelineStage
 * The 7 canonical stages of ASTACODE CRM Pipeline.
 */
enum PipelineStage: string
{
    case NEW_LEAD = 'new_lead';
    case FOLLOW_UP = 'follow_up';
    case REQUIREMENT = 'requirement';
    case QUOTATION = 'quotation';
    case NEGOTIATION = 'negotiation';
    case DEAL = 'deal';
    case REJECT = 'reject';

    public function defaultProbability(): int
    {
        return match ($this) {
            self::NEW_LEAD => 10,
            self::FOLLOW_UP => 20,
            self::REQUIREMENT => 35,
            self::QUOTATION => 55,
            self::NEGOTIATION => 75,
            self::DEAL => 100,
            self::REJECT => 0,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW_LEAD => '#3B82F6',
            self::FOLLOW_UP => '#8B5CF6',
            self::REQUIREMENT => '#06B6D4',
            self::QUOTATION => '#F59E0B',
            self::NEGOTIATION => '#EC4899',
            self::DEAL => '#10B981',
            self::REJECT => '#EF4444',
        };
    }
}

