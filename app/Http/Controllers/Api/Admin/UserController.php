<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $users = User::with('branch')
            ->orderBy('name')
            ->get()
            ->makeHidden('password');

        return $this->success($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'nullable|email|max:50',
            'phone'     => 'nullable|string|max:20',
            'username'  => 'required|string|max:50|unique:users',
            'password'  => 'required|string|min:4',
            'user_type' => 'required|integer',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'active'    => 'nullable|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        return $this->success($user->makeHidden('password'), 'Usuario creado correctamente', 201);
    }

    public function show(User $user)
    {
        $user->load('branch');
        return $this->success($user->makeHidden('password'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:100',
            'email'     => 'nullable|email|max:50',
            'phone'     => 'nullable|string|max:20',
            'username'  => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'user_type' => 'sometimes|integer',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'active'    => 'nullable|boolean',
        ]);

        $user->update($validated);
        return $this->success($user->makeHidden('password'), 'Usuario actualizado correctamente');
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);
        return $this->success(null, 'Contraseña actualizada correctamente');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->success(null, 'Usuario eliminado correctamente');
    }
}