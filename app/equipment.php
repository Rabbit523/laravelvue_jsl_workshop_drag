<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class equipment extends Model
{
    protected $table = 'equipment';

    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

    public function type()
    {

    	return $this->belongsTo(equipmenttype::class, 'equipmentstypes_id');

    }

    public function variation()
    {

    	return $this->belongsToMany(variation::class)->withTimestamps();;

    }

    public function staff()
    {

        return $this->belongsToMany(staff::class)->withTimestamps();;

    }

    public function supplier()
    {

    	return $this->belongsTo(supplier::class, 'suppliers_id');

    }

}
