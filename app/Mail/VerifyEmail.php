<?php

declare(strict_types=1);

namespace App\Mail;

use Phenix\Mail\Mailable;

class VerifyEmail extends Mailable
{
    public function build(): self
    {
        return $this->view('emails.verify', [
                'title' => 'Verify Your Email Address',
                'message' => 'Please click the button below to verify your email address.',
                'actionText' => 'Verify Email',
                'actionUrl' => 'https://example.com/verify?token=some-token',
            ])
            ->subject('Verify Your Email Address');
    }
}
