<?php

namespace App\Http\Middleware;

use App\Models\BranchToken;
use Closure;
use Illuminate\Http\Request;

class BranchTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token requerido'
            ], 401);
        }

        $branchToken = BranchToken::where('token', $token)
            ->where('active', true)
            ->first();

        if (!$branchToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token inválido'
            ], 401);
        }

        $branchToken->update(['last_used_at' => now()]);
        $request->merge(['branch_id' => $branchToken->branch_id]);

        return $next($request);
    }
}