<?php

namespace App\Http\Controllers\Api;

use App\expense;
use App\supplierBill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentExpenseResource;
use Carbon\Carbon;


class ExpenseController extends Controller
{
    //
    //Save all expense by dispatch id
    public function saveAllExpense(Request $request, $dispatch_id, $equipment_id)
    {
        // Validate data
        $this->validate(request(), [
                'expense'           =>       'required'
        ]);

        
        \DB::table('expenses')
        ->where('dispatch_id', '=', $dispatch_id)
        ->where('equipment_id', '=', $equipment_id)
        ->where('expenseType_id', '!=', 1) //Where type isn't Petrol
        ->update(
            [
                'status' => 0,
                'updatedBy' =>  \Auth::User()->id
            ]
        );

        foreach($request->expense as $expense){
        	
        	$i = 0;

        	$expTypeName 	= 	$expense['expenseTypeName'];
	        $expenseTypeID 	= 	$expense['id'];

	        foreach($request->Created as $row)
	        {

	        	if(isset($expense[$expTypeName][$row['position']]))
	        	{

		         	$actualAmount	= 	$expense[$expTypeName][$row['position']];

		            //Add new expense by id
		            $new            =   new expense;
		            $new->expenseDate			=	$row['value'];
		            $new->trip_id				=	$row['trip_id'];
		            $new->equipment_id			=	$equipment_id;
		            $new->dispatch_id           =   $dispatch_id;
		            $new->expenseType_id 		=	$expenseTypeID;
		            $new->actualAmount 			=	$actualAmount;
		            $new->createdBy				=	\Auth::User()->id;

		            // Save category
		            $new->save();

	        	}
	            

	        }

    	}

        if($new->id){
        	return ['message' => 'Record Added Successfully', 'id' => $new->id];
    	}

        
    }

    //Save all fuel by dispatch id
    public function saveAllFuel(Request $request, $dispatch_id, $equipment_id)
    {
        // Validate data
        $this->validate(request(), [
                'expense'           =>       'required'
        ]);

        foreach($request->expense as $expense){
            
            $i = 0;

            $expTypeName    =   $expense['expenseTypeName'];
            $expenseTypeID  =   $expense['id'];

            foreach($request->Created as $row)
            {

                if(isset($expense[$expTypeName][$row['position']]))
                {

                    $actualAmount   =   $expense[$expTypeName][$row['position']];

                    //Add new expense by id
                    $new            =   new expense;
                    $new->expenseDate           =   $row['value'];
                    $new->trip_id               =   $row['trip_id'];
                    $new->equipment_id          =   $equipment_id;
                    $new->dispatch_id           =   $dispatch_id;
                    $new->expenseType_id        =   $expenseTypeID;
                    $new->supplier_id           =   $expense['supplier'][$row['position']]['id'];
                    $new->slipNumber            =   $expense['slip'][$row['position']];
                    $new->rate                  =   $expense['rate'][$row['position']];
                    $new->actualAmount          =   $actualAmount;
                    $new->qty                   =   $expense['qty'][$row['position']];
                    $new->createdBy             =   \Auth::User()->id;

                    // Save category
                    $new->save();

                    if(!empty($expense['oRate'][$row['position']]) && !empty($expense['oQty'][$row['position']])){

                        $new            =   new expense;
                        $new->expenseDate           =   $row['value'];
                        $new->trip_id               =   $row['trip_id'];
                        $new->equipment_id          =   $equipment_id;
                        $new->dispatch_id           =   $dispatch_id;
                        $new->expenseType_id        =   2;
                        $new->supplier_id           =   $expense['supplier'][$row['position']]['id'];
                        $new->slipNumber            =   $expense['slip'][$row['position']];
                        $new->rate                  =   $expense['oRate'][$row['position']];
                        $new->actualAmount          =   ($expense['oRate'][$row['position']] * $expense['oQty'][$row['position']]);
                        $new->qty                   =   $expense['oQty'][$row['position']];
                        $new->createdBy             =   \Auth::User()->id;
                        $new->save();

                    }

                }
                

            }

        }

        if($new->id){
            return ['message' => 'Record Added Successfully', 'id' => $new->id];
        }

        
    }

	//Save expense by expense type id
    public function saveExpense_byId(Request $request, $expensetype_id)
    {
        // Validate data
        $this->validate(request(), [
                'expense'           =>       'required'
        ]);

        $dispatch_id 	= 	$request->expense['dispatch_id'];
		$equipment_id 	=	$request->expense['equipment_id'];
		$expenseTypeID 	= 	$request->expensetype_id;

		\DB::table('expenses')
            ->where('dispatch_id', $dispatch_id)
            ->where('equipment_id', $equipment_id)
            ->where('expenseType_id', $expenseTypeID)
            ->update(
        		[
        			'status' => 0,
        			'updatedBy'	=>	\Auth::User()->id
        		]
        	);

        foreach($request->expense['dates'] as $row)
        {

        	$expTypeName 	= 	$request->expense['data']['expenseTypeName'];
        	$actualAmount	=	null;
        	if(isset($request->expense['data'][$expTypeName][$row['position']])){
        		$actualAmount	= 	$request->expense['data'][$expTypeName][$row['position']];
        	}

        	if($actualAmount != null){

	        	

	            //Add new expense by id
	            $new            =   new expense;
	            $new->expenseDate			=	$row['value'];
			    $new->trip_id				=	$row['trip_id'];
			    $new->equipment_id			=	$equipment_id;
	            $new->dispatch_id           =   $dispatch_id;
	            $new->expenseType_id 		=	$expenseTypeID;
	            $new->actualAmount 			=	$actualAmount;
	            $new->createdBy				=	\Auth::User()->id;

	            // Save category
	            $new->save();

        	}

        }

        if($new->id){
        	return ['message' => 'Record Added Successfully', 'id' => $new->id];
        }

        
    }

     //Save all expense by dispatch id
    public function saveAllFuelbySupplier(Request $request)
    {
        // Validate data
        $this->validate(request(), [
                'selectedSupplier'           =>       'required',
                'selectedBill'               =>       'required',
                'submittedData'              =>       'required',
        ]);

       
       

       $i = 0;
       foreach($request->submittedData as $row)
       {

            $Supplier_id    =   $row['selectedSupplier'];
            $Bill_id        =   $row['selectedBill'];     
            $equipment_id   =   $row['selectedEquipment'];

            $fuelData   =   $row['fuelData'];
            $date = $fuelData['date'];

            $date = Carbon::parse($date)->format('Y-m-d');

            $tripData = $this->grabTrip($equipment_id, $date);

            $new            =   new expense;

            if($tripData){

                $dispatch_id =  $tripData[0]->dispatch_id;
                $trip_id     =  $tripData[0]->trips_id;
            
            } else {
            
                $dispatch_id =  1;
                $trip_id     =  1;
            
            }

            $new->expenseDate       =   $date;
            $new->expenseType_id    =   1;
            $new->dispatch_id       =   $dispatch_id;
            $new->trip_id           =   $trip_id;
            $new->supplier_id       =   $Supplier_id;
            $new->equipment_id      =   $equipment_id;
            $new->slipNumber        =   $fuelData['slip'];
            $new->qty               =   $fuelData['qty'];
            $new->rate              =   $fuelData['rate'];
            $new->actualAmount      =   $fuelData['total'];
            $new->billNumber        =   $Bill_id;
            $new->createdBy         =   \Auth::User()->id;

             $new->save();
             $fuel_id = $new->id;

            if($fuelData['oQty'] > 0){

                $new                    =   new expense;
                $new->expenseDate       =   $date;
                $new->expenseType_id    =   2;
                $new->dispatch_id       =   $dispatch_id;
                $new->trip_id           =   $trip_id;
                $new->supplier_id       =   $Supplier_id;
                $new->equipment_id      =   $equipment_id;
                $new->slipNumber        =   $fuelData['slip'];
                $new->qty               =   $fuelData['oQty'];
                $new->rate              =   $fuelData['oRate'];
                $new->billNumber        =   $Bill_id;
                $new->parent_id         =   $fuel_id;
                $new->actualAmount      =   ($fuelData['oRate'] * $fuelData['oQty']);
                $new->createdBy         =   \Auth::User()->id;
                $new->save();

            }

            $this->doAdjustment($Bill_id, 13); //13 Status Changed to Waiting Approval

        $i++;

       }


        if($new->id){

            $latest = $this->grabLatestData($fuel_id);
            return ['message' => 'Record Added Successfully', 'id' => $new->id, 'data' => $latest];
        }

        
    }

    public function grabLatestData($id)
    {


        $expenses = expense::all();

        return EquipmentExpenseResource::collection(

                    $expenses->filter(function ($expense) use ($id){

                    return    
                              ($expense->id == $id || $expense->parent_id == $id) && 
                              $expense->status != 9 && $expense->status != 0 &&
                              ($expense->expenseType_id == 1 || $expense->expenseType_id == 2);

                    })

                    );

    }

    public function doAdjustment($id, $status)
    {

        $Bill = supplierBill::find($id);

        $Bill->adjustmentAmount      =       request('adjustmentAmount');
        $Bill->remarks               =       request('remarks');
        $Bill->status                =       $status;
        $Bill->save();

    }

    public function grabTrip($equipment_id, $date)
    {

        $Expenses = \DB::table('area_trip')
        ->select('equipment_trip.trips_id', 'equipment_trip.dispatch_id')
        ->join('equipment_trip', 'area_trip.trip_id', 'equipment_trip.trips_id')
        ->where('area_trip.tripStartDate', '=', $date)
        ->where('equipment_trip.equipment_id', '=', $equipment_id)
        ->limit(1);
        
        if($Expenses->count() > 0){
            return $Expenses->get();
        } else {
            return false;
        }

    }

    //Get all expense by dispatch id and equipment id
    public function getEquipmentExpense(Request $request)
    {

    	$equipment_id = $request->equipment_id;
   
    	return EquipmentExpenseResource::collection(expense::all()
    				->where('equipment_id', '=', $equipment_id)
					->where('dispatch_id', '=', $request->dispatch_id)
					->where('status', '=', 1)
    				);
    }

    //Get all expense by multi dispatch id and equipment id
    public function getEquipmentDisptachExpense(Request $request)
    {

        $equipment_id = $request->equipment_id;
   
        return EquipmentExpenseResource::collection(expense::all()
                    ->where('equipment_id', '=', $equipment_id)
                    ->whereIn('dispatch_id', $request->dispatch_id)
                    ->where('status', '=', 1)
                    );
    }

    //Get all fuel by dispatch id and equipment id
    public function getEquipmentFuel(Request $request)
    {

        $equipment_id = $request->equipment_id;
   
        $expenses = expense::all();

        return EquipmentExpenseResource::collection(

                    $expenses->filter(function ($expense) use ($equipment_id, $request){

                    return    $expense->equipment_id == $equipment_id &&
                              $expense->dispatch_id == $request->dispatch_id &&
                              ($expense->expenseType_id == 1 || $expense->expenseType_id == 2);

                    })

                    );

        

    }

    //Get all fuel by dispatch id and equipment id
    public function getBillFuel(Request $request)
    {

        $Bill_id = $request->bill_id;
   
        $expenses = expense::all();

        return EquipmentExpenseResource::collection(

                    $expenses->filter(function ($expense) use ($Bill_id, $request){

                    return    
                              $expense->billNumber == $Bill_id && $expense->status != 9 && $expense->status != 0 &&
                              ($expense->expenseType_id == 1 || $expense->expenseType_id == 2);

                    })

                    );

        

    }


    //Get all approved fuel by bill id and status
    public function getApprovedFuelBill(Request $request)
    {

        $Bill_id = $request->bill_id;
   
        $expenses = expense::all();

        return EquipmentExpenseResource::collection(

                    $expenses->filter(function ($expense) use ($Bill_id, $request){

                    return    
                              $expense->billNumber == $Bill_id && $expense->isApproved == 1  
                              && $expense->status != 9 && $expense->status != 0 &&
                              ($expense->expenseType_id == 1 || $expense->expenseType_id == 2);

                    })

                    );

        

    }

    //Get all approved fuel by supplier and date range
    public function getFuelByDate(Request $request)
    {

   
        $this->validate(request(), [
                'startDate'              =>      'required',
                'endDate'                =>      'required',
        ]);

        $startDate = Carbon::parse(request('startDate'))->format('Y-m-d');
        $endDate = Carbon::parse(request('endDate'))->format('Y-m-d');
        $supplier_id = $request->supplier_id;
        $equipment_id = $request->equipment_id;
        
        

            $expense = expense::where('expenseDate', '>=', $startDate)

                    ->where('expenseDate', '<=', $endDate)

                    ->where('status', '=', 1)

                    ->where('isApproved', '=', 1)

                    ->where('flag', '=', NULL);

                    if( !empty($supplier_id))
                    {
                        $expense->where('supplier_id', $supplier_id);
                    }
                    if( !empty($equipment_id))
                    {
                        $expense->where('equipment_id', $equipment_id);
                    }

                    $expense->where(function($q) {
                        $q->where('expenseType_id', 1)
                            ->orWhere('expenseType_id', 2);
                    });

                    

          return EquipmentExpenseResource::collection( $expense->get() );

        

    }

    //Get all flagged fuel
    public function getFlagged(Request $request)
    {

       return EquipmentExpenseResource::collection(expense::all()
                    ->where('flag', '=', 1)
                    ->where('status', '=', 1)
                    ->where('isApproved', '=', 0)
                    ->whereIn('expenseType_id', [1, 2])
                    );

    }

    //Get all flagged fuel
    public function getDeleted(Request $request)
    {

       return EquipmentExpenseResource::collection(expense::all()
                    ->where('status', '=', 0)
                    ->where('expenseType_id', '=', 1)
                    );

    }

    //Search expense by dates and equipment id 
    //this will only give waiting approval fuel and not flagged 
    public function searchExpenseByEquipments(Request $request)
    {

        $this->validate(request(), [
                'startDate'              =>      'required',
                'endDate'                =>      'required',
                'equipment_id'           =>      'required',
        ]);

        $startDate = Carbon::parse(request('startDate'))->format('Y-m-d');
        $endDate = Carbon::parse(request('endDate'))->format('Y-m-d');
        $equipment_id = request('equipment_id');
        
        return EquipmentExpenseResource::collection(

                    expense::where('expenseDate', '>=', $startDate)

                    ->where('expenseDate', '<=', $endDate)

                    ->where('status', '=', 1)

                    ->where('isApproved', '=', 0)

                    ->where('flag', '=', NULL)

                    ->whereIn('equipment_id', $equipment_id)

                    ->where(function($q) {
                        $q->where('expenseType_id', 1)
                            ->orWhere('expenseType_id', 2);
                    })

                    ->get()

                    );

    }

    //Get count of all expense required action
    public function getExpenseCount()
    {

        $data['Waiting'] = $this->getWaitingFuel();

        $data['Flagged'] = $this->flaggedFuel();

        $data['Deleted'] = $this->deletedFuel();

        return $data;
    }

    //Get count of fuel waiting approval
    public function getWaitingFuel()
    {

        $Expenses = \DB::table('expenses')
        ->where('status', '=', 1)
        ->where('isApproved', '=', 0)
        ->where('expenseType_id', '=', 1); //Where type isn't Petrol

        return $Expenses->count();

    }

    //Get count of flagged fuel
    public function flaggedFuel()
    {

        $Expenses = \DB::table('expenses')
        ->where('status', '=', 1)
        ->where('flag', '=', 1)
        ->where('isApproved', '=', 0)
        ->where('expenseType_id', '=', 1); //Where type isn't Petrol

        return $Expenses->count();

    }

    //Get count of deleted fuel
    public function deletedFuel()
    {

        $Expenses = \DB::table('expenses')
        ->where('status', '=', 0)
        ->where('expenseType_id', '=', 1)
        ;

        return $Expenses->count();

    }

    //Aprove expense by id
    public function approveExpense(Request $request, $expense_id, $bill_id = NULL)
    {

        \DB::table('expenses')
            ->where('id', $expense_id)
            ->orWhere('parent_id', $expense_id)
            ->update(
                [
                    'isApproved'    =>  1,
                    'approvedBy'    =>  \Auth::User()->id
                ]
            );

        //If bill id provided check if any expense remaining to be appoved.
        if($bill_id != null){

            $this->checkWaitingApproval($bill_id);

        } 

            return ['message' => 'Record Updated Successfully'];

    }

    //Save and Aprove expense by id from flag screen
    public function saveNapproveExpense(Request $request)
    {

        $remarks = $request->params['remarks'];
        if( empty($remarks) )
        {
            $remarks = 'Sorted';
        }
        \DB::table('expenses')
            ->where('id', $request->params['id'])
            // ->orWhere('parent_id', $expense_id)
            ->update(
                [
                    'qty'           =>  $request->params['editedData']['qty'],
                    'rate'          =>  $request->params['editedData']['rate'],
                    'actualAmount'  =>  $request->params['editedData']['total'],
                    'isApproved'    =>  1,
                    'updatedBy'     =>  \Auth::User()->id,
                    'approvedBy'    =>  \Auth::User()->id,
                    'reasonToFlag'  =>  $request->params['orignalReason'] . ' - ' . $remarks
                ]
            );

        \DB::table('expenses')
            ->Where('parent_id', $request->params['id'])
            ->update(
                [
                    'qty'           =>  $request->params['editedData']['oQty'],
                    'rate'          =>  $request->params['editedData']['oRate'],
                    'actualAmount'  =>  ($request->params['editedData']['oQty'] * $request->params['editedData']['oRate']),
                    'isApproved'    =>  1,
                    'updatedBy'     =>  \Auth::User()->id,
                    'approvedBy'    =>  \Auth::User()->id
                ]
            );

        //If bill id provided check if any expense remaining to be appoved.
        if($request->params['bill_id'] != null){

            $this->checkWaitingApproval($request->params['bill_id']);

        } 

            return ['message' => 'Record Updated Successfully'];

    }

    //Check if any of the expense left to be approved 
    public function checkWaitingApproval($bill_id)
    {


        $Expenses = \DB::table('expenses')
        ->where('status', '=', 1)
        ->where('isApproved', '=', 0)
        ->where('billNumber', '=', $bill_id)
        ->where('expenseType_id', '=', 1); //Where type is Petrol

        if($Expenses->count() == 0)
        {
            $Bill = supplierBill::find($bill_id);
            $Bill->status       =   14; //Change status to Outstanding
            $Bill->save();

            return ['message' => 'Record Updated Successfully', 'status' => 'billApproved'];
            die();
        }


    }

    //Delete expense by id
    public function deleteExpense(Request $request, $expense_id)
    {


        \DB::table('expenses')
            ->where('id', $expense_id)
            ->orWhere('parent_id', $expense_id)
            ->update(
                [
                    'isApproved'    =>  0,
                    'status'        =>  0,
                    'updatedBy'     =>  \Auth::User()->id
                ]
            );

        return ['message' => 'Record Updated Successfully'];

    }

    //Flag expense by id
    public function flagExpense(Request $request, $expense_id)
    {


        \DB::table('expenses')
            ->where('id', $expense_id)
            ->orWhere('parent_id', $expense_id)
            ->update(
                [
                    'flag'          =>  1,
                    'flagBy'        =>  \Auth::User()->id,
                    'isApproved'    =>  0,
                    'approvedBy'    =>  NULL,
                    'reasonToFlag'  =>  $request->reason,
                ]
            );

        return ['message' => 'Record Updated Successfully'];

    }

    //Replca equipment for expense only for taxation purposes
    public function replaceEquipment(Request $request)
    {

        foreach ($request->submitReplaceData as $row)
        {

            if($row != null)
            {

                \DB::table('expenses')
                ->where('id', $row['expenseId'])
                ->update(
                    [
                        'dummyEquipment'          =>  $row['id']
                    ]
                );

            }

        }

        return ['message' => 'Record Updated Successfully'];


    }

}
