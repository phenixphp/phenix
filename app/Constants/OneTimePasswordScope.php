<?php

declare(strict_types=1);

namespace App\Constants;

enum OneTimePasswordScope: string
{
    case LOGIN = 'login';

    case RESET_PASSWORD = 'reset_password';

    case VERIFY_EMAIL = 'verify_email';

    case AUTHORIZE = 'authorize';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
