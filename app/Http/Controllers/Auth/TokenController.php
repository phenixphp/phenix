<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Phenix\Auth\PersonalAccessToken;
use Phenix\Http\Constants\HttpStatus;
use Phenix\Http\Controller;
use Phenix\Http\Request;
use Phenix\Http\Response;
use Phenix\Util\Date;

class TokenController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $tokens = $user->tokens()
            ->whereGreaterThan('expires_at', Date::now()->toDateTimeString())
            ->get();

        return response()->json($tokens);
    }

    public function refresh(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $token = $user->refreshToken('auth_token');

        return response()->json([
            'access_token' => $token->toString(),
            'expires_at' => $token->expiresAt()->toDateTimeString(),
            'token_type' => 'Bearer',
        ]);
    }

    public function destroy(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        /** @var PersonalAccessToken|null $token */
        $token = PersonalAccessToken::query()
            ->whereEqual('id', $request->route('id'))
            ->whereEqual('tokenable_type', User::class)
            ->whereEqual('tokenable_id', $user->id)
            ->first();

        if (! $token) {
            return response()->json([
                'message' => trans('auth.token.not_found'),
            ], HttpStatus::NOT_FOUND);
        }

        $token->delete();

        return response()->json([], HttpStatus::OK);
    }
}
