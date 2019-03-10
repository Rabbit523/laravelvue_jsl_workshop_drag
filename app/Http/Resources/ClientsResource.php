<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientsResource extends JsonResource
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
            'id'    =>  $this->id,
            'clientName' => $this->clientName,
            'accountNumber' =>  ($this->accountNumber == NULL ? '' : $this->accountNumber),
            'phone1'   => ($this->phone1 == NULL ? '' : $this->phone1 ),
            'phone2'   =>  ($this->phone2 == NULL ? '' : $this->phone2),
            'contactPerson' =>  ($this->contactPerson == NULL ? '' : $this->contactPerson),
            'address'   =>  ($this->address == NULL ? '' : $this->address),
            'emailAddress' => ($this->emailAddress == NULL ? '' : $this->emailAddress),
            'website'   =>  ($this->website == NULL ? '' : $this->website),
            'creditLimit'   =>  ($this->creditLimit == NULL ? '' : $this->creditLimit),
            'label'     =>  $this->clientName,
            'value'     =>  $this->id,
            'status'    =>  $this->statuses->statusName,
        ];
    }
}
