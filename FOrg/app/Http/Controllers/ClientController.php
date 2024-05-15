<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Client::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'company_id' =>'nullable',
            'phone' => 'required',
            'email' => 'required|email'
        ]);

        return response()->json(Client::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): JsonResponse
    {
        return response()->json($client);
    }
    public function name(string $id): JsonResponse
    {
        return response()->json(DB::table('clients')->where('id', '=', $id)->select('name', 'surname')->get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required',
            'surname' => 'sometimes|required',
            'company_id' =>'sometimes|nullable',
            'phone' => 'sometimes|required',
            'email' => 'sometimes|required|email'
        ]);


        $client->update($validated);

        return response()->json(['message' => 'Updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        $client->delete();
        return response()->json(null,204);

    }
}
