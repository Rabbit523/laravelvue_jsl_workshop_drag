<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class equipment_trip extends Model
{
    protected $table = 'equipment_trip';
    protected $primaryKey = 'trips_id';

    public function equipments()
    {

    	return $this->belongsTo(equipment::class, 'dispatch_id');

    }
    
}
