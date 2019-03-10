<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class expense_supplier extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }
}
