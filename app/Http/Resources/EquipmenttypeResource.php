<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmenttypeResource extends JsonResource
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
            'equipmentType'     =>  $this->equipmentType,
            'status'            =>  $this->statuses->statusName,
            'value'             =>  $this->id,
            'label'             =>  $this->equipmentType,
        ];
    }
}
