<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Company::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'owner' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'audio_subrent' => 'required|boolean',
            'video_subrent' => 'required|boolean',
            'vehicle_subrent' => 'required|boolean',
            'storage_subrent' => 'required|boolean',
            'stage_subrent' => 'required|boolean'
        ]);

        return response()->json(Company::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): JsonResponse
    {
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required',
            'address' => 'sometimes|required',
            'owner' => 'sometimes|required',
            'phone' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'audio_subrent' => 'sometimes|required|boolean',
            'video_subrent' => 'sometimes|required|boolean',
            'vehicle_subrent' => 'sometimes|required|boolean',
            'storage_subrent' => 'sometimes|required|boolean',
            'stage_subrent' => 'sometimes|required|boolean'
        ]);


        $company->update($validated);

        return response()->json(['message' => 'Updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return response()->json(null, 204);

    }
}
