<?php

namespace App\Http\Controllers\Api;

use App\clients;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientsResource;
use App\Http\Validation\CantChange;



class ClientsController extends Controller
{
    public function index()
    {
        return ClientsResource::collection(clients::all());
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
                'clientName'               =>      'required|min:3|unique:clients',
                'accountNumber'            =>      'required|unique:clients',
                'phone1'                   =>       'required',
                'emailAddress'             =>       'required'
        ]);

        // Create new
        $new            =   new clients;
        
        $new->clientName            =   request('clientName');
        $new->accountNumber         =   request('accountNumber');
        $new->phone1                =   request('phone1');
        $new->phone2                =   request('phone2');
        $new->contactPerson         =   request('contactPerson');
        $new->address               =   request('address');
        $new->emailAddress          =   request('emailAddress');
        $new->website               =   request('website');
        $new->creditLimit           =   request('creditLimit');
        $new->status                =   1;

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
        return new ClientsResource(clients::find($id));
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
                'clientName'               =>      ['required', new CantChange('clients', $id)],
                'phone1'                   =>       'required',
                'emailAddress'             =>       'required'
        ]);

        \DB::table('clients')
            ->where('id', $id)
            ->update(
                [
                    'accountNumber'         =>   request('accountNumber'),
                    'phone1'                =>   request('phone1'),
                    'phone2'                =>   request('phone2'),
                    'contactPerson'         =>   request('contactPerson'),
                    'address'               =>   request('address'),
                    'emailAddress'          =>   request('emailAddress'),
                    'website'               =>   request('website'),
                    'creditLimit'           =>   request('creditLimit')
        ]);

        return ['message' => 'Record Updated Successfully', 'id' => $id];
    }

    public function statusUpdate(Request $request, $id)
    {

        $this->validate(request(), [
                'statusid'               =>      'required',
                'status'                =>       'required'
        ]);

        $Status = 1;
        if(request('status') == 'Active'){
            $Status = 2;
        }

        \DB::table('clients')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Client Status Updated Successfully', 'id' => $id, 'status' => true];

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
