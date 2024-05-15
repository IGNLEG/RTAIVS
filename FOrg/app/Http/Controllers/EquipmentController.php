<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $audio = Equipment::with('tags')->where('type', 'Sound')->get();
        $video = Equipment::with('tags')->where('type', 'Video')->get();
        $recording = Equipment::with('tags')->where('type', 'Recording')->get();
        $storage = Equipment::with('tags')->where('type', 'Storage')->get();
        $other = Equipment::with('tags')->where('type', 'Other')->get();

        $equipment = [
            'audio' => $audio,
            'video' => $video,
            'recording' => $recording,
            'storage' => $storage,
            'other' => $other,
        ];

        return response()->json($equipment);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedEquipment = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'type' => [
                'required',
                Rule::in(['Sound', 'Video', 'Recording', 'Storage', 'Stage', 'Other']),
            ],
            'amount_total' => 'required|integer',
            'unit_price' => 'required|decimal:0,2',
            'rent_price' => 'required|decimal:0,2',
            'profit' => 'nullable|decimal:0,2',
            'problems' => 'sometimes|required',
            'warehouse_id' => 'required'
        ]);
        $validatedImage = $request->validate([
            'img_file' =>'mimes:jpg,png|nullable',
            'img_name' => 'nullable',
        ]);

        if($request->hasFile('img_file')){
            $path = $request->file('img_file')->getRealPath();
            $doc = file_get_contents($path);
            $base64 = base64_encode($doc);
            $mime = $request->file('img_file')->getClientMimeType();

            $equipment = Equipment::create([
                'warehouse_id' => $validatedEquipment['warehouse_id'],
                'code' => $validatedEquipment['code'],
                'name' => $validatedEquipment['name'],
                'type' => $validatedEquipment['type'],
                'amount_total' => $validatedEquipment['amount_total'],
                'amount_available' => $validatedEquipment['amount_total'],
                'unit_price' => $validatedEquipment['unit_price'],
                'rent_price' => $validatedEquipment['rent_price'],
                'profit' => Arr::exists($validatedEquipment, 'profit') ? $validatedEquipment['profit'] : 0,
                'problems' => $validatedEquipment['problems'],
                'img_name' => $validatedImage['img_name'],
                'img_mime' => $mime,
                'img_base64' => $base64,

            ]);
        }
        else {
            $equipment = Equipment::create([
                'warehouse_id' => $validatedEquipment['warehouse_id'],
                'code' => $validatedEquipment['code'],
                'name' => $validatedEquipment['name'],
                'type' => $validatedEquipment['type'],
                'amount_total' => $validatedEquipment['amount_total'],
                'amount_available' => $validatedEquipment['amount_total'],
                'unit_price' => $validatedEquipment['unit_price'],
                'rent_price' => $validatedEquipment['rent_price'],
                'profit' => Arr::exists($validatedEquipment, 'profit') ? $validatedEquipment['profit'] : 0,
                'problems' => $validatedEquipment['problems']
            ]);
        }

        $validatedTags = $request->validate([
            'tag_ids' => 'nullable'
        ]);
        if(count($validatedTags) > 0) {
            $tagIds = json_decode($validatedTags['tag_ids']);
            $equipment->tags()->attach($tagIds);
        }

        return response()->json($equipment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment): JsonResponse
    {
        $equipmentData = Equipment::with('tags')->where('id', $equipment->id)->get();
        return response()->json($equipmentData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment): JsonResponse
    {
        $validatedEquipment = $request->validate([
            'code' => 'sometimes|required',
            'name' => 'sometimes|required',
            'type' => [
                'sometimes', 'required',
                Rule::in(['Sound', 'Video', 'Recording', 'Storage', 'Other']),
            ],
            'amount_total' => 'sometimes|required|integer',
            'unit_price' => 'sometimes|required|decimal:0,2',
            'rent_price' => 'sometimes|required|decimal:0,2',
            'profit' => 'sometimes|required|decimal:0,2',
            'warehouse_id' => 'sometimes|required',
            'problems' => 'sometimes|required',
        ]);

        $validatedTags = $request->validate([
            'tag_ids' => 'sometimes|required'
        ]);



        if($request->hasFile('img_file')){
            $validatedImage = $request->validate([
                'img_file' =>'sometimes|mimes:jpg,png',
                'img_name' => 'nullable',
            ]);
            $path = $request->file('img_file')->getRealPath();
            $doc = file_get_contents($path);
            $base64 = base64_encode($doc);
            $mime = $request->file('img_file')->getClientMimeType();
            $equipment->update($validatedEquipment);
            $equipment->update([
                'img_name' => $validatedImage['img_name'],
                'img_mime' => $mime,
                'img_base64' => $base64,

            ]);
        }
        else {
            $equipment->update($validatedEquipment);
        }

        if(count($validatedTags) > 0) {
            $tagIds = json_decode($validatedTags['tag_ids']);
            $equipment->tags()->sync($tagIds);
        }

        return response()->json(['message' => 'Updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment): JsonResponse
    {
        $equipment->tags()->detach();
        $equipment->delete();
        return response()->json(null, 204);

    }
}
