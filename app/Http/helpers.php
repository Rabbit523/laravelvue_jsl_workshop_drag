<?php

namespace App\Http;

class helpers {

	public function getEquipmentData($dispatch_id)
    {

       $Equipments = \DB::table('equipment')
             ->Join('dispatch_equipment', 'equipment.id', '=', 'dispatch_equipment.equipment_id')
             ->leftJoin('equipment_trip', 'dispatch_equipment.equipment_id', '=', 'equipment_trip.equipment_id')
             ->where('equipment_trip.dispatch_id', '!=', NULL)
             ->get();

        return $Equipments;

    }
}
