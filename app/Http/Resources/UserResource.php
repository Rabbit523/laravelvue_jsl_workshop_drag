<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        $array['name'] = $this->name;
        $array['email'] = $this->email;
        $array['status']    = $this->statuses->statusName;

        return $array;
        // return [
        //     'name' => $this->name,
        //     'email' => $this->email,
        // ];
    }
}
