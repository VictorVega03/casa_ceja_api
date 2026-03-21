<?php

namespace App\Http\Middleware;

use App\Models\UserToken;
use Closure;
use Illuminate\Http\Request;

class UserTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-User-Token');

        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token requerido',
            ], 401);
        }

        $userToken = UserToken::with('user')
                              ->where('token', $token)
                              ->first();

        if (!$userToken || !$userToken->user || !$userToken->user->active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token inválido',
            ], 401);
        }

        $userToken->touch();

        $request->merge([
            'auth_user'    => $userToken->user,
            'auth_user_id' => $userToken->user->id,
        ]);

        return $next($request);
    }
}