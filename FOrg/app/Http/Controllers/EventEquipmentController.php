<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventEquipmentController extends Controller
{
    public function ShowEventEquipment(string $id): JsonResponse
    {
        $equipmentToTake = DB::table('events_equipment')
            ->join('equipment', 'events_equipment.equipment_id','=', 'equipment.id')
            ->select('equipment.id', 'equipment.name', 'equipment.type', 'equipment.img_mime', 'equipment.img_base64', 'equipment.img_name', 'events_equipment.equipment_amount')
            ->where('events_equipment.events_id', $id)->where('events_equipment.equipment_taken', '=', '0')->get();
        $equipmentToReturn = DB::table('events_equipment')
            ->join('equipment', 'events_equipment.equipment_id','=', 'equipment.id')
            ->select('equipment.id', 'equipment.name', 'equipment.type', 'equipment.img_mime', 'equipment.img_base64', 'equipment.img_name', 'events_equipment.equipment_amount')
            ->where('events_equipment.events_id', $id)->where('events_equipment.equipment_returned', '=', '0')->where('events_equipment.equipment_taken', '=', '1')->get();
        $equipmentAll = DB::table('events_equipment')
            ->join('equipment', 'events_equipment.equipment_id','=', 'equipment.id')
            ->select('equipment.name', 'equipment.type', 'equipment.img_mime', 'equipment.img_base64', 'equipment.img_name', 'events_equipment.equipment_amount')
            ->where('events_equipment.events_id', $id)->get();

        $forInvoice = DB::table('events_equipment')
            ->join('equipment', 'events_equipment.equipment_id', '=', 'equipment.id')
            ->select('equipment.name', 'equipment.rent_price','events_equipment.equipment_amount')->where('events_id', $id)->get();
        return response()->json(['toTake' => $equipmentToTake, 'toReturn' => $equipmentToReturn, 'all_eq' => $equipmentAll, 'for_invoice' => $forInvoice]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function UpdateEquipment (Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'eq_ids' => 'required',
            'option' => 'required'
        ]);
        if($data['option'] == 'take') {
            DB::table('events_equipment')->where('events_id', $id)->whereIn('equipment_id', $data['eq_ids'])->update(['equipment_taken' => 1, 'taken_by' => $request->user()->name]);
        }
        else if($data['option'] == 'return'){
            DB::table('events_equipment')->where('events_id', $id)->whereIn('equipment_id', $data['eq_ids'])->update(['equipment_returned' => 1, 'returned_by' => $request->user()->name]);
            return  response()->json(['message' => 'nigger']);
        }
        return  response()->json(['message' => 'Successfully updated.']);
    }


}
