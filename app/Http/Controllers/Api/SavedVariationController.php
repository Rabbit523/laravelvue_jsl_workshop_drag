<?php

namespace App\Http\Controllers\Api;

use App\saved_variation as thiscontroller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SavedVariationResource as thisresource;


class SavedVariationController extends Controller
{
    public function store(Request $request)
    {

    	// Validate data
        $this->validate(request(), [
                'data'           =>       'required'
        ]);

        $staff_id 					 =	 $request->staff_id;
        // Create new 
        foreach ($request->trip_ids as $row)
        { 

	        $this->findDelete($row['trip_id'], $staff_id);

	        foreach ($request->data as $subRow)
	        {

	      		$new            =   new thiscontroller;
	        
	        	$new->trip_id    		         =   $row['trip_id'];
	        	$new->dispatch_id                =   $row['dispatch_id'];  	
	        	$new->multiplier				 =	 $row['multiplier'];

	        	$new->staff_id					=	$staff_id;
	        	$new->variation_id				=	$subRow['vari_id'];
	        	$new->amount					=	$subRow['amount'];
	        	$new->type 						=	$subRow['variType'];
	        	$new->paidTo					=	$subRow['paidTo'];

	        	// Save variation
	        	$new->save();

	        }

    	}

        return ['message' => 'Record Added Successfully', 'id' => $new->id];

    }

   public function findDelete($trip_id, $staff_id)
   {

   		\DB::table('saved_variations')
   			->where('trip_id', $trip_id)
   			->where('staff_id', $staff_id)
   			->update(['status' => 9]); 
        

   }


   //Get Staff Specific Saved Variation
   public function getSavedVariations(Request $request)
   {

   	return new thisresource(
                    thiscontroller::whereIn('staff_id', $request->staff_id)
                                    ->whereIn('dispatch_id', $request->dispatch_id)
                                    ->where('status', 1)
                                    ->get()
        );

   }


}
