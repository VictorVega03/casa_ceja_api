<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\UserToken;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /// <summary>
    /// Login de usuario — endpoint público, sin middleware.
    /// Genera o recupera el token del usuario automáticamente.
    /// </summary>
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
                    ->where('active', true)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Credenciales inválidas', 401);
        }

        $userToken = UserToken::getOrCreateForUser($user->id);
        $userToken->touch();

        $branches = Branch::where('active', true)
                          ->get(['id', 'name']);

        return $this->success([
            'token'    => $userToken->token,
            'user'     => $user->only([
                'id', 'name', 'username', 'password', 'user_type', 'branch_id'
            ]),
            'branches' => $branches,
        ]);
    }
}