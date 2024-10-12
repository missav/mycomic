<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command(\App\Console\Commands\ImportComicCommand::class)
    ->everySixHours()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command(\App\Console\Commands\DownloadComicCoverCommand::class)
    ->everySixHours()
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
