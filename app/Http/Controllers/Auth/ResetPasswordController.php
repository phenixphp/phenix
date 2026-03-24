<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use App\Models\UserOtp;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Phenix\App;
use Phenix\Facades\Hash;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Http\Controller;
use Phenix\Http\Request;
use Phenix\Http\Response;
use Phenix\Util\Date;
use Phenix\Validation\Types\Email;
use Phenix\Validation\Types\Numeric;
use Phenix\Validation\Types\Password;
use Phenix\Validation\Validator;

class ResetPasswordController extends Controller
{
    public function store(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100),
            'otp' => Numeric::required()->digits(6),
            'password' => Password::required()->secure(static fn (): bool => App::isProduction())->confirmed(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        /** @var User|null $user */
        $user = User::query()
            ->whereEqual('email', $request->body('email'))
            ->whereNotNull('email_verified_at')
            ->first();

        if ($user === null) {
            return response()->json([
                'message' => trans('auth.otp.invalid'),
            ], HttpStatus::NOT_FOUND);
        }

        $otp = UserOtp::query()
            ->whereEqual('user_id', $user->id)
            ->whereEqual('scope', OneTimePasswordScope::RESET_PASSWORD->value)
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

        $user->password = Hash::make($request->body('password'));
        $user->save();

        $user->tokens()->delete();

        return response()->json([
            'message' => trans('auth.password_reset.reset'),
        ], HttpStatus::OK);
    }
}
