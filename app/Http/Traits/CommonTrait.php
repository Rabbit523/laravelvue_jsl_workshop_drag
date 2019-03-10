<?php
namespace App\Http\Traits;

use App\dispatch_equipment;
use App\logs;

trait CommonTrait {

    // Get all equipments by dispatch id
    public function reservedEquipments($dispatch_id)
    {

        $Equipments = \DB::table('equipment')

        ->select('equipment.id', \DB::raw('CONCAT(equipment.equipmentName, " - ", equipment.equipmentNumber) AS equipmentName'), 'equipment.status', 'dispatch_equipment.dispatch_id')

        ->Join('dispatch_equipment', 'equipment.id', '=', 'dispatch_equipment.equipment_id')

        ->where('dispatch_equipment.dispatch_id', '=', $dispatch_id)

        ->get();

        return $Equipments;

    }

    // Get all equipments with trips
    public function reservedEquipments_withTrips($dispatch_id)
    {
       
        $Equipments = \DB::table('equipment')

        ->select('equipment.id', 'equipment.equipmentName', 'equipment.equipmentNumber', 'equipment.status')

        ->Join('equipment_trip', 'equipment.id', '=', 'equipment_trip.equipment_id')

        ->where('equipment_trip.dispatch_id', '=', $dispatch_id)

        ->groupBy('equipment.id')

        ->get();

        return $Equipments;


    }

    //Save in Log
    Public function savetoLog($tableName, $record_id, $fields)
    {

        $data = '';
        foreach($fields as $field)
        {

            $data .= $field . '|';

        }

        $new        =   new logs;

        $new->tableName           =     $tableName;
        $new->record_id           =     $record_id;
        $new->data                =     $data;      

        //Save
        $new->save();


    }
}