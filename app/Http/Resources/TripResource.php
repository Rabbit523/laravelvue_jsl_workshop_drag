<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
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
        $array['staffs'] =  [
                                $this->trips->staff_one, 
                                $this->trips->staff_two, 
                                $this->trips->staff_three
                            ];
        $array['tripType']['value'] = $this->trips->trip_Type->id;
        $array['tripType']['label'] = $this->trips->trip_Type->name;
        return $array;
    }
}
