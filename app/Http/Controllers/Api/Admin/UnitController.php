<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $units = Unit::orderBy('name')->get();
        return $this->success($units);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:50|unique:units',
            'active' => 'nullable|boolean',
        ]);

        $unit = Unit::create($validated);
        return $this->success($unit, 'Unidad creada correctamente', 201);
    }

    public function show(Unit $unit)
    {
        return $this->success($unit);
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name'   => 'sometimes|string|max:50|unique:units,name,' . $unit->id,
            'active' => 'nullable|boolean',
        ]);

        $unit->update($validated);
        return $this->success($unit, 'Unidad actualizada correctamente');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return $this->success(null, 'Unidad eliminada correctamente');
    }
}