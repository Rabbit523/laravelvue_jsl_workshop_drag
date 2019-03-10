<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class CertificateStaffResource extends JsonResource
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
        $array['certificateName'] = $this->certificate->certificateName;
        $array['validFrom'] = Carbon::parse($this->validFrom)->format('d-m-Y');
        $array['validTo']   = Carbon::parse($this->validTo)->format('d-m-Y');
        $array['status']    =   ($this->status == 1 ? true : false );


        return $array;
    }
}
