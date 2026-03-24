<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\UserOtp;
use Phenix\Mail\Mailable;

class SendResetPasswordOtp extends Mailable
{
    public function __construct(
        protected UserOtp $userOtp,
    ) {
    }

    public function build(): self
    {
        return $this->view('emails.otp', [
                'title' => trans('auth.otp.reset_password.title'),
                'message' => trans('auth.otp.reset_password.message'),
                'otp' => $this->userOtp->otp,
            ])
            ->subject(trans('auth.otp.reset_password.subject'));
    }
}
