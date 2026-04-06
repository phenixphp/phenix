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
use Phenix\Validation\Types\Numeric;
use Phenix\Validation\Validator;

class VerifyEmailController extends Controller
{
    public function verify(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100)
                ->exists('users', 'email', function ($query) use ($request): void {
                    $query->whereEqual('email', $request->body('email'))
                        ->whereNull('email_verified_at');
                }),
            'otp' => Numeric::required()->digits(6),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user = User::query()->whereEqual('email', $request->body('email'))->first();

        $otp = UserOtp::query()
            ->whereEqual('user_id', $user->id)
            ->whereEqual('scope', OneTimePasswordScope::VERIFY_EMAIL->value)
            ->whereEqual('code', hash('sha256', (string) $request->body('otp')))
            ->whereNull('used_at')
            ->whereGreaterThanOrEqual('expires_at', Date::now()->toDateTimeString())
            ->first();

        if (! $otp) {
            return response()->json([
                'message' => trans('auth.otp.invalid'),
            ], HttpStatus::NOT_FOUND);
        }

        $otp->usedAt = Date::now();
        $otp->save();

        $user->emailVerifiedAt = Date::now();
        $user->save();

        return response()->json([
            'message' => trans('auth.email_verification.verified'),
        ], HttpStatus::OK);
    }
}
