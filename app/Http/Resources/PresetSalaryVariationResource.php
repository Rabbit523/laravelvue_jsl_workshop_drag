<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PresetSalaryVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);
        $array['Driver']    =   $this->driverAmount;
        $array['Helper']    =   $this->helperAmount;
        $array['Operator']  =   $this->operatorAmount;
        $array['variation'] =   $this->variation->salaryvariationName;
        $array['variationType'] =   $this->variation->type;
        $array['paidTo']        =   $this->variation->paid_to;
        return $array;
    }
}
