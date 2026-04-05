<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\OneTimePasswordScope;
use App\Queries\UserOtpQuery;
use Phenix\Database\Models\Attributes\BelongsTo;
use Phenix\Database\Models\Attributes\Column;
use Phenix\Database\Models\Attributes\DateTime;
use Phenix\Database\Models\Attributes\ForeignKey;
use Phenix\Database\Models\Attributes\Id;
use Phenix\Database\Models\DatabaseModel;
use Phenix\Util\Date;
use Phenix\Util\Str;

class UserOtp extends DatabaseModel
{
    #[Id]
    public string $id;

    #[Column]
    public string $scope;

    #[Column]
    public string $code;

    #[ForeignKey(name: 'user_id')]
    public int $userId;

    #[BelongsTo(foreignProperty: 'userId')]
    public User $user;

    #[DateTime(name: 'expires_at')]
    public Date $expiresAt;

    #[DateTime(name: 'used_at')]
    public Date|null $usedAt = null;

    #[DateTime(name: 'created_at', autoInit: true)]
    public Date $createdAt;

    #[DateTime(name: 'updated_at')]
    public Date|null $updatedAt = null;

    public int $otp;

    public static function table(): string
    {
        return 'user_one_time_passwords';
    }

    protected static function newQueryBuilder(): UserOtpQuery
    {
        return new UserOtpQuery();
    }

    public static function fromScope(OneTimePasswordScope $scope): self
    {
        $value = random_int(100000, 999999);

        $otp = new self();
        $otp->id = Str::uuid()->toString();
        $otp->scope = $scope->value;
        $otp->code = hash('sha256', (string) $value);
        $otp->expiresAt = Date::now()->addMinutes(config('auth.otp.expiration', 10));
        $otp->otp = $value;

        return $otp;
    }

    public function getScope(): OneTimePasswordScope
    {
        return OneTimePasswordScope::from($this->scope);
    }
}
