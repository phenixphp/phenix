<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\OneTimePasswordScope;
use App\Mail\SendEmailVerificationOtp;
use App\Mail\SendLoginOtp;
use App\Queries\UserQuery;
use Phenix\Database\Models\Attributes\Column;
use Phenix\Database\Models\Attributes\DateTime;
use Phenix\Database\Models\Attributes\Hidden;
use Phenix\Database\Models\Attributes\Id;
use Phenix\Database\Models\DatabaseModel;
use Phenix\Facades\Mail;
use Phenix\Mail\Mailable;
use Phenix\Util\Date;

class User extends DatabaseModel
{
    #[Id]
    public int $id;

    #[Column]
    public string $name;

    #[Column]
    public string $email;

    #[Hidden]
    public string $password;

    #[DateTime(name: 'created_at', autoInit: true)]
    public Date $createdAt;

    #[DateTime(name: 'updated_at')]
    public Date|null $updatedAt = null;

    public static function table(): string
    {
        return 'users';
    }

    protected static function newQueryBuilder(): UserQuery
    {
        return new UserQuery();
    }

    public function createOneTimePassword(OneTimePasswordScope $scope): UserOtp
    {
        $userOtp = UserOtp::make($scope);
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
