<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserToken;
use App\Traits\ApiResponse;

class TokenController extends Controller
{
    use ApiResponse;

    /// Lista todos los tokens de usuario
    public function index()
    {
        $tokens = UserToken::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($tokens);
    }

    /// Revoca el token de un usuario — fuerza re-login
    public function destroy(UserToken $userToken)
    {
        $userToken->delete();
        return $this->success(null, 'Token revocado correctamente');
    }
}