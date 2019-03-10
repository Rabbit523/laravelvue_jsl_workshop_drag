<?php


namespace App\Http\Controllers\Api;

use App\expense_supplier as supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseSupplierResource AS SupplierResource;
use App\Http\Validation\CantChange;

class ExpenseSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SupplierResource::collection(supplier::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'supplierName'           =>       'required|unique:expense_suppliers'
        ]);

        // Create new stock category 
        $new            =   new supplier;
        
        $new->supplierName             	=   request('supplierName');
        $new->accountNumber         	=   request('accountNumber');
        $new->phone1                	=   request('phone1');
        $new->phone2                	=   request('phone2');
        $new->contactPerson         	=   request('contactPerson');
        $new->address               	=   request('address');
        $new->emailAddress          	=   request('emailAddress');
        $new->website               	=   request('website');
        $new->creditLimit           	=   request('creditLimit');
        $new->status                	=   1;

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
        return new SupplierResource(supplier::find($id));
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
         // Validate data
        $this->validate(request(), [
                'supplierName'                =>       ['required', new CantChange('expense_suppliers', $id)],
        ]);

        \DB::table('expense_suppliers')
            ->where('id', $id)
            ->update(
                [
                    'accountNumber'                 =>   request('accountNumber'),
                    'phone1'                        =>   request('phone1'),
                    'phone2'                        =>   request('phone2'),
                    'contactPerson'                 =>   request('contactPerson'),
                    'address'                       =>   request('address'),
                    'emailAddress'                  =>   request('emailAddress'),
                    'website'                       =>   request('website'),
                    'creditLimit'                   =>   request('creditLimit')
        ]);

        return ['message' => 'Record Updated Successfully', 'id' => $id];

    }

    public function statusUpdate(Request $request, $id)
    {

        $new->supplierName              =   request('supplierName');
        $new->accountNumber             =   request('accountNumber');
        $new->phone1                    =   request('phone1');
        $new->phone2                    =   request('phone2');
        $new->contactPerson             =   request('contactPerson');
        $new->address                   =   request('address');
        $new->emailAddress              =   request('emailAddress');
        $new->website                   =   request('website');
        $new->creditLimit               =   request('creditLimit');

        $this->validate(request(), [
                'statusid'               =>       'required',
                'status'                 =>       'required',
        ]);

        $Status = 1;
        if(request('status') == 'Active'){
            $Status = 2;
        }

        \DB::table('expense_suppliers')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Supplier Status Updated Successfully', 'id' => $id, 'status' => true];

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
