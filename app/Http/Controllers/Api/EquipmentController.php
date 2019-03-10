<?php


namespace App\Http\Controllers\Api;

use App\equipment as equipments;
use App\supplier;
use App\equipmenttype;
use App\equipment_variation;
use App\equipment_staff;
use App\variation;
use App\staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentResource;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\EquipmenttypeResource;
use App\Http\Resources\VariationResource;
use App\Http\Resources\StaffResource;
use App\Http\Validation\CantChange;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EquipmentResource::collection(equipments::all());
    }


    public function getdata()
    {

        $data['Supplier'] = SupplierResource::collection(supplier::all());

        $data['Type'] = EquipmenttypeResource::collection(equipmenttype::all());

        $data['Variations'] = VariationResource::collection(variation::all());
  
        $data['Staff'] = StaffResource::collection(staff::all()->where('status', '=', 1));

        return $data;

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
                'equipmentName'           =>       'required|unique:equipment',
                'equipmentNumber'         =>       'required',
                'selectedSupplier'        =>       'required',
                'selectedType'            =>       'required',
                'selectedStaff'           =>       'required|array|min:1'
        ]);

        // Create new stock category 
        $new            =   new equipments;
        
        $new->equipmentName             =   request('equipmentName');
        $new->equipmentNumber           =   request('equipmentNumber');
        $new->suppliers_id               =   request('selectedSupplier')['value'];
        $new->equipmentstypes_id         =   request('selectedType')['value'];
        $new->status                =   1;

        // Save category
        $new->save();

        $this->updateStaff($new->id, request('selectedStaff'));

        if(!empty(request('selectedVariations')))
        {
            $this->updateVariations($new->id, request('selectedVariations'));
        }
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
        return new EquipmentResource(equipments::find($id));
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
                'equipmentName'                 =>       [new CantChange('equipment', $id)],
                'equipmentNumber'               =>       [new CantChange('equipment', $id)],
                'selectedSupplier'              =>       'required',
                'selectedType'                  =>       'required',
                'selectedStaff'                 =>       'required|array|min:1'
        ]);

        \DB::table('equipment')
            ->where('id', $id)
            ->update(
                [
                    // 'equipmentName'              =>  request('equipmentName'), Can't Be Changed
                    // 'equipmentNumber'            =>  request('equipmentNumber'), Can't Be Changed
                    'suppliers_id'                  =>   request('selectedSupplier')['id'],
                    'equipmentstypes_id'            =>   request('selectedType')['id'],
        ]);

        if(request('isChanged'))
        {

           $this->updateVariations($id, request('selectedVariations'));

        }

        $this->updateStaff($id, request('selectedStaff'));

    
        return ['message' => 'Record Updated Successfully', 'id' => $id];
    }


    // Update Variations
    public function updateVariations($id, $Variations)
    {


        \DB::table('equipment_variation')->where('equipment_id', $id)->delete(); 

        foreach($Variations as $Vari)
        {

            $new                        =   new equipment_variation;
            
            $new->equipment_id         =   $id;
            $new->variation_id         =   $Vari['value'];

            // // Save variation
            $new->save();

        }     

    }

    // Update Variations
    public function updateStaff($id, $Staff)
    {


        \DB::table('equipment_staff')->where('equipment_id', $id)->delete(); 

        foreach($Staff as $Sf)
        {

            $new                        =   new equipment_staff;
            
            $new->equipment_id         =   $id;
            $new->staff_id             =   $Sf['value'];

            // // Save staff
            $new->save();

        }     

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

        \DB::table('equipment')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Equipment Status Updated Successfully', 'id' => $id, 'status' => true];

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
