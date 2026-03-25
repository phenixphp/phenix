<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendEmailVerificationOtp;
use App\Mail\SendLoginOtp;
use App\Mail\SendResetPasswordOtp;
use Phenix\Auth\User as Authenticable;
use Phenix\Database\Models\Attributes\DateTime;
use Phenix\Facades\Mail;
use Phenix\Mail\Mailable;
use Phenix\Util\Date;

class User extends Authenticable
{
    #[DateTime(name: 'email_verified_at')]
    public Date|null $emailVerifiedAt = null;

    public function createOneTimePassword(OneTimePasswordScope $scope): UserOtp
    {
        $userOtp = UserOtp::fromScope($scope);
        $userOtp->userId = $this->id;
        $userOtp->save();

        return $userOtp;
    }

    public function sendOneTimePassword(OneTimePasswordScope $scope): void
    {
        $userOtp = $this->createOneTimePassword($scope);
        $mailable = $this->resolveMailable($scope, $userOtp);

        Mail::to($this->email)
            ->send($mailable);
    }

    protected function resolveMailable(OneTimePasswordScope $scope, UserOtp $userOtp): Mailable
    {
        /** @phpstan-ignore-next-line */
        return match ($scope) {
            OneTimePasswordScope::VERIFY_EMAIL => new SendEmailVerificationOtp($userOtp),
            OneTimePasswordScope::LOGIN => new SendLoginOtp($userOtp),
            OneTimePasswordScope::RESET_PASSWORD => new SendResetPasswordOtp($userOtp),
        };
    }
}
