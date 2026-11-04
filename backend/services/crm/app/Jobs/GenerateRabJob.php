<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\RequirementVersion;
use App\Services\AiRabGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateRabJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 60;

    public function __construct(
        public readonly int $requirementId
    ) {}

    public function handle(AiRabGeneratorService $generatorService): void
    {
        $requirement = RequirementVersion::find($this->requirementId);
        if (!$requirement) {
            return;
        }

        try {
            $generatorService->generateForRequirement($requirement);
        } catch (\Throwable $e) {
            $requirement->status = 'FAILED';
            $requirement->error_message = $e->getMessage();
            $requirement->save();

            throw $e;
        }
    }
}

