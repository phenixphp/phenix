<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\OneTimePasswordScope;
use App\Models\User;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Phenix\App;
use Phenix\Facades\Hash;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Http\Controller;
use Phenix\Http\Request;
use Phenix\Http\Response;
use Phenix\Validation\Types\Email;
use Phenix\Validation\Types\Password;
use Phenix\Validation\Types\Str;
use Phenix\Validation\Validator;

class RegisterController extends Controller
{
    public function store(Request $request): Response
    {
        $validator = new Validator($request);
        $validator->setRules([
            'name' => Str::required()->min(3)->max(20)->unique('users', 'name'),
            'email' => Email::required()->validations(
                new DNSCheckValidation(),
                new NoRFCWarningsValidation()
            )->max(100)->unique('users', 'email'),
            'password' => Password::required()->secure(static fn (): bool => App::isProduction())->confirmed(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->name = $request->body('name');
        $user->email = $request->body('email');
        $user->password = Hash::make($request->body('password'));
        $user->save();

        $user->sendOneTimePassword(OneTimePasswordScope::VERIFY_EMAIL);

        return response()->json($user, HttpStatus::CREATED);
    }
}
