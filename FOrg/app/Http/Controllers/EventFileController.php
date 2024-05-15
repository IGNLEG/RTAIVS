<?php

namespace App\Http\Controllers;

use App\Models\EventFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id): Collection
    {
        return EventFile::select('id', 'name')->where('event_id', $id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'mimes:pdf,jpg,png,xls,xlsx',
            'name' => 'required|max:255',
            'event_id' => 'required|integer'
        ]);
        if($request->hasFile('file')){
            $path = $request->file('file')->getRealPath();
            $ext = $request->file->extension();
            $doc = file_get_contents($path);
            $base64 = base64_encode($doc);
            $mime = $request->file('file')->getClientMimeType();

            EventFile::create([
                'event_id' => $request->event_id,
                'name' => $request->name.'.'.$ext,
                'file' => $base64,
                'mime' => $mime,
            ]);
        }
        return response()->json(['message' => 'Uploaded successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        return response()->json(EventFile::where('id', $id)->get());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {

        DB::table('event_files')->where('id', $id)->delete();
        return response()->json(null,204);

    }
}
