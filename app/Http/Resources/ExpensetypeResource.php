<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpensetypeResource extends JsonResource
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
            'expenseType'  =>  $this->expenseType,
            'label'     =>  $this->expenseType,
            'value'     =>  $this->id,
            'status'    =>  $this->statuses->statusName,
        ];
    }
}
