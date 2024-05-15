<?php

namespace App\Http\Controllers;

use App\Models\EventMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventMessageController extends Controller
{
    public function index(Request $request, string $id): JsonResponse
    {
        return response()->json(EventMessage::select('sender_name', 'message')->where('event_id', $id)->get());
    }

    public function store(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'message' => 'required'
        ]);


        EventMessage::create([
            'sender_id' => $request->user()->id,
            'sender_name' => $request->user()->name,
            'event_id' => $id,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Message successfully sent.'],201);
    }

    public function destroy(Request $request, string $event_id, string $message_id): JsonResponse
    {

        EventMessage::where('event_id', $event_id)->where('id', $message_id)->where('sender_id', $request->user()->id)->delete();
        return response()->json( null,204);
    }
}
