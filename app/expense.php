<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class expense extends Model
{
    public function type()
    {

    	return $this->belongsTo(expensetype::class, 'expenseType_id');

    }

    public function created_By()
    {

    	return $this->belongsTo(User::class, 'createdBy');

    }

	public function updated_By()
    {

    	return $this->belongsTo(User::class, 'updatedBy');

    }  

    public function approved_By()
    {

        return $this->belongsTo(User::class, 'approvedBy');

    }  

    public function flagged_By()
    {

        return $this->belongsTo(User::class, 'flagBy');

    }

    public function supplier()
    {

        return $this->belongsTo(expense_supplier::class, 'supplier_id');

    } 

    public function equipment()
    {

        return $this->belongsTo(equipment::class, 'equipment_id');

    }    

    public function linkdummyEquipment()
    {

        return $this->belongsTo(equipment::class, 'dummyEquipment');

    }   

     

}
