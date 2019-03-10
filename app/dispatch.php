<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispatch extends Model
{
    public function client()
    {

    	return $this->belongsTo(clients::class, 'client_id');

    }

    public function equipments()
    {

    	return $this->belongsToMany(equipment::class)->withTimestamps();

    }

    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

    public function dispatchEquipment()
    {

        return $this->hasMany(dispatch_equipment::class, 'dispatch_id');

    }

    public function equipmentTrips()
    {

        return $this->hasMany(equipment_trips::class, 'dispatch_id');

    }

    public function trips()
    {

        return $this->hasMany(trip::class, 'dispatch_id');

    }

    public function tripDetails()
    {

        return $this->hasMany(area_trip::class, 'dispatch_id');

    }

}
