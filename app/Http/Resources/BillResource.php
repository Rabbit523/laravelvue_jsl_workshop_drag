<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BillResource extends JsonResource
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
        $data['supplierName']   =   $this->supplier->supplierName;
        $data['receivingDate']          =   Carbon::parse($this->receivingDate)->format('d-m-Y');
        $data['startingDate']           =   Carbon::parse($this->startingDate)->format('d-m-Y');
        $data['endingDate']             =   Carbon::parse($this->endingDate)->format('d-m-Y');
        $data['supplier']   =   $this->supplier;
        $data['status']     =   $this->statuses->statusName;
        $data['label']  =   'Bill Number: ' . $this->id . ' (' . $data['status'] . ')' . ' - Supplier Reference: ' . $this->referenceNumber . ' - From ' . 
                            Carbon::parse($this->startingDate)->format('d-m-Y') . ' to ' . 
                            Carbon::parse($this->endingDate)->format('d-m-Y') . ' Amount: ' . $this->billAmount;
        return $data;
    }
}
