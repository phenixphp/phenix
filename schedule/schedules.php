<?php

declare(strict_types=1);

use App\Models\UserOtp;
use Phenix\Auth\PersonalAccessToken;
use Phenix\Facades\Schedule;
use Phenix\Util\Date;

Schedule::timer(function (): void {
    UserOtp::query()
        ->whereNull('used_at')
        ->whereLessThan('expires_at', Date::now()->toDateTimeString())
        ->delete();
})->everyMinute();

Schedule::timer(function (): void {
    PersonalAccessToken::query()
        ->whereLessThanOrEqual('expires_at', Date::now()->toDateTimeString())
        ->delete();
})->everyMinute();