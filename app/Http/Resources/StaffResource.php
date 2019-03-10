<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    =>  $this->id,
            'staffName' => $this->staffName,
            'homePhone' =>  ($this->homePhone == NULL ? '' : $this->homePhone),
            'cellPhone'   => ($this->cellPhone == NULL ? '' : $this->cellPhone ),
            'emergencyNo'   =>  ($this->emergencyNo == NULL ? '' : $this->emergencyNo),
            'cnic' =>  ($this->cnic == NULL ? '' : $this->cnic),
            'driversLicense'   =>  ($this->driversLicense == NULL ? '' : $this->driversLicense),
            'address' => ($this->address == NULL ? '' : $this->address),
            'label'     =>  $this->staffName . ' - ' . $this->type . ' - ' . $this->fatherName,
            'value'     =>  $this->id,
            'type'      =>  $this->type,
            'salaryType'    =>  $this->salaryType,
            'employeeCode'  =>  ($this->employeeCode == NULL ? '' : $this->employeeCode),
            'certificates'  =>  $this->certificate_staff,
            'replaceDates' => array(),
            'fatherName'    =>  ($this->fatherName == NULL ? '' : $this->fatherName),
            'status'    =>  $this->statuses->statusName,


        ];
    }
}
