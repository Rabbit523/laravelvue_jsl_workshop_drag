<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatchEquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array =  parent::toArray($request);
        $array['equipments'] = $this->equipments;
        $array['assignedEquipments'] = $this->assignedEquipments;
        return $array;
    }
}
