<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificationsResource extends JsonResource
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
        $array['status']    =    $this->statuses->statusName;
        $array['label']     =    $this->certificateName;
        $array['value']     =    $this->id;

        return $array;

    }
}
