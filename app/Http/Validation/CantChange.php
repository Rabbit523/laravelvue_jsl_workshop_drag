<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Rule;


class CantChange implements Rule

{
	protected $tableName;
	protected $id;

	protected $message = "This field can't be changed";

	public function __construct($tableName, $id)
    {
        $this->tableName = $tableName;
        $this->id = $id;
    }


	public function passes($attribute, $value)
	{

		if($value != ''){ // Check if not empty

			if(!$this->checkForChanges($attribute, $value))

			{

				$this->message = 'Leave me alone! (This field can\'t be changed)';
				return false;

			}

			return true;

		}

	}


	public function message()
	{

		return $this->message ;

	}

	public function checkForChanges($attribute, $value)

	{

		 $result = \DB::table($this->tableName)->where('id', $this->id);


		 if($result->count() > 0){

		 	$data = $result->get()[0];

		 	
		 	if($data->$attribute != $value){
		 	 	return false;
		 	} else {
		 		return true;
		 	}

		 }

	}


}
