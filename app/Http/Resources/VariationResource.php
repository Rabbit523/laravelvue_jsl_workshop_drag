<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationResource extends JsonResource
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
            'variationName'     =>  $this->variationName,
            'status'            =>  $this->statuses->statusName,
            'label'             =>  $this->variationName,
            'value'             =>  $this->id
        ];
    }
}
