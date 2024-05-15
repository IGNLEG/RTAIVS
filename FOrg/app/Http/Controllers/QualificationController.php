<?php

namespace App\Http\Controllers;

use App\Models\Qualification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Qualification::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);

        return response()->json(Qualification::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Qualification $qualification): JsonResponse
    {
        return response()->json($qualification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Qualification $qualification): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);


        $qualification->update($validated);

        return response()->json(['message' => 'Updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Qualification $qualification): JsonResponse
    {
        $qualification->delete();
        return response()->json(null, 204);

    }
}
