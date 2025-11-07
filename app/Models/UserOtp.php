<?php

declare(strict_types=1);

namespace App\Models;

use App\Queries\UserOtpQuery;
use Phenix\Database\Models\DatabaseModel;

class UserOtp extends DatabaseModel
{
    public static function table(): string
    {
        return 'user_one_time_passwords';
    }

    protected static function newQueryBuilder(): UserOtpQuery
    {
        return new UserOtpQuery();
    }
}
