<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dispatch_equipment extends Model
{
    protected $table 		= 'dispatch_equipment';
	protected $primaryKey 	= 'dispatch_id';

    public function equipments()
    {

    	return $this->belongsTo(equipment::class, 'dispatch_id');

    }
    
}
