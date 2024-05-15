<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $inbox = Message::where('recipient_id', $request->user()->id)->get();
        $sent = Message::where('sender_id', $request->user()->id)->get();

        return response()->json(['inbox' => $inbox, 'sent' => $sent]);
    }

    public function store(Request $request){
        $request->validate([
            'recipient_name' => 'required',
            'message' => 'required'
        ]);

        $recipient = User::where('name', $request->recipient_name)->first();

        Message::create([
            'sender_id' => $request->user()->id,
            'sender_name' => $request->user()->name,
            'recipient_id' => $recipient->id,
            'recipient_name' => $request->recipient_name,
            'message' => $request->message,
            'replied_to' => $request->replied_to != null ? $request->replied_to : null,
        ]);

        return response()->json(['message' => 'Message successfully sent.'],201);
    }

    public function delete(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'option' => 'required'
        ]);

        if($request->option != 'inbox' && $request->option != 'sent'){
            return response()->json(['message' => 'Invalid request.'], 422);
        }
        $message = Message::where('id', $id)->first();
        switch ($request->option){
            case 'inbox':
                if($request->user()->id === $message->recipient_id) {
                    Message::where('id', $id)->update(['recipient_id' => null]);
                }
                break;
            case 'sent':
                if($request->user()->id === $message->sender_id) {
                    Message::where('id', $id)->update(['sender_id' => null]);
                }
                break;
        }
        $message = Message::where('id', $id)->first();
        if($message->recipient_id == null && $message->sender_id == null){
            Message::where('id', $id)->delete();
        }

        return response()->json(null, 204);
    }


}
