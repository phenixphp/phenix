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

class ForgotPasswordController extends Controller
{
    public function store(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user = User::query()
            ->whereEqual('email', $request->body('email'))
            ->whereNotNull('email_verified_at')
            ->first();

        if ($user !== null) {
            $otpCount = UserOtp::query()
                ->whereEqual('user_id', $user->id)
                ->whereEqual('scope', OneTimePasswordScope::RESET_PASSWORD->value)
                ->whereGreaterThanOrEqual('created_at', Date::now()->subHour()->toDateTimeString())
                ->count();

            if ($otpCount < 5) {
                $user->sendOneTimePassword(OneTimePasswordScope::RESET_PASSWORD);
            }
        }

        return response()->json([
            'message' => trans('auth.password_reset.sent'),
        ], HttpStatus::OK);
    }
}
