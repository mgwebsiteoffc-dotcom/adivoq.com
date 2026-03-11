<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// reminders job

Schedule::command('invoices:send-reminders')->hourly();

// Every night mark overdue invoices
Schedule::command('invoices:mark-overdue')->dailyAt('00:10');

// 1st day of month reset usage counters
Schedule::command('tenants:reset-monthly-counters')->monthlyOn(1, '00:30');

Schedule::command('invoices:generate-recurring')->dailyAt('01:10');

// Apply pending plan changes daily
Schedule::command('subscriptions:apply-pending-changes')->dailyAt('02:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
