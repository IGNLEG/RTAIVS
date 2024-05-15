<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Vehicle::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required',
            'license_plate' => 'required',
            'make' => [
                'required',
                Rule::in(['Mercedes', 'Renault', 'Opel/Vauxhall', 'Ford', 'Volkswagen']),
            ],
            'model' => 'required',
            'amount_total' => 'required|integer',
            'amount_available' => 'required|integer',
            'gearbox' => [
                'required',
                Rule::in(['Manual', 'Automatic']),
            ],
            'mileage' => 'required|integer',
            'mpg' => 'required|decimal:0,1',
            'price_per_km' => 'required|decimal:0,2',
            'problems' => 'sometimes|required',
            'insurance_until' => 'required|date',
            'inspection_until' => 'required|date',
        ]);

        return response()->json(Vehicle::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle): JsonResponse
    {
        return response()->json($vehicle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'sometimes|required',
            'license_plate' => 'sometimes|required',
            'make' => [
                'sometimes','required',
                Rule::in(['Mercedes', 'Renault', 'Opel/Vauxhall', 'Ford', 'Volkswagen']),
            ],
            'model' => 'sometimes|required',
            'amount_total' => 'sometimes|integer',
            'amount_available' => 'sometimes|integer',
            'gearbox' => [
                'sometimes',
                'required',
                Rule::in(['Manual', 'Automatic']),
            ],
            'mileage' => 'sometimes|required|integer',
            'mpg' => 'sometimes|required|decimal:0,1',
            'price_per_km' => 'sometimes|required|decimal:0,2',
            'problems' => 'sometimes|required',
            'insurance_until' => 'sometimes|required|date',
            'inspection_until' => 'sometimes|required|date',
        ]);


        $vehicle->update($validated);

        return response()->json(null,201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle): JsonResponse
    {
        $vehicle->delete();
        return response()->json(null,204);

    }
}
