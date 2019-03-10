<?php


namespace App\Http\Controllers\Api;

use App\supplierBill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource as Resource;
use Carbon\Carbon;


class SupplierBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Resource::collection(supplierBill::all());
    }


    //Get Bills by status
    public function byStatus(Request $request)
    {
        return Resource::collection(supplierBill::all()->where('status', $request->status));
    }

     public function byMultiStatus(Request $request)
    {
        return Resource::collection(supplierBill::all()->whereIn('status', $request->status));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // Validate data
        $this->validate(request(), [
                'billAmount'		        =>       'required',
                'selectedSupplier'			=>		 'required',
                'startingDate'				=>		 'required',
                'endingDate'				=>		 'required'
        ]);

        // Create new stock category 
        $new            		  		  =   new supplierBill;
        
        $new->referenceNumber           =   request('referenceNumber');
        $new->billAmount				=	request('billAmount');
        $new->expense_supplier_id 	    =   request('selectedSupplier')['value'];
        $new->startingDate				=	Carbon::parse(request('startingDate'))->format('Y-m-d');
        $new->endingDate				=	Carbon::parse(request('endingDate'))->format('Y-m-d');
        $new->remarks                   =   request('remarks');
        $new->receivingDate             =   Carbon::parse(request('receivingDate'))->format('Y-m-d');
        $new->adjustmentAmount          =   request('adjustmentAmount');
        // $new->status               		=   1;

        // Save category
        $new->save();

         return ['message' => 'Record Added Successfully', 'id' => $new->id];
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function show(city $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return new Resource(supplierBill::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
    }

    public function statusUpdate(Request $request)
    {

        $this->validate(request(), [
                'bill_id'               =>      'required',
                'status'                =>       'required'
        ]);

        // $Status = 1;
        // if(request('status') == 'Active'){
        //     $Status = 2;
        // }

        \DB::table('supplier_bills')
            ->where('id', $request->bill_id)
            ->update(
                [
                    'status'            =>   $request->status,
                ]);

        return ['message' => 'Client Status Updated Successfully', 'id' => $request->bill_id, 'status' => true];   

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(city $city)
    {
        //
    }
}
