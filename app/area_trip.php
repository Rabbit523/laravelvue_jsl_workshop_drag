<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class area_trip extends Model
{
    protected $table = 'area_trip';

    public function createdBy()
    {

    	return $this->belongsTo(User::class, 'createdBy');

    }

    public function updatedBy()
    {

    	return $this->belongsTo(User::class, 'updatedBy');

    }

    public function equipment_trip()
    {

    	return $this->belongsTo(equipment_trip::class, 'trip_id', 'trips_id');

    }

    public function area()
    {

    	return $this->belongsTo(area::class);

    }

    public function trips()
    {

    	return $this->belongsTo(trip::class, 'trip_id');

    }

    public function dispatch()
    {

        return $this->belongsTo(dispatch::class, 'dispatch_id');

    }
    
}
