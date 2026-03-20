<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use App\Models\UserOtp;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
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

class LoginController extends Controller
{
    public function login(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100)
                ->exists('users', 'email', function ($query): void {
                    $query->whereNotNull('email_verified_at');
                }),
            'password' => Password::required(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user = User::query()->whereEqual('email', $request->body('email'))->first();

        if (! Hash::verify($user->password, (string) $request->body('password'))) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], HttpStatus::UNAUTHORIZED);
        }

        $otpCount = UserOtp::query()
            ->whereEqual('user_id', $user->id)
            ->whereEqual('scope', OneTimePasswordScope::LOGIN->value)
            ->whereGreaterThanOrEqual('created_at', Date::now()->subHour()->toDateTimeString())
            ->count();

        if ($otpCount >= 5) {
            return response()->json([
                'message' => 'You have exceeded the maximum number of OTP requests. Please try again later.',
            ], HttpStatus::TOO_MANY_REQUESTS);
        }

        $user->sendOneTimePassword(OneTimePasswordScope::LOGIN);

        return response()->json([
            'message' => 'A verification code has been sent to your email address.',
        ]);
    }

    public function authorize(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100)
                ->exists('users', 'email', function ($query): void {
                    $query->whereNotNull('email_verified_at');
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
            ->whereEqual('scope', OneTimePasswordScope::LOGIN->value)
            ->whereEqual('code', hash('sha256', (string) $request->body('otp')))
            ->whereNull('used_at')
            ->whereGreaterThanOrEqual('expires_at', Date::now()->toDateTimeString())
            ->first();

        if (! $otp) {
            return response()->json([
                'message' => 'The provided OTP is invalid.',
            ], HttpStatus::NOT_FOUND);
        }

        $otp->usedAt = Date::now();
        $otp->save();

        $token = $user->createToken('auth_token');

        return response()->json([
            'access_token' => $token->toString(),
            'expires_at' => $token->expiresAt()->toDateTimeString(),
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        $user?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], HttpStatus::OK);
    }
}
