<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command('push:send-scheduled --limit=100')
  ->everyMinute()
  ->timezone(config('app.timezone', 'Asia/Jakarta'))
  ->withoutOverlapping(10)
  ->appendOutputTo(storage_path('logs/push-scheduler.log'));
