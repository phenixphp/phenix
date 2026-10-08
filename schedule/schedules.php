<?php

declare(strict_types=1);

use App\Tasks\DeleteExpiredPersonalAccessTokens;
use App\Tasks\DeleteExpiredUserOtps;
use Phenix\Facades\Schedule;

Schedule::call('delete-expired-user-otps', function (): void {
    DeleteExpiredUserOtps::dispatch();
})->everyMinute();

Schedule::call('delete-expired-personal-access-tokens', function (): void {
    DeleteExpiredPersonalAccessTokens::dispatch();
})->everyMinute();
