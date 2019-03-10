<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'id'        =>  $this->id,
            'areaName'  =>  $this->areaName,
            'coordinates'   =>  $this->coordinates,
            'cityid'    =>  $this->city,
            'city'      =>  $this->city->cityName,
            'status'    =>  $this->statuses->statusName,
            'value'     =>  $this->id,
            'label'     =>  $this->areaName . ' - ' . $this->city->cityName,
        ];
    }
}
