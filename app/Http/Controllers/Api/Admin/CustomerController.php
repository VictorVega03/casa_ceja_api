<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        return $this->success($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'rfc'              => 'nullable|string|max:13',
            'street'           => 'nullable|string|max:255',
            'exterior_number'  => 'nullable|string|max:20',
            'interior_number'  => 'nullable|string|max:20',
            'neighborhood'     => 'nullable|string|max:100',
            'postal_code'      => 'nullable|string|max:10',
            'city'             => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:100',
            'phone'            => 'nullable|string|max:20',
            'active'           => 'nullable|boolean',
        ]);

        $customer = Customer::create($validated);
        return $this->success($customer, 'Cliente creado correctamente', 201);
    }

    public function show(Customer $customer)
    {
        return $this->success($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'             => 'sometimes|string|max:100',
            'rfc'              => 'nullable|string|max:13',
            'street'           => 'nullable|string|max:255',
            'exterior_number'  => 'nullable|string|max:20',
            'interior_number'  => 'nullable|string|max:20',
            'neighborhood'     => 'nullable|string|max:100',
            'postal_code'      => 'nullable|string|max:10',
            'city'             => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:100',
            'phone'            => 'nullable|string|max:20',
            'active'           => 'nullable|boolean',
        ]);

        $customer->update($validated);
        return $this->success($customer, 'Cliente actualizado correctamente');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return $this->success(null, 'Cliente eliminado correctamente');
    }
}