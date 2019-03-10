<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class staff extends Model
{
    public function statuses()
    {

    	return $this->belongsTo(status::class, 'status');

    }

    public function certificate_staff()
    {

    	return $this->hasMany(certificate_staff::class);

    }

    public function savedVariations()
    {

    	return $this->hasMany(saved_variation::class);

    }

}
