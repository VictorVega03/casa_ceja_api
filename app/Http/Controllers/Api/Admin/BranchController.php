<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        return $this->success($branches);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'address'      => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:40',
            'razon_social' => 'nullable|string|max:100',
            'active'       => 'nullable|boolean',
        ]);

        $branch = Branch::create($validated);
        return $this->success($branch, 'Sucursal creada correctamente', 201);
    }

    public function show(Branch $branch)
    {
        return $this->success($branch);
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name'         => 'sometimes|string|max:100',
            'address'      => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:40',
            'razon_social' => 'nullable|string|max:100',
            'active'       => 'nullable|boolean',
        ]);

        $branch->update($validated);
        return $this->success($branch, 'Sucursal actualizada correctamente');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return $this->success(null, 'Sucursal eliminada correctamente');
    }
}