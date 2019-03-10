<?php

namespace App\Http\Controllers\Api;

use App\staff;
use App\certificate_staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Http\Validation\CantChange;
use Carbon\Carbon;


class StaffController extends Controller
{
    public function index()
    {
        return StaffResource::collection(staff::all());
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
                'staffName'           =>       'required',
                'selectedType'        =>       'required',
        ]);

        // Create new stock category 
        $new            =   new staff;
        
        $new->staffName             =   request('staffName');
        $new->fatherName            =   request('fatherName');
        if(!empty(request('selectedType'))){
            $new->type                  =   request('selectedType')['value'];
        }
        $new->employeeCode          =   request('employeeCode');
        $new->homePhone             =   request('homePhone');
        $new->cellPhone             =   request('cellPhone');
        $new->emergencyNo           =   request('emergencyNo');
        $new->address               =   request('address');
        $new->cnic                  =   request('cnic');
        $new->driversLicense        =   request('driversLicense');
        if(!empty(request('selectedSalaryType'))){
            $new->salaryType            =   request('selectedSalaryType')['value'];
        }
        $new->status                =   1;

        // Save category
        $new->save();

        $this->storeCertificates(request('selectedCertifications'), $new->id);

        return ['message' => 'Record Added Successfully', 'id' => $new->id];

    }

    public function storeCertificates($selectedCertifications, $staff_id){


        foreach($selectedCertifications as $row){

            $new        =   new certificate_staff;

            $new->certificate_id        =   $row['id'];
            $new->staff_id              =   $staff_id;

            if(isset($row['number']))
            {
                $new->certificateNumber     =   $row['number'];
            }

            if(isset($row['validFrom']))
            {
                $new->validFrom             =   Carbon::parse($row['validFrom'])->format('Y-m-d');
            }

            if(isset($row['validTo']))
            {
                $new->validTo               =   Carbon::parse($row['validTo'])->format('Y-m-d');
            }

            $new->status                    =   true;

            $new->save();

        }

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
        return new StaffResource(staff::find($id));
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
                'staffName'               =>       'required',
                'selectedType'            =>       'required',
        ]);

        $selectedType = NULL;
        if(!empty(request('selectedType'))){
            $selectedType = request('selectedType')['value'];
        }

        $selectedSalaryType = NULL;
        if(!empty(request('selectedSalaryType'))){
            $selectedSalaryType = request('selectedSalaryType')['value'];
        }

        \DB::table('staff')
            ->where('id', $id)
            ->update(
                [
                    'fatherName'            =>   request('fatherName'),
                    'cellPhone'             =>   request('cellPhone'),
                    'cnic'                  =>   request('cnic'),
                    'homePhone'             =>   request('homePhone'),
                    'address'               =>   request('address'),
                    'emergencyNo'           =>   request('emergencyNo'),
                    'driversLicense'        =>   request('driversLicense'),
                    'employeeCode'          =>   request('employeeCode'),
                    'type'                  =>   $selectedType,
                    'salaryType'            =>   $selectedSalaryType,
        ]);

            $this->storeCertificates(request('selectedCertifications'), $id);

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

        \DB::table('staff')
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
