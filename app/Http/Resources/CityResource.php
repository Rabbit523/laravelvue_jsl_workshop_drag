<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'cityName'  =>  $this->cityName,
            'status'    =>  $this->statuses->statusName,
            'label'     =>  $this->cityName,
            'value'     =>  $this->id
        ];
    }
}
