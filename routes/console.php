<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune', ['--model' => \Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem::class])
    ->daily()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\ImportComicCommand::class)
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->graceTimeInMinutes(30);

Schedule::command(\App\Console\Commands\DownloadComicCoverCommand::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\DownloadChapterCommand::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\CheckComicUpdateCommand::class)
    ->everyThreeHours()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\ImportAuthorCommand::class)
    ->everySixHours()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\DownloadAuthorCoverCommand::class)
    ->everySixHours()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\SubmitRecombeeRecordCommand::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\SyncPageViewCommand::class)
    ->dailyAt('05:11')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command('auth:clear-resets')->everyFifteenMinutes();
