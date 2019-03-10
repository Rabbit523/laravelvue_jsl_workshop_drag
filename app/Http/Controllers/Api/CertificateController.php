<?php

namespace App\Http\Controllers\Api;

use App\certificate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CertificationsResource as Resource;
use App\Http\Validation\CantChange;

class CertificateController extends Controller
{
    public function index()
    {
        return Resource::collection(certificate::all());
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
                'certificateName'           =>       'required|unique:certificates',
        ]);

        // Create new stock category 
        $new            =   new certificate;
        
        $new->certificateName             =   request('certificateName');
        $new->expirable	                  =   request('expirable');
        $new->status 		              =   1;

        // Save category
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
        return new Resource(certificate::find($id));
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
                'certificateName'               =>       ['required', new CantChange('certificates', $id)],
        ]);

        \DB::table('certificates')
            ->where('id', $id)
            ->update(
                [
                    'expirable'             =>   request('expirable'),
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

        \DB::table('certificates')
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
        //
    }
}
