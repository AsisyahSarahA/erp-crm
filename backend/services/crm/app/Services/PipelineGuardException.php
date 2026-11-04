<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class PipelineGuardException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $guardFailureCode,
        public readonly string $deepLinkTab = 'overview',
        public readonly int $httpStatus = 422,
        public readonly array $extra = [],
    ) {
        parent::__construct($message, $httpStatus);
    }

    public function toProblemDetails(string $instance = ''): array
    {
        return array_merge([
            'type' => 'https://astacode.id/errors/crm-pipeline-guard',
            'title' => $this->httpStatus === 403 ? 'Forbidden Stage Override' : 'Pipeline Transition Guard Failed',
            'status' => $this->httpStatus,
            'detail' => $this->getMessage(),
            'message' => $this->getMessage(),
            'guard_failure' => $this->guardFailureCode,
            'deep_link_tab' => $this->deepLinkTab,
            'instance' => $instance,
        ], $this->extra);
    }
}

