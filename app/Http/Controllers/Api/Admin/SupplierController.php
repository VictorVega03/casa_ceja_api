<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return $this->success($suppliers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:200',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:300',
            'active'  => 'nullable|boolean',
        ]);

        $supplier = Supplier::create($validated);
        return $this->success($supplier, 'Proveedor creado correctamente', 201);
    }

    public function show(Supplier $supplier)
    {
        return $this->success($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'    => 'sometimes|string|max:200',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:300',
            'active'  => 'nullable|boolean',
        ]);

        $supplier->update($validated);
        return $this->success($supplier, 'Proveedor actualizado correctamente');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return $this->success(null, 'Proveedor eliminado correctamente');
    }
}