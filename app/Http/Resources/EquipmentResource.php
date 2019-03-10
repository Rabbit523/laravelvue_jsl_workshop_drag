<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'id'                =>  $this->id,
            'equipmentName'     =>  $this->equipmentName,
            'equipmentNumber'   =>  $this->equipmentNumber,
            'type'              =>  $this->type->equipmentType,
            'typeData'              =>  $this->type,
            'supplier'          =>  $this->supplier->supplierName,
            'supplierData'          =>  $this->supplier,
            'variation'         =>  $this->variation,
            'staff'             =>  $this->staff,
            'status'            =>  $this->statuses->statusName,
            'value'             =>  $this->id,
            'label'             =>  $this->equipmentName . ' - ' . $this->equipmentNumber . ' - ' . $this->supplier->supplierName,
        ];
    }
}
