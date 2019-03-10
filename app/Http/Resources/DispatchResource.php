<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class DispatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
     public function __construct($resource, $reservedEquipments_withTrips = NULL, $reservedEquipments = NULL)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        
        $this->reservedEquipments_withTrips = $reservedEquipments_withTrips;
        $this->reservedEquipments = $reservedEquipments;
    }


    public function toArray($request)
    {
        $array = parent::toArray($request);
        $array['referenceNo']  =  ($this->referenceNo == NULL ? '' : $this->referenceNo);
        $array['clientName']   =  $this->client->clientName;
        $array['dispatchStartingDate'] = Carbon::parse($this->dispatchStartingDate)->format('d-m-Y');
        $array['invoiceDate'] = Carbon::parse($this->invoiceDate)->format('d-m-Y');
        
        $array['value'] =   $this->id;
        $array['status'] =   $this->statuses->statusName;
        $array['client'] =   $this->client;
        //$array['equipments'] = $this->equipments;
        $array['reservedEquipments'] =   $this->reservedEquipments;
        $array['reservedEquipments_withTrips'] = $this->reservedEquipments_withTrips;
        return $array;
    }
}
