<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFormRequest;
use App\User;
use App\logincodes;
use Auth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterFormRequest $request)
	{
	    $user = new User;
	    $user->email = $request->email;
	    $user->name = $request->name;
	    $user->password = bcrypt($request->password);
	    $user->save();
	    return response([
	        'status' => 'success',
	        'data' => $user
	       ], 200);
	 }

	public function login(Request $request)
	{
		
	    $credentials = $request->only('email', 'password');
	    if ( ! $token = \JWTAuth::attempt($credentials)) {
	            return response([
	                'status' => 'error',
	                'error' => 'invalid.credentials',
	                'msg' => 'Invalid Credentials.'
	            ], 400);
	    }

	 //    $status = true;
	 //    if(!empty($request->pinCode)){
	 //    	$status = $this->checkForPin($request->only('email', 'pinCode'));
	 //    }

	 //    if(empty($request->pinCode))
	 //    {
	 //    	$this->sendPinCode($request->email);
	 //    	return response([
  //               'status' => 'pinCode',
  //               'error' => 'pinCode sent',
  //               'msg' => 'We have sent a pin number to your mobile!'
  //           ], 400);
		// }

		// if(!empty($request->pinCode) && !$status)
		// {
		// 	return response([
  //               'status' => 'error',
  //               'error' => 'invalid.credentials',
  //               'msg' => 'invalid pin code!'
  //           ], 400);
		// }

	    return response(['status' => 'success'])->header('Authorization', $token);
	}

	public function checkForPin($credentials)
	{

		$user = logincodes::where('email', $credentials['email'])
					->where('pinCode', $credentials['pinCode'])
					->where('status', '0')
					->take(1);

		if($user->count() == 0){
			return false;
		}


		\DB::table('logincodes')
            ->where('email', $credentials['email'])
            ->update(
                [
                    'status'            =>   1,
        ]);

		return true;


	}

	public function generateCode($email)
	{

		$characters = '123456890';

		// generate a pin based on 2 * 7 digits + a random character
		$pin = mt_rand(10, 99)
		    . mt_rand(10, 99)
		    . $characters[rand(0, strlen($characters) - 1)];

		// shuffle the result
		$data['code'] = str_shuffle($pin);

		$data['phoneNumber'] = $this->savePin($email, $data['code']);

		return $data;

	}

	public function savePin($email, $code)
	{

		$user = User::where('email', '=', $email)->get();
		if($user->count() > 0){
			$phoneNumber = $user[0]->phoneNumber;
		}

		$new            		=   new logincodes;
        
        $new->email             =   $email;
        $new->pinCode           =   'JSL' . $code;
        $new->status            =    0;

        // Save category
        $new->save();

        return $phoneNumber;


	}

	public function sendPinCode($email)
	{

		
		
		$data = $this->generateCode($email);
		$Phone = '92' . ltrim($data['phoneNumber'], '0');
		$Code = $data['code'];

		$MSG = "Your verification code is: JSL$Code" . PHP_EOL . "Please enter on website to verify your number.";
		
		$Result = $this->buildurl($Phone, $MSG);

		/* $this->mailer->sendcode($Code); */

	}

	public function buildurl($phone, $msg, $debug=false){
	
		
		global $username,$password,$smsurl;
		$username   = "jobolo"; // your username
		$password   = "968574jobolo!"; // your Password
		$smsurl     = "http://sms.dotklick.com:80/api/?"; // change smsdomain.com to your provided
		        
        $url  = 'username='.$username;
        $url  .= '&password='.$password;
        $url  .= '&receiver='.urlencode($phone);
        $url  .= '&msgdata='.urlencode($msg);
        $urltouse =  $smsurl.$url;
        
        $response = $this->httpRequest($urltouse);
		
	}

	function httpRequest($url){
	
		########################################################
		# Functions used to send the SMS message
		########################################################
		
        $args;
        $pattern = "/http...([0-9a-zA-Z-.]*).([0-9]*).(.*)/";
        preg_match($pattern,$url,$args);
        $in = "";
        $fp = fsockopen("$args[1]", $args[2], $errno, $errstr, 30);
        if (!$fp)
        {
           return("$errstr ($errno)");
        }
        else
        {
            $out = "GET /$args[3] HTTP/1.1\r\n";
            $out .= "Host: $args[1]:$args[2]\r\n";
            $out .= "User-agent: PHP Web SMS client\r\n";
            $out .= "Accept: */*\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);
            while (!feof($fp))
            {
               $in.=fgets($fp, 128);
            }
        }
        fclose($fp);
        return($in);

    }

	public function user(Request $request)
	{
	    $user = User::find(Auth::user()->id);
	    return response([
	            'status' => 'success',
	            'data' => $user
	        ]);
	}

	public function refresh()
	{
	    return response([
	            'status' => 'success'
	        ]);
	}

	public function logout()
	{
	    JWTAuth::invalidate();
	    return response([
	            'status' => 'success',
	            'msg' => 'Logged out Successfully.'
	        ], 200);
	}

	 
}
