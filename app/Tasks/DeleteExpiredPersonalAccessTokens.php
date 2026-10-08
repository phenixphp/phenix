<?php

declare(strict_types=1);

namespace App\Tasks;

use Amp\Cancellation;
use Amp\Sync\Channel;
use Phenix\Auth\PersonalAccessToken;
use Phenix\Tasks\QueuableTask;
use Phenix\Tasks\Result;
use Phenix\Util\Date;

class DeleteExpiredPersonalAccessTokens extends QueuableTask
{
    protected function handle(Channel $channel, Cancellation $cancellation): Result
    {
        $deleted = PersonalAccessToken::query()
            ->whereLessThanOrEqual('expires_at', Date::now()->toDateTimeString())
            ->delete();

        return Result::success($deleted);
    }
}
