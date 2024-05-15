<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $events = Event::paginate(15);
        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedEvent = $request->validate([
            'location' => ['sometimes', 'required'],
            'phone' => ['sometimes', 'required'],
            'client_id' => ['sometimes', 'required', 'numeric'],
            'user_id' => 'prohibited',
            'start_date' => 'sometimes|required|date|before_or_equal:end_date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'event_type' => [
                'sometimes', 'required',
                Rule::in(['Private', 'Public', 'Rent Order']),
            ],
            'event_subtype' => [
                'sometimes', 'required',
                Rule::in(['Wedding', 'Birthday', 'Corporate', 'Concert', 'Town Celebration', 'Holiday Related', 'Other']),
            ],
            'description' => ['sometimes', 'required'],
            'event_status' => [
                'sometimes', 'required',
                Rule::in(['Draft']),
            ],
        ]);

        $validatedEvent = Arr::add($validatedEvent, 'user_id', $request->user()->id);
        $validatedEquipment = $request->validate([
            'equipment_id' => 'nullable',
        ]);
        $validatedVehicles = $request->validate([
            'vehicles_id' => 'nullable',
        ]);
        $validatedStaff = $request->validate([
            'staff_id' => 'nullable',
        ]);
        $event = Event::create($validatedEvent);

        if(count($validatedEquipment) > 0) {
            for($i = 0; $i < count($validatedEquipment['equipment_id']); $i++){
                $event->equipment()->attach($validatedEquipment['equipment_id'][$i][0], ['equipment_amount' => $validatedEquipment['equipment_id'][$i][1]]);
            }
        }
        if(count($validatedVehicles) > 0) {
            for($i = 0; $i < count($validatedVehicles['vehicles_id']); $i++){
                $event->vehicles()->attach($validatedVehicles['vehicles_id'][$i][0], ['vehicle_amount' => $validatedVehicles['vehicles_id'][$i][1]]);
            }
        }
        if(count($validatedStaff) > 0) {
            $event->staff()->attach(Arr::collapse($validatedStaff));
        }


        return response()->json($event, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Event $event): JsonResponse
    {
        //$eqList = DB::table('events_equipment')->where('events_id', $event->id)->get();
        $eventData = $event->with('equipment')->with('vehicles')->with('staff')->where('id', $event->id)->get();

        return response()->json($eventData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $validatedEvent = $request->validate([
            'location' => 'sometimes|required',
            'user_id' => 'sometimes|prohibited',
            'client_id' => 'sometimes|required|numeric',
            'start_date' => 'sometimes|required|date|before_or_equal:end_date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'event_type' => [
                'sometimes', 'required',
                Rule::in(['Private', 'Public', 'Rent Order']),
            ],
            'event_subtype' => [
                'sometimes', 'required',
                Rule::in(['Wedding', 'Birthday', 'Corporate', 'Concert', 'Town Celebration', 'Holiday Related', 'Other']),
            ],
            'description' => 'sometimes|required',
            'event_status' => [
                'sometimes', 'required',
                Rule::in(['Draft', 'Planning', 'In Progress', 'Complete']),
            ]
        ]);
        if(($event->event_status == 'Draft' && ($validatedEvent['event_status'] != 'Planning' || $validatedEvent['event_status'] != 'Draft')) &&
           ($event->event_status == 'Planning' && $validatedEvent['event_status'] != 'In Progress') &&
           ($event->event_status == 'In Progress' && $validatedEvent['event_status'] != 'Completed'))
        {
            return response()->json(['message' => 'Bad status.'], 400);
        }
        $event->update($validatedEvent);

        $validatedEquipment = $request->validate([
            'equipment_id' => 'nullable',
        ]);
        $validatedVehicles = $request->validate([
            'vehicles_id' => 'nullable',
        ]);
        $validatedStaff = $request->validate([
            'staff_id' => 'nullable',
        ]);

        $eqSyncData = [];
        $vehSyncData = [];
        $oldEventEquipment = DB::table('events_equipment')->select('equipment_id', 'equipment_amount')->where('events_id', $event->id)->get();
        $oldEventVehicles = DB::table('events_vehicles')->select('vehicles_id', 'vehicle_amount')->where('events_id', $event->id)->get();

        if($validatedEvent['event_status'] != 'Complete' && $validatedEvent['event_status'] != 'Draft') {
            foreach ($oldEventEquipment as &$item) {
                DB::table('equipment')->where('id', $item->equipment_id)->update([
                    'amount_available' => DB::raw("amount_available + '$item->equipment_amount'")
                ]);
            }
            foreach ($oldEventVehicles as &$item) {
                DB::table('vehicles')->where('id', $item->vehicles_id)->update([
                    'amount_available' => DB::raw("amount_available + '$item->vehicle_amount'")
                ]);
            }
        }
        if(count($validatedEquipment) > 0) {
            for($i = 0; $i < count($validatedEquipment['equipment_id']); $i++){
                $eqSyncData[$validatedEquipment['equipment_id'][$i][0]] = ['equipment_amount' => $validatedEquipment['equipment_id'][$i][1]];
            }
            $event->equipment()->sync($eqSyncData);
        }
        else if($validatedEvent['event_status'] != 'Complete')
            DB::table('events_equipment')->where('events_id', $event->id)->delete();

        if(count($validatedVehicles) > 0) {
            for($i = 0; $i < count($validatedVehicles['vehicles_id']); $i++){
                $vehSyncData[$validatedVehicles['vehicles_id'][$i][0]] = ['vehicle_amount' => $validatedVehicles['vehicles_id'][$i][1]];
            }
            $event->vehicles()->sync($vehSyncData);
        }
        else if($validatedEvent['event_status'] != 'Complete')
            DB::table('events_vehicles')->where('events_id', $event->id)->delete();

        if(count($validatedStaff) > 0) {
            $event->staff()->sync(Arr::collapse($validatedStaff));
        }
        else if($validatedEvent['event_status'] != 'Complete')
            DB::table('events_users')->where('events_id', $event->id)->delete();

        if($validatedEvent['event_status'] == 'Complete'){
            $eqFromEvent = DB::table('events_equipment')->select('equipment_id', 'equipment_amount')->where('events_id', $event->id)->get();
            $vehFromEvent = DB::table('events_vehicles')->select('vehicles_id', 'vehicle_amount')->where('events_id', $event->id)->get();
            foreach($eqFromEvent as &$item){
                DB::table('equipment')->where('id', $item->equipment_id)->update([
                    'profit' => DB::raw("profit + rent_price * '$item->equipment_amount'"),
                    'amount_available' => DB::raw("amount_available + '$item->equipment_amount'")
                ]);
            }

            foreach($vehFromEvent as &$item){
                DB::table('vehicles')->where('id', $item->vehicles_id)->update([
                    'profit' => DB::raw("profit + price_per_km * '$request->km_travelled'"),
                    'amount_available' => DB::raw("amount_available + '$item->vehicle_amount'")
                ]);
            }
        }
        else if ($validatedEvent['event_status'] == 'Planning' || $validatedEvent['event_status'] == 'In Progress') {
            $eqFromEvent = DB::table('events_equipment')->select('equipment_id', 'equipment_amount')->where('events_id', $event->id)->get();
            $vehFromEvent = DB::table('events_vehicles')->select('vehicles_id', 'vehicle_amount')->where('events_id', $event->id)->get();
            foreach($eqFromEvent as &$item){
                DB::table('equipment')->where('id', $item->equipment_id)->update([
                    'amount_available' => DB::raw("amount_available - '$item->equipment_amount'")
                ]);
            }
            foreach($vehFromEvent as &$item){
                DB::table('vehicles')->where('id', $item->vehicles_id)->update([
                    'amount_available' => DB::raw("amount_available - '$item->vehicle_amount'")
                ]);
            }
        }

        return response()->json(['message' => 'Updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        $eqFromEvent = DB::table('events_equipment')->select('equipment_id', 'equipment_amount')->where('events_id', $event->id)->get();
        $vehFromEvent = DB::table('events_vehicles')->select('vehicles_id', 'vehicle_amount')->where('events_id', $event->id)->get();

        foreach($eqFromEvent as &$item) {
            DB::table('equipment')->where('id', $item->equipment_id)->update([
                'amount_available' => DB::raw("amount_available + '$item->equipment_amount'")
            ]);
        }
        foreach($vehFromEvent as &$item) {
            DB::table('vehicles')->where('id', $item->vehicles_id)->update([
                'amount_available' => DB::raw("amount_available + '$item->vehicle_amount'")
            ]);
        }
        $event->equipment()->detach();
        $event->vehicles()->detach();
        $event->staff()->detach();

        $event->delete();

        return response()->json(null, 204);

    }
}
