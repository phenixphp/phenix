<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Phenix\Constants\HttpStatus;
use Phenix\Facades\Log;
use Phenix\Http\Controller;
use Phenix\Http\Request;
use Phenix\Http\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->paginate($request->getUri());

        dump('Freder H');

        return response()->json($users);
    }

    public function store(StoreUserRequest $request): Response
    {
        $user = new User();
        $user->name = $request->body('name');
        $user->email = $request->body('email');

        if ($user->save()) {
            return response()->json($user, HttpStatus::CREATED);
        }

        return response()->json([], HttpStatus::INTERNAL_SERVER_ERROR);
    }

    public function show(Request $request): Response
    {
        $user = User::find($request->route('user'), ['id', 'name', 'email']);

        if ($user) {
            return response()->json($user);
        }

        return response()->json([], HttpStatus::NOT_FOUND);
    }

    public function update(Request $request): Response
    {
        return response()->json([]);
    }

    public function delete(Request $request): Response
    {
        return response()->json([]);
    }
}
