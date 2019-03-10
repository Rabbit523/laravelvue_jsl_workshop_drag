<?php
//
namespace App\Http\Controllers\Api;

use App\dispatch as thiscontroller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DispatchResource as Resources;
use App\Http\Validation\CantChange;
use App\Http\Traits\CommonTrait;
use Carbon\Carbon;



class DispatchController extends Controller
{
    use CommonTrait;

    public function index()
    {
        return Resources::collection(thiscontroller::all());
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
                'dispatchNo'               	=>     'required|min:3|unique:dispatches',
                'selectedClient'			=>	   'required'
        ]);

        // Create new
        $new            =   new thiscontroller;
        
        $new->dispatchNo            =   request('dispatchNo');
        $new->referenceNo           =   request('referenceNo');
        $new->dispatchStartingDate  =   Carbon::parse($request->dispatchStartingDate)->format('Y-m-d');
        $new->invoiceNo             =   request('invoiceNo');
        $new->totalAmount           =   request('totalAmount');
        $new->totalExpense          =   request('totalExpense');
        $new->client_id             =   request('selectedClient')['value'];
        $new->trackingIDNo          =   request('trackingIDNo');
        $new->remarks 		        =   request('remarks');
        $new->status                =   3;

        if(!empty($request->invoiceDate))
        {
            $new->invoiceDate           =   Carbon::parse($request->invoiceDate)->format('Y-m-d');
        }

        // Save
        $new->save();

        return ['message' => 'Record Added Successfully', 'id' => $new->id];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return new Resources(thiscontroller::find($id), $this->reservedEquipments_withTrips($id), $this->reservedEquipments($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

         // Validate data
        $this->validate(request(), [
                'dispatchNo'               	=>     ['required', new CantChange('dispatches', $id)],
                'selectedClient'			=>	   'required'
        ]);



        \DB::table('dispatches')
            ->where('id', $id)
            ->update(
        [
            'referenceNo'         	=>   request('referenceNo'),
            'dispatchStartingDate'  =>   Carbon::parse($request->dispatchStartingDate)->format('Y-m-d'),
            'invoiceNo'             =>   request('invoiceNo'),
            'invoiceDate'           =>   Carbon::parse($request->invoiceDate)->format('Y-m-d'),
            'totalAmount'         	=>   request('totalAmount'),
            'totalExpense'          =>   request('totalExpense'),
            'client_id'         	=>   request('selectedClient')['value'],
            'trackingIDNo'          =>   request('trackingIDNo'),
            'remarks' 		        =>   request('remarks')
        ]);

        return ['message' => 'Record Updated Successfully', 'id' => $id];
    }

    public function statusUpdate(Request $request, $id)
    {

        $this->validate(request(), [
                'statusid'               =>      'required',
                'status'                =>       'required'
        ]);

        $Status = 3;
        if(request('status') == 'Active'){
            $Status = 4;
        }

        \DB::table('clients')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Client Status Updated Successfully', 'id' => $id, 'status' => true];

    }

    /* Search Dispatch  */

    public function searchDispatch()
    {

        $this->validate(request(), [
                'startDate'               =>      'required',
                'endDate'                   =>      'required',
                'equipment_id'          =>  'required',
        ]);

        $startDate = Carbon::parse(request('startDate'))->format('Y-m-d');
        $endDate = Carbon::parse(request('endDate'))->format('Y-m-d');
        $equipment_id = request('equipment_id');
        
        // return Resources::collection(thiscontroller::all()
        //     ->where('dispatchStartingDate', '>=', $startDate)
        //     ->where('dispatchStartingDate', '<=', $endDate)
        // );

        $Equipments = \DB::table('dispatches')

        ->select('dispatches.id', 'dispatches.referenceNo', 'dispatches.status', 'dispatches.dispatchNo', 'equipment.equipmentNumber', 'dispatch_equipment.equipment_id', 'clients.clientName', 'statuses.statusName')

        ->Join('dispatch_equipment', 'dispatch_equipment.dispatch_id', '=', 'dispatches.id')

        ->Join('equipment', 'equipment.id', '=', 'dispatch_equipment.equipment_id')

        ->Join('clients', 'clients.id', '=', 'dispatches.client_id')

        ->Join('statuses', 'dispatches.status', '=', 'statuses.id')

        ->where('dispatchStartingDate', '>=', $startDate)

        ->where('dispatchStartingDate', '<=', $endDate)

        ->where('equipment.id', '=', $equipment_id)

        ->get();

        return $Equipments;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $task = clients::findOrFail($id);
        //$task->delete(); // will return true

        return ['message' => 'Client Deleted Successfully', 'id' => $id, 'status' => $task->delete()];

    }
}
