<?php

declare(strict_types=1);

namespace App\Services;

use App\Integrations\Adapters\StubEmailAdapter;
use App\Integrations\Adapters\StubTelegramAdapter;
use App\Integrations\Adapters\StubWhatsAppAdapter;
use App\Integrations\Contracts\EmailAdapterInterface;
use App\Integrations\Contracts\TelegramAdapterInterface;
use App\Integrations\Contracts\WhatsAppAdapterInterface;
use App\Models\AutomationRule;
use App\Models\AutomationRun;
use Illuminate\Support\Facades\Log;

class AutomationEngineService
{
    private WhatsAppAdapterInterface $wa;
    private EmailAdapterInterface $email;
    private TelegramAdapterInterface $telegram;

    public function __construct(
        ?WhatsAppAdapterInterface $wa = null,
        ?EmailAdapterInterface $email = null,
        ?TelegramAdapterInterface $telegram = null
    ) {
        $this->wa = $wa ?? new StubWhatsAppAdapter();
        $this->email = $email ?? new StubEmailAdapter();
        $this->telegram = $telegram ?? new StubTelegramAdapter();
    }

    /**
     * Triggers automation rules matching a specific event name or trigger type.
     */
    public function trigger(string $triggerType, ?string $eventName = null, array $payload = []): array
    {
        $query = AutomationRule::where('is_active', true)
            ->where('trigger_type', $triggerType);

        if ($eventName !== null) {
            $query->where('trigger_event', $eventName);
        }

        $rules = $query->orderBy('priority', 'asc')->get();
        $results = [];

        foreach ($rules as $rule) {
            $results[] = $this->executeRule($rule, $triggerType, $payload);
        }

        return $results;
    }

    /**
     * Evaluates condition and executes rule actions.
     */
    public function executeRule(AutomationRule $rule, string $triggerType, array $payload): array
    {
        // 1. Evaluate Condition via safe JSON-logic
        $conditions = $rule->conditions ?? [];
        $matches = JsonLogicEvaluator::evaluate($conditions, $payload);

        if (!$matches) {
            return [
                'rule_id' => $rule->id,
                'rule_name' => $rule->name,
                'matched' => false,
                'status' => 'SKIPPED',
            ];
        }

        // 2. Execute Actions
        $executedActions = [];
        $status = 'SUCCESS';
        $errorMessage = null;

        foreach ($rule->actions ?? [] as $actionDef) {
            try {
                $actionResult = $this->dispatchAction($actionDef, $payload);
                $executedActions[] = $actionResult;
            } catch (\Exception $e) {
                $status = 'FAILED';
                $errorMessage = $e->getMessage();
                Log::error("Automation action failed in rule {$rule->name}: " . $e->getMessage());
                break;
            }
        }

        // 3. Record Execution Run
        AutomationRun::create([
            'rule_id' => $rule->id,
            'trigger_type' => $triggerType,
            'trigger_payload' => $payload,
            'matched_condition' => true,
            'actions_executed' => $executedActions,
            'status' => $status,
            'error_message' => $errorMessage,
            'executed_at' => now(),
        ]);

        $rule->increment('execution_count');
        $rule->update(['last_run_at' => now()]);

        return [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'matched' => true,
            'status' => $status,
            'actions' => $executedActions,
        ];
    }

    /**
     * Dispatches individual action based on action definition.
     */
    private function dispatchAction(array $action, array $context): array
    {
        $type = $action['type'] ?? 'unknown';

        return match ($type) {
            'round_robin_assign' => $this->actionRoundRobin($action, $context),
            'send_whatsapp' => $this->actionSendWhatsApp($action, $context),
            'send_email' => $this->actionSendEmail($action, $context),
            'send_telegram' => $this->actionSendTelegram($action, $context),
            'escalate_stale_lead' => $this->actionEscalateStaleLead($action, $context),
            'create_task' => $this->actionCreateTask($action, $context),
            'update_field' => $this->actionUpdateField($action, $context),
            default => [
                'type' => $type,
                'status' => 'executed_generic',
                'details' => $action,
            ],
        };
    }

    private function actionRoundRobin(array $action, array $context): array
    {
        $pool = $action['pool'] ?? ['Citra Sales', 'Dimas Sales', 'Dewi Sales'];
        static $index = 0;
        $assigned = $pool[$index % count($pool)];
        $index++;

        return [
            'action' => 'round_robin_assign',
            'assigned_user' => $assigned,
            'target_id' => $context['lead_id'] ?? $context['id'] ?? null,
        ];
    }

    private function actionSendWhatsApp(array $action, array $context): array
    {
        $phone = $action['recipient_phone'] ?? $context['phone'] ?? '+6281234567890';
        $template = $action['template'] ?? 'reminder_followup';
        $result = $this->wa->sendMessage($phone, $template, $context);

        return [
            'action' => 'send_whatsapp',
            'delivery' => $result,
        ];
    }

    private function actionSendEmail(array $action, array $context): array
    {
        $email = $action['recipient_email'] ?? $context['email'] ?? 'client@example.com';
        $subject = $action['subject'] ?? 'Pemberitahuan Otomatis ASTACODE';
        $body = $action['body'] ?? 'Pesan pengingat otomatis.';
        $result = $this->email->sendEmail($email, $subject, $body);

        return [
            'action' => 'send_email',
            'delivery' => $result,
        ];
    }

    private function actionSendTelegram(array $action, array $context): array
    {
        $chatId = $action['chat_id'] ?? 'alerts_channel';
        $message = $action['message'] ?? '🚨 Peringatan Sistem Otomasi.';
        $result = $this->telegram->sendAlert($chatId, $message);

        return [
            'action' => 'send_telegram',
            'delivery' => $result,
        ];
    }

    private function actionEscalateStaleLead(array $action, array $context): array
    {
        $days = (int) ($context['idle_days'] ?? 5);
        $escalateTo = $days >= 10 ? 'Direktur Utama' : 'Sales Manager';

        return [
            'action' => 'escalate_stale_lead',
            'escalated_to' => $escalateTo,
            'idle_days' => $days,
            'lead_id' => $context['lead_id'] ?? 'LD-UNKNOWN',
        ];
    }

    private function actionCreateTask(array $action, array $context): array
    {
        return [
            'action' => 'create_task',
            'title' => $action['title'] ?? 'Task Tindak Lanjut',
            'assignee' => $action['assignee'] ?? 'Sales',
            'due_date' => now()->addDays((int) ($action['due_in_days'] ?? 1))->toDateString(),
        ];
    }

    private function actionUpdateField(array $action, array $context): array
    {
        return [
            'action' => 'update_field',
            'field' => $action['field'] ?? 'status',
            'value' => $action['value'] ?? 'updated',
        ];
    }
}

