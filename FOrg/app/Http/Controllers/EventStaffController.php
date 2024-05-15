<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventStaffController extends Controller
{
    public function getEmployeeEventsByDate(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);
        $from = $request->from;
        $to = $request->to;

        $eventsInfo = DB::table('events_users')->join('events', 'events_users.events_id', '=', 'events.id')
            ->where('events_users.users_id', '=', $request->user()->id)
            ->where('events.start_date', '>=', $from)
            ->where('events.end_date', '<=', $to)
            ->where('events.event_status', '!=', 'Completed')
            ->select('events.id', 'events.location', 'events.start_date as start', 'events.end_date as end', 'events.event_type as type', 'events.event_subtype as subtype')
            ->get();

        return response()->json($eventsInfo);
    }

    public function getEventEmployees(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required'
        ]);
        $event = Event::where('id', $request->event_id)->first();
        return response()->json($event->staff());
    }
}
