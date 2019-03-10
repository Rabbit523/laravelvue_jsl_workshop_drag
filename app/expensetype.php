<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class expensetype extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }
    
}
