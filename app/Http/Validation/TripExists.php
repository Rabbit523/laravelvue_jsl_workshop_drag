<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;


class TripExists implements Rule

{
	protected $message = "Trip for this equipment for provided date already exists";

	protected $selectedEquipment;

	public function __construct($selectedEquipment)
    {

    	$this->selectedEquipment = $selectedEquipment;
        
    }


	public function passes($attribute, $value)
	{

		if($value != ''){ // Check if not empty

			$result = $this->checkForDuplicate($attribute, $value);

			if($result['status'] == false)

			{

				$message = "Trip already exists for these date(s): " . PHP_EOL;
				foreach($result['date'] as $row)
				{

				$message .= Carbon::parse($row)->format('d-m-Y') . ', ';
				
				}

				$this->message = $message;
				return false;

			}

			return true;

		}

	}


	public function message()
	{

		return $this->message ;

	}

	public function checkForDuplicate($attribute, $value)

	{

		$DateArray = array();

		$data['status'] = true;

		foreach($value as $row)
		{

				$tripDate = Carbon::parse($row['startingDate'])->format('Y-m-d');

				 $result = \DB::table('trips')

				 ->Join('equipment_trip', 'trips.id', '=', 'equipment_trip.trips_id')

		         ->Join('area_trip', 'area_trip.trip_id', '=', 'equipment_trip.trips_id')

				 ->where('equipment_trip.equipment_id', '=', $this->selectedEquipment)

				 ->where('area_trip.tripStartDate', '=', $tripDate)

				 ->where('area_trip.status', '=', 1)

				 ;

			 if(isset($row['trip_id']))
			 {
			 	$result->where('area_trip.trip_id', '!=', $row['trip_id']);
			 }
		

			 if($result->count() > 0){

			 	$data['status'] = false;

			 	array_push($DateArray, $tripDate);

			 } 

		}

		 $data['date'] = $DateArray;

		 return $data;

	}


}
