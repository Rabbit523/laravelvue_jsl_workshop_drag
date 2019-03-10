<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class certificate_staff extends Model
{
    protected $table = 'certificate_staff';

     public function certificate()
    {

    	return $this->belongsTo(certificate::class, 'certificate_id');

    }

}
