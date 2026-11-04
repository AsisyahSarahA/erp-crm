<?php

use App\Console\Commands\CheckStaleLeadsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('crm:leads:stale-check', function () {
    $cmd = new CheckStaleLeadsCommand();
    $cmd->setLaravel($this->getLaravel());
    $cmd->setOutput($this->getOutput());
    return $cmd->handle();
})->purpose('Hitung days_in_stage, flag No Next Step, dan eskalasi lead stale (BR-CRM-08 & FR-CRM-24)');

Artisan::command('crm:activities:reminders', function () {
    $cmd = new \App\Console\Commands\SendActivityRemindersCommand();
    $cmd->setLaravel($this->getLaravel());
    $cmd->setOutput($this->getOutput());
    return $cmd->handle();
})->purpose('Kirim pengingat aktivitas/meeting H-1 dan 1 jam sebelum jadwal ke Sales (FR-CRM-04)');

Schedule::command('crm:leads:stale-check')->dailyAt('08:00');
Schedule::command('crm:activities:reminders')->everyFifteenMinutes();
