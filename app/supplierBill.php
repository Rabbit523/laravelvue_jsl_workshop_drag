<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class supplierBill extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

    public function supplier()
    {

    	return $this->belongsTo(expense_supplier::class, 'expense_supplier_id');

    }

}
