<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class preset_salaryvariation extends Model
{

    public function variation()
    {

    	return $this->belongsTo(salaryvariation::class, 'salaryvariation_id');

    }

}
