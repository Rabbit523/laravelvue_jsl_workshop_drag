<?php

//
namespace App\Http\Controllers\Api;
use App\dispatch_equipment as thiscontroller;
use App\Http\Resources\DispatchEquipmentResource as Resources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;

class DispatchEquipmentController extends Controller
{
    use CommonTrait;
   
	//Store Equipment with Dispatch
    public function store(Request $request, $id)
    {
         // Validate data
        $this->validate(request(), [
                'selectedEquipments'               	=>     'required'
        ]);

        
        if(request('isChanged'))
        {

           $this->updateEquipments($id, request('selectedEquipments'));

        }
        
        return ['message' => 'Record Added Successfully', 'id' => $id, 'reservedEquipments' => $this->reservedEquipments($id), 'reservedEquipments_withTrips' => $this->reservedEquipments_withTrips($id)];

    }

    // Reserved equipments
    public function updateEquipments($id, $selectedEquipments)
    {

        //thiscontroller::whereNotIn('equipment_id', $selectedEquipments['value'])->delete();
        $DeleteArray = [];

        foreach($selectedEquipments as $row)
        {
            if(isset($row['trips_id'])){
                array_push($DeleteArray, $row['value']);
            }
        }

        thiscontroller::whereNotIn('equipment_id', $DeleteArray)->where('dispatch_id', $id)->delete();


        foreach($selectedEquipments as $row)
        {
            
            if(!isset($row['trips_id']))
            {
                $new            =   new thiscontroller;

                $new->dispatch_id           =   $id;
                $new->equipment_id          =   $row['value'];
                $new->status                =   1;

                // Save
                $new->save();
            }

        }


    }

    // Get all equipments by dispatch id
    public function getAllByDispatch($dispatch_id)
    {

        return $this->getDispatchData($dispatch_id);

    }

    public function getEquipmentStatus($dispatch_id, $equipment_id)
    {

        $result = thiscontroller::find($dispatch_id);

        return new Resources(
                    thiscontroller::where('equipment_id', $equipment_id)
                                    ->where('dispatch_id', $dispatch_id)
                                    ->firstOrFail()
        );

        // return Resources::collection(
        //     thiscontroller::where('dispatch_id', $dispatch_id)
        //                     ->where('equipment_id', $equipment_id)
        //                     ->get()
        //                 );

    }

    public function updateStatus($dispatch_id, $equipment_id, $status)
    {

        if($status == 1){

            $status = 11;

        } else  if($status == 11){

            $status = 12;

        }

         \DB::table('dispatch_equipment')
            ->where('equipment_id', $equipment_id)
            ->where('dispatch_id', $dispatch_id)
            ->update(
                [
                    // 'areaName'              =>   request('areaName'), Area Name Can't Be Changed
                    'status'               =>   $status
        ]);

        return ['message' => 'Record Added Successfully', 'id' => 1, 'status' => $status];

    }


}
