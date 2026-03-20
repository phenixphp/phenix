<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use App\Models\UserOtp;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Http\Controller;
use Phenix\Http\Request;
use Phenix\Http\Response;
use Phenix\Util\Date;
use Phenix\Validation\Types\Email;
use Phenix\Validation\Validator;

class ResendVerificationOtpController extends Controller
{
    public function resend(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100)
                ->exists('users', 'email', function ($query): void {
                    $query->whereNull('email_verified_at');
                }),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        /** @var User $user */
        $user = User::query()->whereEqual('email', $request->body('email'))->first();

        $otpCount = UserOtp::query()
            ->whereEqual('user_id', $user->id)
            ->whereEqual('scope', OneTimePasswordScope::VERIFY_EMAIL->value)
            ->whereGreaterThanOrEqual('created_at', Date::now()->subHour()->toDateTimeString())
            ->count();

        if ($otpCount >= 5) {
            return response()->json([
                'message' => trans('auth.otp.limit_exceeded'),
            ], HttpStatus::TOO_MANY_REQUESTS);
        }

        $user->sendOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        return response()->json([
            'message' => trans('auth.otp.email_verification.resent'),
        ], HttpStatus::OK);
    }
}
