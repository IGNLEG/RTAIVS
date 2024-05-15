<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Tag::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => [
                'required',
                Rule::in(['Sound', 'Video', 'Recording', 'Storage', 'Other']),
            ],
            'name' => 'required',
        ]);

        return response()->json(Tag::create($validated), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): JsonResponse
    {
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $validated = $request->validate([
            'category' => [
                'required',
                Rule::in(['Sound', 'Video', 'Recording', 'Storage', 'Other']),
            ],
            'name' => 'required',
        ]);


        $tag->update($validated);

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();
        return response()->json(null,204);

    }
}
