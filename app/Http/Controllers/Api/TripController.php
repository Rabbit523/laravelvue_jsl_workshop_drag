<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\trip as thiscontroller;
use App\area_trip as areacontroller;
use App\equipment_trip as equipmentcontroller;
use App\dispatch_equipment as dispatchcontroller;
use App\equipment_staff;
use App\Http\Validation\TripExists;
use App\Http\Traits\CommonTrait;
use Carbon\Carbon;


class TripController extends Controller
{
    use CommonTrait;

    //Store Equipment with Dispatch
    public function store(Request $request, $dispatchid)
    {
         // Validate data
        $this->validate(request(), [
                'selectedEquipments'               	=>     	'required',
                'selectedLocations'					=>	 	'required',
                // 'tripsDetails'                      =>       ['required', new TripExists(request('selectedEquipments')['value'])],
        ]);
        
        // $equipment_id   =    request('selectedEquipments')['value'];

        foreach (request('selectedEquipments') as $row)
        {

            $equipment_id = $row['value'];
            $stf = equipment_staff::where('equipment_id', '=', $equipment_id)->get();

            foreach($request->tripsDetails as $tripDetails)
            {

                // Create new
                $new                        =   new thiscontroller;
                $new->dispatch_id	        =	$dispatchid;
                $new->tripType              =   $tripDetails['tripType']['id'];
                $new->createdBy             =   \Auth::User()->id;
                $new->flag                  =   ($tripDetails['missing'] == false ? 0 : 1);
                $new->status                =   3;

               if($stf->count() > 0)
               {
                $new->staff1    =   $stf[0]->staff_id;
               }

               if($stf->count() > 1)
               {
                $new->staff2    =   $stf[1]->staff_id;
               }

               if($stf->count() > 2)
               {
                $new->staff3    =   $stf[2]->staff_id;
               }           

                // Save
                $new->save();


                if($new->id){

                	$this->storeEquipment($new->id, $equipment_id, $dispatchid);

                	$this->storeTripDetails($new->id, $tripDetails, $dispatchid, $tripDetails['tripLocations']);

            	} //End if new id

            } //End for each trip

        } //End of equipment for each

        return ['message' => 'Record Added Successfully', 'id' => $dispatchid, 'reservedEquipments' => $this->reservedEquipments($dispatchid), 'reservedEquipments_withTrips' => $this->reservedEquipments_withTrips($dispatchid)];

    }

    // Store Equipment
    public function storeEquipment($trip_id, $Equipment_id, $dispatchid)
    {

    	$new		=	new equipmentcontroller;

    	$new->equipment_id 		=	$Equipment_id;
    	$new->trips_id			=	$trip_id;
        $new->dispatch_id       =   $dispatchid;

    	$new->save();

    }

    // Store trip Details
    public function storeTripDetails($trip_id, $trip_details, $dispatchid, $trip_locations)
    {

        
        $i = 0;
        foreach($trip_locations as $row)
        {
            

                $new        =   new areacontroller;

                $new->area_id               =   $row['value'];
                $new->trip_id               =   $trip_id;
                $new->dispatch_id           =   $dispatchid;
                $new->sequence              =   $i;
                $new->tripStartDate         =   Carbon::parse($trip_details['startingDate'])->format('Y-m-d');
                $new->detailsofGoods        =   $trip_details['detailsofGood'];
                $new->weightandDimmension   =   $trip_details['wieghtdimension'];
                $new->consignmentNo         =   $trip_details['consignmentNumber'];
                $new->createdBy             =   \Auth::User()->id;

                if($trip_details['endingDate'] != ''){
                    $new->tripEndDate           =   Carbon::parse($trip_details['endingDate'])->format('Y-m-d');
                }

                //Save
                $new->save();

                $i++;

        }
        
        
    }



    //Update Trips
    public function updateTrips(Request $request, $dispatch_id)
    {

        foreach($request->Changed as $row)
        {
            $this->updateExisting($row, $dispatch_id);
        }

        foreach($request->Deleted as $row)
        {
            // \DB::table('trips')->where('id', $row['trip_id'])->delete();    
        \DB::table('trips')->where('id', $row['trip_id'])->update(['status' => 9, 'updatedBy' => \Auth::User()->id]); 
        \DB::table('area_trip')
            ->where('trip_id', $row['trip_id'])
            ->where('status', '=', 1)
            ->update(['status' => 9, 'updatedBy' => \Auth::User()->id]); 

        }

        // foreach($request->Created as $row){
            $this->storeMissingTrip($request->Created, $dispatch_id, $request->equipment_id);   
        // }
        return ['message' => 'Record Updated Successfully', 'id' => $dispatch_id];

    }

    //Create new trip from trip management page
    public function storeMissingTrip($tripDetails, $dispatch_id, $equipment_id)
    {

            // Validate data
            $this->validate(request(), [
                    // 'Created'                      =>       [new TripExists($equipment_id)],
            ]);

        $stf = equipment_staff::where('equipment_id', '=', $equipment_id)->get();

        foreach($tripDetails as $tripDetail)
        {

            // Create new
            $new                        =   new thiscontroller;
            $new->dispatch_id           =   $dispatch_id;
            $new->createdBy             =   \Auth::User()->id;
            $new->status                =   3;
            $new->tripType              =   $tripDetail['tripType'];
            $new->flag                  =   ($tripDetail['missing'] == false ? 0 : 1);

           if($stf->count() > 0)
           {
            $new->staff1    =   $stf[0]->staff_id;
           }

           if($stf->count() > 1)
           {
            $new->staff2    =   $stf[1]->staff_id;
           }

           if($stf->count() > 2)
           {
            $new->staff3    =   $stf[2]->staff_id;
           }           

            // Save
            $new->save();


            if($new->id){

                $this->storeEquipment($new->id, $equipment_id, $dispatch_id);

                $this->storeTripDetails($new->id, $tripDetail, $dispatch_id, $tripDetail['tripLocations']);

            }

        }


    }

    //Update Existing Trip
    public function updateExisting($data, $dispatch_id)
    {

         // Validate data
        $this->validate(request(), [
                // 'Changed'          =>       [new TripExists(request('equipment_id'))],
        ]);

        $Time = Carbon::now()->timestamp;

        // Update to status to remove from list
        \DB::table('area_trip')
            ->where('trip_id', $data['trip_id'])
            ->where('status', '=', 1)
            ->update(['status' => $Time, 'updatedBy' => \Auth::User()->id]); 

        // Update to trip table hasLog
        \DB::table('trips')
            ->where('id', $data['trip_id'])
            ->update(['hasLog' => 1, 
                'flag' =>   ($data['missing'] == false ? 0 : 1), 
                'updatedBy' => \Auth::User()->id]); 
        
        
        // Add trip details again as new
        $i = 0;
        foreach($data['tripLocations'] as $row)
        {
                $new        =   new areacontroller;

                $new->area_id               =   $row['id'];
                $new->trip_id               =   $data['trip_id'];
                $new->dispatch_id           =   $dispatch_id;
                $new->sequence              =   $i;
                $new->tripStartDate         =   Carbon::parse($data['startingDate'])->format('Y-m-d');
                $new->detailsofGoods        =   $data['detailsofGood'];
                $new->weightandDimmension   =   $data['wieghtdimension'];
                $new->consignmentNo         =   $data['consignmentNumber'];
                $new->createdBy             =   \Auth::User()->id;

                if($data['endingDate'] != ''){
                    $new->tripEndDate           =   Carbon::parse($data['endingDate'])->format('Y-m-d');
                }

                //Save
                $new->save();

                $i++;

        }
        
    }

    // Set bill T of trip(s)
    public function setBillT(Request $request)
    {

        $this->validate(request(), [
                'data'                =>      'required',
        ]);
        foreach($request->data as $row)
        {

            \DB::table('trips')
            ->where('id', $row['trip_id'])
            ->update([
                        'billT'             =>  $row['billT'], 
                        'billTReceiving'    =>  Carbon::parse($row['billTReceiving'])->format('Y-m-d'), 
                        'updatedBy'         =>  \Auth::User()->id]
                    ); 

        }

        return ['message' => 'Record Updated Successfully', 'id' => 1];

    }

    // Replace Staff
    public function replaceStaff(Request $request)
    {

        $this->validate(request(), [
                'replacement'                =>      'required',
        ]);

        $flag = false;

        foreach($request->replacement as $row)
        {

           $flag = $this->addStaff($row['trip_id'], $row['originalStaff'], $row['replaceBy']);

        }
        

        if($flag)
        {
            return ['message' => 'Staff Replaced Successfully', 'id' => 1];

        } else {

            return 'Something went wrong';
        }

    }

    //add staff
    public function addStaff($trip_id, $originalStaff, $replaceBy)
    {

            \DB::table('trips')
            ->where('id', $trip_id)
            ->where('staff1', $originalStaff)
            ->update(['staff1' => $replaceBy, 'updatedBy' => \Auth::User()->id]); 

            \DB::table('trips')
            ->where('id', $trip_id)
            ->where('staff2', $originalStaff)
            ->update(['staff2' => $replaceBy, 'updatedBy' => \Auth::User()->id]); 

            \DB::table('trips')
            ->where('id', $trip_id)
            ->where('staff3', $originalStaff)
            ->update(['staff3' => $replaceBy, 'updatedBy' => \Auth::User()->id]); 

            return true;

    }


    //Replace Equipment
    public function replaceEquipment(Request $request, $equipment_id, $dispatch_id)
    {

        $this->validate(request(), [
                'equipment'                =>      'required',
                'dates'                     =>      'required',
        ]);


        $flag = false;

        foreach($request->replacement as $row)
        {

            $Trip = thiscontroller::find($row['trip_id']);
            $newTrip =  $Trip->replicate();

            if(isset($request->equipment['staff'][0])){
                $newTrip->staff1    =   $request->equipment['staff'][0]['id'];    
            } else {
                $newTrip->staff1    =   NULL;
            }
            if(isset($request->equipment['staff'][1])){
                $newTrip->staff2    =   $request->equipment['staff'][1]['id'];    
            } else {
                $newTrip->staff2    =   NULL;
            }
            if(isset($request->equipment['staff'][2])){
                $newTrip->staff3    =   $request->equipment['staff'][2]['id'];    
            } else {
                $newTrip->staff3    =   NULL;
            }

            $newTrip->save();

            $Trip->status           =   9;
            $Trip->updatedBy        =   \Auth::User()->id;
            $Trip->save();

            $trip_id = $row['trip_id'];
            $newTrip_id = $newTrip->id;

            $areaTrip = areacontroller::where('trip_id', $trip_id)->get();

            foreach($areaTrip as $at)
            {

                $oldAreaTrip = areacontroller::find($at->id);

                $newAreaTrip = $oldAreaTrip->replicate();
                $newAreaTrip->trip_id = $newTrip_id;
                $newAreaTrip->createdBy = \Auth::User()->id;
                $newAreaTrip->save();

                $oldAreaTrip->status  = 10;
                $oldAreaTrip->updatedBy = \Auth::User()->id;
                $oldAreaTrip->save();

            }

            $dispatchEquipment = dispatchcontroller::where('equipment_id', $request->equipment['id'])
                                    ->where('dispatch_id', $dispatch_id)
                                    ->get();
            
            if($dispatchEquipment->count() == 0)
            {

                $newEquipment = new dispatchcontroller;

                $newEquipment->dispatch_id  = $dispatch_id;
                $newEquipment->equipment_id = $request->equipment['id'];
                $newEquipment->status       = 1;

                $newEquipment->save();

            }

            // $equipment_trip = equipmentcontroller::where('trips_id', $row['trip_id'])->get();

            $equipment_trip                 =   equipmentcontroller::find($row['trip_id']);

            $newEquipmentTrip               =   new equipmentcontroller;
            $newEquipmentTrip->equipment_id =   $request->equipment['id'];
            $newEquipmentTrip->trips_id     =   $newTrip_id;
            $newEquipmentTrip->dispatch_id  =   $dispatch_id;
            $newEquipmentTrip->save();

            $equipment_trip->replacedby_id  =   $request->equipment['id'];
            $equipment_trip->reason         =   $request->reason;
            $equipment_trip->updatedBy      =   \Auth::User()->id;
            $equipment_trip->save();

            $flag = true;

        }

        if($flag)
        {
            return ['message' => 'Staff Replaced Successfully', 'id' => 1];

        } else {

            return ['message' => 'Something went wrong'];
        }

    }

    //Get Trips

    // Get all trips by equipments id
    public function getAllByEquipment(Request $request)
    {

        //return \Auth::User();

        $Trips = \DB::table('area_trip')

             ->select('area_trip.*', 'trips.billT', 'areas.areaName', 'users.name', 'users.tagColor', 'users.id', 'trips.hasLog')

             ->Join('trips', 'area_trip.trip_id', '=', 'trips.id')

             ->Join('equipment_trip', 'area_trip.trip_id', '=', 'equipment_trip.trips_id')

             ->Join('areas', 'areas.id', '=', 'area_trip.area_id')

             ->Join('users', 'area_trip.createdBy', '=', 'users.id')

             ->where('equipment_trip.equipment_id', '=', $request->equipment_id)

             ->where('equipment_trip.dispatch_id', '=', $request->dispatch_id)

             // ->where('area_trip.status', '=', 1, 'area_trip.status', '=', 9)

             ->where(function($q) {
                        $q->where('area_trip.status', 1)
                            ->orWhere('area_trip.status', 9);
                    })

             // ->orWhere('area_trip.status', '=', 9)

             ->orderBy('area_trip.tripStartDate', 'ASC');

             return $Trips->get();

    }



    // Get trip dates for expense
    public function getTripDates(Request $request)
    {

        $Trips = \DB::table('area_trip')

             ->select('area_trip.trip_id', 'area_trip.tripStartDate')

             ->DISTINCT('area_trip.tripStartDate')

             ->Join('equipment_trip', 'area_trip.trip_id', '=', 'equipment_trip.trips_id')

             ->where('equipment_trip.equipment_id', '=', $request->equipment_id)

             ->where('equipment_trip.dispatch_id', '=', $request->dispatch_id)

             ->where('area_trip.status', '=', 1)

             ->orderBy('area_trip.tripStartDate', 'ASC');

             return $Trips->get();

    }

    // Get all trips by trip id (log)
    public function getAllByTripID(Request $request)
    {

        $Trips = \DB::table('area_trip')

             ->select('area_trip.*', 'users.tagColor', 'users.name', 'areas.areaName')

             ->Join('areas', 'areas.id', '=', 'area_trip.area_id')

             ->Join('users', 'area_trip.createdBy', '=', 'users.id')

             ->where('area_trip.trip_id', '=', $request->trip_id)

             ->orderBy('area_trip.status', 'ASC');

             return $Trips->get();

    }

    // Get all trips by status
    public function getTripsByStatus(Request $request)
    {

        $Trips = \DB::table('area_trip')

             ->select('area_trip.*', 'users.tagColor', 'users.name', 'areas.areaName')

             ->Join('areas', 'areas.id', '=', 'area_trip.area_id')

             ->Join('users', 'area_trip.createdBy', '=', 'users.id')

             ->Join('equipment_trip', 'equipment_trip.trips_id', '=', 'area_trip.trip_id')

             ->where('area_trip.dispatch_id', '=', $request->dispatch_id)

             ->where('equipment_trip.equipment_id', '=', $request->equipment_id)

             ->where('area_trip.status', '=', $request->status);

             return $Trips->get();

    }

}
