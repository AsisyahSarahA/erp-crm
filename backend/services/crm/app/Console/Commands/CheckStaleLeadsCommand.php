<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lead;
use Astacode\Kernel\Outbox\HasOutbox;
use Illuminate\Console\Command;

class CheckStaleLeadsCommand extends Command
{
    use HasOutbox;

    protected $signature = 'crm:leads:stale-check';

    protected $description = 'Hitung days_in_stage, flag No Next Step, dan eskalasi lead stale (BR-CRM-08 & FR-CRM-24)';

    public function handle(): int
    {
        $this->info('Memulai pengecekan lead stale & No Next Step...');

        $activeLeads = Lead::whereNotIn('stage_id', ['deal', 'deal_won', 'reject', 'lost'])
            ->with(['activities', 'customer'])
            ->get();

        $staleWarningCount = 0;
        $escalationCount = 0;
        $noNextStepCount = 0;

        foreach ($activeLeads as $lead) {
            // 1. Calculate days_in_stage
            $lastTransition = $lead->stageHistories()->latest('changed_at')->first();
            if ($lastTransition) {
                $daysInStage = max(1, (int) now()->diffInDays($lastTransition->changed_at));
            } elseif ($lead->created_at !== null) {
                $daysInStage = max((int) $lead->days_in_stage, max(1, (int) now()->diffInDays($lead->created_at)));
            } else {
                $daysInStage = max(1, (int) $lead->days_in_stage);
            }
            $lead->days_in_stage = $daysInStage;

            // 2. Check "No Next Step" (future scheduled activity)
            $hasNextStep = $lead->activities()
                ->where('status', 'SCHEDULED')
                ->where('start_at', '>=', now())
                ->exists();

            $lead->has_next_step = $hasNextStep;
            if (!$hasNextStep) {
                $noNextStepCount++;
            }

            // 3. Stale logic: ≥ 5 days in stage without activity -> stale warning; ≥ 10 days -> escalation
            $isStale = ($daysInStage >= 5);
            $lead->is_stale = $isStale;

            if ($isStale && $lead->stale_since === null) {
                $lead->stale_since = now();
            } elseif (!$isStale) {
                $lead->stale_since = null;
            }

            $lead->save();

            // 4. Emit outbox event notifications per BR-CRM-08
            if ($daysInStage >= 10) {
                $escalationCount++;
                $this->recordEvent(
                    eventName: 'lead.stale_escalation',
                    payload: [
                        'lead_id' => $lead->id,
                        'code' => $lead->code,
                        'title' => $lead->title,
                        'stage' => $lead->stage_id,
                        'days_in_stage' => $daysInStage,
                        'sales_pic_id' => $lead->sales_pic_id,
                        'sales_pic_name' => $lead->sales_pic_name,
                        'target_role' => 'sales_manager',
                        'escalated_at' => now()->toIso8601String(),
                    ],
                    aggregateType: 'Lead',
                    aggregateId: (string) $lead->id
                );
            } elseif ($daysInStage >= 5) {
                $staleWarningCount++;
                $this->recordEvent(
                    eventName: 'lead.stale_warning',
                    payload: [
                        'lead_id' => $lead->id,
                        'code' => $lead->code,
                        'title' => $lead->title,
                        'stage' => $lead->stage_id,
                        'days_in_stage' => $daysInStage,
                        'sales_pic_id' => $lead->sales_pic_id,
                        'warned_at' => now()->toIso8601String(),
                    ],
                    aggregateType: 'Lead',
                    aggregateId: (string) $lead->id
                );
            }
        }

        $this->info("Pengecekan selesai: {$activeLeads->count()} lead aktif diproses.");
        $this->info("- Lead No Next Step : {$noNextStepCount}");
        $this->info("- Lead Stale Warning (≥5 hari): {$staleWarningCount}");
        $this->info("- Lead Escalation (≥10 hari)  : {$escalationCount}");

        return Command::SUCCESS;
    }
}
