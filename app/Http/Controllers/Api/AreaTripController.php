<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\area_trip as thiscontroller;
use App\trip;
use App\dispatch;
use App\Http\Resources\TripResource as Resources;
use App\Http\Resources\DailyReportResource;
use Carbon\Carbon;

class AreaTripController extends Controller
{

	//Get all trips by dispatch id
    public function getAllByEquipment(Request $request)
    {

       return 
    	  Resources::collection(
    		thiscontroller::where('dispatch_id', $request->dispatch_id)
        
    		->whereHas('equipment_trip', function($query) use ($request) {
    			$query->where('equipment_trip.equipment_id', '=', $request->equipment_id)
          ->where('equipment_trip.dispatch_id', '=', $request->dispatch_id);
			   })
          // ->where('dispatch_id', $request->dispatch_id)
          ->where(function($q) {
                        $q->where('status', 1)
                            ->orWhere('status', 9);
                    })
          // ->where('status', 1)
          // ->orWhere('status', 9)
			    ->with(['equipment_trip', 'createdBy', 'updatedBy', 'area', 'trips'])
          ->orderBy('tripStartDate', 'ASC')
          ->orderBy('status', 'ASC')
          ->get());

             

    }

  //Get all trips by dispatch id
    public function getAllTripByDispatch(Request $request)
    {

       return 
        Resources::collection(
        thiscontroller::whereIn('dispatch_id', $request->dispatch_id)
        
          ->whereHas('equipment_trip', function($query) use ($request) {
            $query->where('equipment_trip.equipment_id', '=', $request->equipment_id)
            ->whereIn('equipment_trip.dispatch_id',  $request->dispatch_id);
          })

          ->join('dispatches', 'dispatches.id', 'area_trip.dispatch_id')
        
          ->where('area_trip.status', 1)
        
          ->with(['equipment_trip', 'createdBy', 'updatedBy', 'area', 'trips'])

          ->orderBy('tripStartDate', 'ASC')

          ->get());   

    }

    //getTripsByDate get trips for daily or monthly report
    public function getTripsForDailyReport(Request $request)
    {

      $startDate = Carbon::parse($request->startingDate)->format('Y-m-d');
      $endDate = Carbon::parse($request->endingDate)->format('Y-m-d');

      return DailyReportResource::collection(

        thiscontroller::where('tripStartDate', '>=', $startDate)
        
        ->where('tripStartDate', '<=', $endDate)
        
        ->where('status', 1)
        
        ->with(['equipment_trip', 'area', 'dispatch', 'trips'])

        ->orderBy('tripStartDate', 'ASC')

        ->orderBy('id', 'ASC')

        ->get()

      );



    }

    //Search trips by provided fields
    public function searchTripByFields(Request $request)
    {

      $startDate = Carbon::parse($request->startingDate)->format('Y-m-d');
      $endDate = Carbon::parse($request->endingDate)->format('Y-m-d');

      
      $Query = \DB::table('order AS a');

      $Query = thiscontroller::where('tripStartDate', '>=', $startDate);
        
        $Query->where('tripStartDate', '<=', $endDate)
        
        ->where('area_trip.status', 1)
        
        ->with(['equipment_trip', 'area', 'dispatch', 'trips'])

        ->orderBy('tripStartDate', 'ASC')

        ->orderBy('area_trip.id', 'ASC');

        return DailyReportResource::collection($Query->get());

    }

}
