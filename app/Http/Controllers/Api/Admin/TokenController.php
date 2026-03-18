<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BranchToken;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $tokens = BranchToken::with('branch')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($tokens);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'name'      => 'nullable|string|max:100',
        ]);

        $token = BranchToken::create([
            'branch_id' => $validated['branch_id'],
            'name'      => $validated['name'] ?? null,
            'token'     => Str::random(64),
            'active'    => true,
        ]);

        $token->load('branch');
        return $this->success($token, 'Token generado correctamente', 201);
    }

    public function toggle(BranchToken $branchToken)
    {
        $branchToken->update(['active' => !$branchToken->active]);
        $status = $branchToken->active ? 'activado' : 'desactivado';
        return $this->success($branchToken, "Token {$status} correctamente");
    }

    public function destroy(BranchToken $branchToken)
    {
        $branchToken->delete();
        return $this->success(null, 'Token eliminado correctamente');
    }
}