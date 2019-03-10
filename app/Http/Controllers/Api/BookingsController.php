<?php
//
namespace App\Http\Controllers\Api;

use App\bookings as thiscontroller;
use App\booking_slots as bookingslots;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource as Resources;
use App\Http\Validation\CantChange;
use App\Http\Traits\CommonTrait;
use Carbon\Carbon;



class BookingsController extends Controller
{
    use CommonTrait;

    public function index()
    {
        return Resources::collection(thiscontroller::all());
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         // Validate data
        $this->validate(request(), [
                'customerName'               	=>     'required|min:3',
                'orderNumber'					=>	   'required|unique:bookings'
        ]);

        // Create new
        $new            =   new thiscontroller;
        
        $new->customerName             =   request('customerName');
        $new->orderNumber	           =   request('orderNumber');
        $new->suburb					=	request('suburb')['value'];
        $new->description 				=	request('description');

        $clearAccess = (request('clearAccess') == 'yes' ? true : false );
        $upstairs = (request('upstairs') == 'yes' ? true : false );
        $chargedForUpstairs = (request('chargeForUpstair') == true ? true : false );

        $new->clearAccess				=	$clearAccess;
        $new->deliveryLocation			=	request('deliveryLocation')['value'];
        $new->upstairs					=	$upstairs;
        $new->chargedForUpstairs		=	$chargedForUpstairs;	
        $new->bookingType				=	request('bookingType')['name'];
        $new->bookingDate				=	Carbon::parse($request->deliverySlot['date'])->format('Y-m-d');
        $new->startingTime				=	request('bookingTime');
        $new->endingTime				=	request('bookingTime');
        $new->status                =   3;

        // Save
        $new->save();

        if($new->id > 0)
        {
        	$this->upDateSlot($request->deliverySlot['id'], $request->duration['value'], $new->id);
        }

        return ['message' => 'Record Added Successfully', 'id' => $new->id];

    }

    public function upDateSlot($slotId, $duration, $bookingId)
    {

    	for ($i = 0; $i < $duration; $i++)
    	{

			$bookingSlot = bookingslots::all()->where('id', '>=', $slotId)->where('id', '<=', ($slotId+$duration-1));
			foreach($bookingSlot as $row)
			{
				if($row->bookingId == NULL)
				{
					$this->updating($row->id, $bookingId, $duration);
				} 
				// else 
				// {
					
				// }

			}

    	}
    	



    }

    public function updating($slotId, $bookingId, $duration)
    {

    	\DB::table('booking_slots')
            ->where('id', $slotId)
            ->update(
	        [
	            'bookingsId'         	=>   $bookingId,
	            'slotsTaken'			=>	 $duration,
	        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return new Resources(thiscontroller::find($id), $this->reservedEquipments_withTrips($id), $this->reservedEquipments($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

         // Validate data
        $this->validate(request(), [
                'dispatchNo'               	=>     ['required', new CantChange('dispatches', $id)],
                'selectedClient'			=>	   'required'
        ]);



        \DB::table('dispatches')
            ->where('id', $id)
            ->update(
        [
            'referenceNo'         	=>   request('referenceNo'),
            'dispatchStartingDate'  =>   Carbon::parse($request->dispatchStartingDate)->format('Y-m-d'),
            'invoiceNo'             =>   request('invoiceNo'),
            'invoiceDate'           =>   Carbon::parse($request->invoiceDate)->format('Y-m-d'),
            'totalAmount'         	=>   request('totalAmount'),
            'totalExpense'          =>   request('totalExpense'),
            'client_id'         	=>   request('selectedClient')['value'],
            'trackingIDNo'          =>   request('trackingIDNo'),
            'remarks' 		        =>   request('remarks')
        ]);

        return ['message' => 'Record Updated Successfully', 'id' => $id];
    }

    public function moveSlot(Request $request)
    {

         // Validate data
        $this->validate(request(), [
                'bookingID'                =>     'required',
                'slotID'                   =>     'required',
                'totalSlots'               =>     'required'
        ]);



        \DB::table('booking_slots')
            ->where('bookingsId', $request->bookingID)
            ->update(
        [
            'bookingsId'            =>   NULL,
            'slotsTaken'            =>   1
        ]);

        // return ['message' => 'Record Updated Successfully', 'id' => $id];
    }

    public function statusUpdate(Request $request, $id)
    {

        $this->validate(request(), [
                'statusid'               =>      'required',
                'status'                =>       'required'
        ]);

        $Status = 3;
        if(request('status') == 'Active'){
            $Status = 4;
        }

        \DB::table('clients')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Client Status Updated Successfully', 'id' => $id, 'status' => true];

    }

    /* Get Booking By Date  */
    public function getBookingsByDate()
    {

    	 $this->validate(request(), [
                'date'		               =>      'required',
        ]);

    	$postedDate = request('date');

    	$day = date('w', strtotime( date('Y-m-d', strtotime($postedDate) ) ) );
		
		$week_start = date('Y-m-d', strtotime('+'.(1-$day).' days'));
		$week_end = date('Y-m-d', strtotime('+'.(5-$day).' days'));

		$newWeekStart = date('Y-m-d', strtotime('+1 week', strtotime($week_start)));
    	
		$secondWeekStart = date('Y-m-d', strtotime('+2 week', strtotime($week_start)));
		
		$thirdWeekStart = date('Y-m-d', strtotime('+3 week', strtotime($week_start)));
		
		$fouthWeekStart = date('Y-m-d', strtotime('+4 week', strtotime($week_start)));
		
		
        $this->createSlot($week_start);



        $slots = bookingslots::
					select('*')
					// ->Join('bookings AS b', 'booking_slots.bookingsId', '=', 'b.id')
					// ->Join('areas AS c', 'b.suburb', '=', 'c.id')
					->whereDate('timeSlotStart', '>=', $week_start)
					->whereDate('timeSlotStart', '<=', $week_end)
					// ->whereNotNull('bookingsId')
					->orderBy('bookingType', 'ASC')
					->orderBy('timeSlotStart', 'ASC');

        $data['slots'] =  $slots->get();

        $bookings = thiscontroller::
						select('bookings.*', 'areas.areaName', 'cities.cityName')
						->join('areas', 'bookings.suburb', '=', 'areas.id')
						->join('cities', 'areas.city_id', '=', 'cities.id')
						->whereDate('bookingDate', '>=', $week_start);

		$data['bookings']	=	$bookings->get();			

        return $data;
    }

    public function createSlot($date)
    {

    	

    	$u = 0;
    	
    	for ($k = 0; $k < 7; $k++)
    	{

		$date = date('Y-m-d', strtotime($date. " + $u days"));

		$slots = bookingslots::
						select('id')
						->whereDate('timeSlotStart', '=', $date);
		$day = date('D', strtotime($date));				
    	
		if($slots->count() == 0 && $day != 'Sat' && $day != 'Sun'){
			
			$array = array('One Man', 'Two Man');
			$i = 1;
			$startingTime = strtotime('9:00');
			$endingTime = '4:00:00';

			

				foreach ( $array as $ar)
				{
					$startingTime = strtotime('9:00');

					for ($o = 0; $o < 14; $o++)
					{

						$endTime = date("H:i", strtotime('+30 minutes', $startingTime));

						// Create new
				        $new            =   new bookingslots;
				        
				        $new->bookingType             	=   $ar;
				        $new->bookingFor				=	$i;
				        $new->slotType 					=	$day . ($o+1);
				        
				        
				        $new->timeSlotStart				=	$date . ' ' . date("H:i", $startingTime);
				        $new->timeSlotEnd	           	=   $date . ' ' . $endTime;
				        
				        // Save
				        $new->save();

				        $i++;

				        $startingTime = strtotime($endTime);

				    }
			    

				}	

				
				
			}

			$u = 1;
    	}
    	
		

    }
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $task = clients::findOrFail($id);
        //$task->delete(); // will return true

        return ['message' => 'Client Deleted Successfully', 'id' => $id, 'status' => $task->delete()];

    }
}
