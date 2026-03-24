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

    \Log::info('UserTokenMiddleware', [
        'token_received' => $token ? substr($token, 0, 8).'...' : 'null',
        'token_length'   => $token ? strlen($token) : 0,
    ]);

    if (!$token) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Token requerido',
        ], 401);
    }

    $userToken = UserToken::with('user')
                          ->where('token', $token)
                          ->first();

    \Log::info('UserToken lookup', [
        'found'       => $userToken ? 'yes' : 'no',
        'user_active' => $userToken?->user?->active,
    ]);

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