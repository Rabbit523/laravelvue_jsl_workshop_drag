<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class trip extends Model
{

    public function dispatch()
    {

    	return $this->belongsTo(dispatch::class, 'dispatch_id');

    }

    public function staff_one(){

    	return $this->belongsTo(staff::class, 'staff1');

    }

    public function staff_two(){

        return $this->belongsTo(staff::class, 'staff2');

    }

    public function staff_three(){

        return $this->belongsTo(staff::class, 'staff3');

    }

    public function tripDetails(){

    	return $this->hasMany(area_trip::class, 'id');

    }

    public function trip_Type(){

        return $this->belongsTo(tripType::class, 'tripType');

    }
    
}
