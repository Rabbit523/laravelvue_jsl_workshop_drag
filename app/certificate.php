<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class certificate extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

    

}
