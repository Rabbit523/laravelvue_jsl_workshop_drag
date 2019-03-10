<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentExpenseResource extends JsonResource
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

        $array['expenseTypes']      = $this->type->expenseType;
        $array['createdBy']         = $this->created_By;
        $array['updatedBy']         = $this->updated_By;
        $array['approvedBy']        = $this->approved_By;
        $array['flaggedBy']         = $this->flagged_By;
        $array['supplier']          = ($this->supplier_id == NULL ? '' : $this->supplier->supplierName);
        $array['equipment']         = ($this->equipment_id == NULL ? '' : $this->equipment->equipmentNumber);
        $array['dummyEquipment']         = ($this->dummyEquipment == NULL ? NULL : $this->linkdummyEquipment->equipmentNumber);
        $array['equipmentType']     = ($this->equipment_id == NULL ? '' : $this->equipment->equipmentstypes_id);  
        $array['equipmentSupplier'] = ($this->equipment_id == NULL ? '' : $this->equipment->supplier);  
        $array['dummyEquipmentSupplier'] = ($this->dummyEquipment == NULL ? NULL : $this->linkdummyEquipment->supplier);  
        $array['eqTypes']           = ($this->equipment_id == NULL ? '' : $this->equipment->type);  
        return $array;
        
    }
}
