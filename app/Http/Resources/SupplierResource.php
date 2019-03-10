<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'id'            =>  $this->id,
            'supplierName'     =>  $this->supplierName,
            'status'        =>  $this->statuses->statusName,
            'value'         =>  $this->id,
            'label'         =>  $this->supplierName,
            'fullName'      =>  $this->fullName,
        ];
    }
}
