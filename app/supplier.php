<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

}
