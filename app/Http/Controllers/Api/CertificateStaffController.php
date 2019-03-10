<?php

namespace App\Http\Controllers\Api;

use App\certificate_staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CertificateStaffResource as Resource;

class CertificateStaffController extends Controller
{
    
	public function getStaffCertificates($staff_id)
	{

		return Resource::collection(certificate_staff::all()->where('staff_id', '=', $staff_id));

	}

	public function updateStaffCertificates($id){


		$cert = certificate_staff::findOrFail($id);

		if($cert->status == 1){

			$cert->status = 0;

		} else {

			$cert->status = 1;

		}

        return ['message' => 'Record Added Successfully', 'id' => $cert->save()];


	}

}
