<?php

declare(strict_types=1);

namespace App\Tasks;

use Amp\Cancellation;
use Amp\Sync\Channel;
use App\Models\UserOtp;
use Phenix\Tasks\QueuableTask;
use Phenix\Tasks\Result;
use Phenix\Util\Date;

class DeleteExpiredUserOtps extends QueuableTask
{
    protected function handle(Channel $channel, Cancellation $cancellation): Result
    {
        $deleted = UserOtp::query()
            ->whereNull('used_at')
            ->whereLessThan('expires_at', Date::now()->toDateTimeString())
            ->delete();

        return Result::success($deleted);
    }
}
