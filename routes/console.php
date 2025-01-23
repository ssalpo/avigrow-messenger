<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:update-expired-tokens')->everyMinute();
Schedule::command('app:change-account-status')->everyFiveMinutes();
Schedule::command('app:clear-inactive-conversations')->cron('*/40 * * * *');
Schedule::command('app:clear-unused-fm-tags')->dailyAt('1:00');
Schedule::command('app:close-inactive-bot-states')->daily();
Schedule::command('app:import-ads-for-accounts')->weekly()->saturdays()->at('3:00');
Schedule::command('app:import-ads-for-accounts')->dailyAt('3:00');
Schedule::command('app:import-reviews')->dailyAt('2:00');
Schedule::command('app:process-review-answers')->everyThirtyMinutes();
