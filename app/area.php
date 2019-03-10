<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    public function city()
    {

    	return $this->belongsTo(city::class, 'city_id');

    }

    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

}
