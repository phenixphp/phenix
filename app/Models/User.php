<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendEmailVerificationOtp;
use App\Mail\SendLoginOtp;
use Phenix\Auth\User as Authenticable;
use Phenix\Facades\Mail;
use Phenix\Mail\Mailable;

class User extends Authenticable
{
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
        return match ($scope) {
            OneTimePasswordScope::VERIFY_EMAIL => new SendEmailVerificationOtp($userOtp),
            OneTimePasswordScope::LOGIN => new SendLoginOtp($userOtp),
        };
    }
}
