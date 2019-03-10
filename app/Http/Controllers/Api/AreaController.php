<?php


namespace App\Http\Controllers\Api;

use App\area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Http\Validation\CantChange;


class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AreaResource::collection(area::all());
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
                'areaName'           =>       'required|unique:areas',
                'selectedCity'       =>       'required'
        ]);

        // Create new stock category 
        $new            =   new area;
        
        $new->areaName             =   request('areaName');
        // $new->coordinates          =   request('coordinates');
        $new->city_id              =   request('selectedCity')['value'];
        $new->status               =    1;

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
        return new AreaResource(area::find($id));
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
                'areaName'           =>       ['required', new CantChange('areas', $id)],
                'selectedCity'       =>       'required'
        ]);

        \DB::table('areas')
            ->where('id', $id)
            ->update(
                [
                    // 'areaName'              =>   request('areaName'), Area Name Can't Be Changed
                    'city_id'               =>   request('selectedCity')['id'],
                    // 'coordinates'          =>   request('coordinates')
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

        \DB::table('cities')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'City Status Updated Successfully', 'id' => $id, 'status' => true];

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
