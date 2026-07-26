<?php

use Illuminate\Support\Facades\Schedule;
use \App\Models\story\storyModel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use \App\Console\Commands\ExpiredStories;

Schedule::command(ExpiredStories::class)
    ->everyThirtyMinutes()
    ->withoutOverlapping();




