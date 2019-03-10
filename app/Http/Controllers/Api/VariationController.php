<?php

namespace App\Http\Controllers\Api;

use App\variation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\VariationResource;
use Illuminate\Contracts\Validation\Rule;
use App\Http\Validation\CantChange;

class VariationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VariationResource::collection(variation::all());
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
                'variationName'           =>       'required|unique:variations'
        ]);

        // Create new stock category 
        $new            =   new variation;
        
        $new->variationName             =   request('variationName');
        $new->status                =   1;

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
        return new VariationResource(variation::find($id));
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
                'variationName'                =>       ['required', new CantChange('variations', $id)],
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

        \DB::table('variations')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Variation Status Updated Successfully', 'id' => $id, 'status' => true];

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
