<?php

namespace App\Http\Controllers\Api;
use App\equipments_trip_kilometers as thiscontroller;
use App\Http\Resources\EquipKilometerResource as Resources;
use FinalBytes\GoogleDistanceMatrix\GoogleDistanceMatrix;
use FinalBytes\GoogleDistanceMatrix\Response\GoogleDistanceMatrixResponse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EquipmentsTripKilometersController extends Controller
{
    
    public function getDistance(Request $request)
    {


		$distanceMatrix = new GoogleDistanceMatrix('AIzaSyBolIDsu6E8ggLwFXvoSW1-Sej2wL3ibQE');
	        $distance = $distanceMatrix
	            ->setLanguage('en-UK')
	            ->addOrigin($request->origin)
	            ->addDestination($row)
	            ->addDestination('36.721184,-4.420084')
	            ->setMode('driving')
	            ->setAvoid('')
	            ->setUnits('imperial')
	            ->sendRequest();

	    $rows = $distance->getRows();

	    $data['distance'] = array();

	    foreach($rows as $row){

	    	foreach($row->getelements() as $rw){
	    		$array = $rw->getdistance()->getvalue();
	    		array_push($data['distance'], ($array/1609));
	    	}
	    }

	    $data['total'] = (array_sum($data['distance']));

	    return $data;

		
	}

}
