<?php

namespace App\Http\Controllers\Api;

use App\advancepayments;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvancePaymentsResource;
use Carbon\Carbon;

class AdvancepaymentsController extends Controller
{

	public function store(Request $request)
    {
        
        // Validate data
        $this->validate(request(), [
                'voucherAmount'		        =>       'required',
                'selectedStaff'				=>		 'required',
                'selectedEquipment'			=>		 'required',
                'voucherDate'				=>		 'required',
                'voucherAmount'				=>		 'required'
        ]);

        // Create new advance payment
        $new            		  		=   new advancepayments;
        
        if(!empty($request->selectedSupplier))
        {
        	$new->expensesupplier_id 	    =   request('selectedSupplier')['value'];        	
        }
        if(!empty($request->selectedBill))
        {
        	$new->bill_id				=	request('selectedBill')['id'];
        }

        $new->dispatch_id 				=	1;
        $new->trip_id					=	1;

        $new->staff_id 					=	request('selectedStaff')['value'];
        
        $new->equipment_id 				=	request('selectedEquipment')['value'];

        $new->voucherDate	            =   Carbon::parse(request('receivingDate'))->format('Y-m-d');
        
        $new->referenceNumber           =   request('referenceNumber');

        $new->actualAmount				=	request('voucherAmount');

        $new->description               =   request('remarks');
        
        // Save advance slip
        $new->save();

         return ['message' => 'Record Added Successfully', 'id' => $new->id];
        
    }

    public function getByStaff(Request $request){


    	return AdvancePaymentsResource::collection(advancepayments::all()
    				->whereIn('dispatch_id', $request->dispatch_ids)
					->whereIn('staff_id', $request->staff_ids)
					->where('status', '=', 1)
    				);

    }
}
