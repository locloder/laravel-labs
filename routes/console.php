<?php

use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendTelegramMessageJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {
    Log::info('[Scheduler] Початок перевірки прострочених задач...');

    $staleTasks = Task::where('status', 'in_progress')
        ->where('updated_at', '<', Carbon::now()->subDays(7))
        ->get();

    foreach ($staleTasks as $task) {
        $task->update(['status' => 'expired']);

        $message = "⚠️ <b>Task Expired!</b>\n\n" .
                   "<b>ID:</b> {$task->id}\n" .
                   "<b>Title:</b> {$task->title}\n" .
                   "<b>Deadline was:</b> {$task->due_date}\n" .
                   "<b>Last active:</b> {$task->updated_at->diffForHumans()}";

        SendTelegramMessageJob::dispatch($message);
        
        Log::info("[Scheduler] Задача #{$task->id} позначена як expired.");
    }

    if ($staleTasks->isEmpty()) {
        Log::info('[Scheduler] Прострочених задач не знайдено.');
    }

})->dailyAt('08:00')
  ->appendOutputTo(storage_path('logs/scheduler.log'));

Schedule::command('app:generate-report')
    ->weeklyOn(1, '09:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));
