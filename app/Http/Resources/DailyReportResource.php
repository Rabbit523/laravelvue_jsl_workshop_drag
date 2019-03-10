<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $data = parent::toArray($request);
        $data['accountNumber']  =   $this->dispatch->client->accountNumber;
        $data['client_id']      =   "" . $this->dispatch->client->id . "";

        return $data;
    }
}
