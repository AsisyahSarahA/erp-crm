<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Activity;
use Astacode\Kernel\Outbox\OutboxEvent;
use Illuminate\Console\Command;

class SendActivityRemindersCommand extends Command
{
    protected $signature = 'crm:activities:reminders';

    protected $description = 'Kirim pengingat aktivitas/meeting H-1 dan 1 jam sebelum jadwal ke Sales (in-app, email, WA)';

    public function handle(): int
    {
        $now = now();

        // 1. Reminder H-1 (23–25 jam sebelum start_at)
        $h1Start = $now->copy()->addHours(23);
        $h1End = $now->copy()->addHours(25);

        $h1Activities = Activity::query()
            ->where('status', 'SCHEDULED')
            ->whereNull('reminder_h1_sent_at')
            ->whereBetween('start_at', [$h1Start, $h1End])
            ->get();

        $h1Count = 0;
        foreach ($h1Activities as $activity) {
            $activity->reminder_h1_sent_at = $now;
            $activity->saveQuietly();

            OutboxEvent::create([
                'event_name' => 'activity.reminder_triggered',
                'aggregate_type' => 'Activity',
                'aggregate_id' => (string) $activity->id,
                'payload' => [
                    'activity_id' => $activity->id,
                    'title' => $activity->title,
                    'type' => $activity->type,
                    'reminder_type' => 'H_MINUS_1',
                    'start_at' => $activity->start_at?->toIso8601String(),
                    'meet_link' => $activity->meet_link,
                    'recipient_id' => $activity->created_by,
                    'channels' => ['in_app', 'email', 'whatsapp'],
                    'message' => "Pengingat H-1: {$activity->title} dijadwalkan besok pukul " . $activity->start_at?->format('H:i') . ' WIB.',
                ],
                'idempotency_key' => "reminder_h1_{$activity->id}_" . $activity->start_at?->timestamp,
                'status' => OutboxEvent::STATUS_PENDING,
                'attempts' => 0,
                'available_at' => $now,
            ]);

            $h1Count++;
        }

        // 2. Reminder 1 Jam sebelum (50–70 menit sebelum start_at)
        $oneHourStart = $now->copy()->addMinutes(50);
        $oneHourEnd = $now->copy()->addMinutes(70);

        $oneHourActivities = Activity::query()
            ->where('status', 'SCHEDULED')
            ->whereNull('reminder_1h_sent_at')
            ->whereBetween('start_at', [$oneHourStart, $oneHourEnd])
            ->get();

        $oneHourCount = 0;
        foreach ($oneHourActivities as $activity) {
            $activity->reminder_1h_sent_at = $now;
            $activity->saveQuietly();

            OutboxEvent::create([
                'event_name' => 'activity.reminder_triggered',
                'aggregate_type' => 'Activity',
                'aggregate_id' => (string) $activity->id,
                'payload' => [
                    'activity_id' => $activity->id,
                    'title' => $activity->title,
                    'type' => $activity->type,
                    'reminder_type' => 'ONE_HOUR',
                    'start_at' => $activity->start_at?->toIso8601String(),
                    'meet_link' => $activity->meet_link,
                    'recipient_id' => $activity->created_by,
                    'channels' => ['in_app', 'email', 'whatsapp'],
                    'message' => "Pengingat 1 Jam: {$activity->title} akan dimulai dalam 1 jam (" . $activity->start_at?->format('H:i') . ' WIB).' . ($activity->meet_link ? " Tautan Meet: {$activity->meet_link}" : ''),
                ],
                'idempotency_key' => "reminder_1h_{$activity->id}_" . $activity->start_at?->timestamp,
                'status' => OutboxEvent::STATUS_PENDING,
                'attempts' => 0,
                'available_at' => $now,
            ]);

            $oneHourCount++;
        }

        $this->info("Pengingat aktivitas terkirim: {$h1Count} H-1, {$oneHourCount} 1-Jam.");

        return 0;
    }
}

