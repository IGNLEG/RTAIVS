<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventsVehicleController extends Controller
{
    public function ShowEventVehicles(string $id): JsonResponse
    {
        $vehiclesToTake = DB::table('events_vehicles')
            ->join('vehicles', 'events_vehicles.vehicles_id','=', 'vehicles.id')
            ->select('vehicles.id', 'vehicles.license_plate', 'vehicles.make', 'vehicles.model', 'events_vehicles.vehicle_amount')
            ->where('events_vehicles.events_id', $id)->where('events_vehicles.vehicle_taken', '=', '0')->get();
        $vehiclesToReturn = DB::table('events_vehicles')
            ->join('vehicles', 'events_vehicles.vehicles_id','=', 'vehicles.id')
            ->select('vehicles.id', 'vehicles.license_plate', 'vehicles.make', 'vehicles.model', 'events_vehicles.vehicle_amount')
            ->where('events_vehicles.events_id', $id)->where('events_vehicles.vehicle_returned', '=', '0')->where('events_vehicles.vehicle_taken', '=', '1')->get();
        $vehiclesAll = DB::table('events_vehicles')
            ->join('vehicles', 'events_vehicles.vehicles_id','=', 'vehicles.id')
            ->select('vehicles.id', 'vehicles.license_plate', 'vehicles.make', 'vehicles.model', 'events_vehicles.vehicle_amount', 'vehicles.price_per_km')
            ->where('events_vehicles.events_id', $id)->get();
        return response()->json(['toTake' => $vehiclesToTake, 'toReturn' => $vehiclesToReturn, 'all_veh' => $vehiclesAll]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function UpdateVehicles (Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'veh_ids' => 'required',
            'option' => 'required'
        ]);
        if($data['option'] == 'take') {
            DB::table('events_vehicles')->where('events_id', $id)->whereIn('vehicles_id', $data['veh_ids'])->update(['vehicle_taken' => 1, 'taken_by' => $request->user()->name]);
        }
        else if($data['option'] == 'return'){
            DB::table('events_vehicles')->where('events_id', $id)->whereIn('vehicles_id', $data['veh_ids'])->update(['vehicle_returned' => 1, 'returned_by' => $request->user()->name]);
        }
        return  response()->json(['message' => 'Successfully updated.']);
    }
}
