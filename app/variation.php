<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class variation extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }
}
