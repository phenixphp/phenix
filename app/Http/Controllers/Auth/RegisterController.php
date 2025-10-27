<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Phenix\App;
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
            'email' => Email::required()->max(100)->unique('users', 'email'),
            'password' => Password::required()->secure(static fn (): bool => App::isProduction())->confirmed(),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->failing(),
            ], HttpStatus::UNPROCESSABLE_ENTITY);
        }

        $user = User::create($validator->validated());
        $user->sendVerificationEmail();

        return response()->json($user, HttpStatus::CREATED);
    }
}
