<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Validation\CantChange;

class UsersController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

     public function store(Request $request)
    {
         // Validate data
        $this->validate(request(), [
                'name'               	=>      'required|min:3',
                'email'            		=>      'required|unique:users',
                'tagColor'              =>      'required',
                'password'              =>      'required|min:6',
                'phoneNumber'           =>      'required|min:8'
        ]);

        // Create new
        $new            =   new user;
        
        $new->name              =   request('name');
        $new->email          	=   request('email');
        $new->phoneNumber       =   request('phoneNumber');
        $new->password 			=   Hash::make(request('password'));
        $new->tagColor 			=	request('tagColor')['value'];
        $new->status            =   1;

        // Save
        $new->save();

        return ['message' => 'Record Added Successfully', 'id' => $new->id];

    }

    public function edit($id)
    {
        return new UserResource(user::find($id));
    }

    public function update(Request $request, $id)
    {
         // Validate data
        $this->validate(request(), [
                'name'                  =>      ['required', new CantChange('users', $id)],
                'email'                 =>      'required',
                'tagColor'              =>      'required',
                'phoneNumber'           =>      'required'
        ]);

        \DB::table('users')
            ->where('id', $id)
            ->update(
                [
                    // 'areaName'              =>   request('areaName'), Area Name Can't Be Changed
                    'email'             =>  request("email"),
                    'tagColor'          =>  request('tagColor')['value'],
                    'phoneNumber'       =>  request('phoneNumber')
        ]);

            if(!empty(request('password'))){
                \DB::table('users')
                    ->where('id', $id)
                    ->update(
                        [
                            // 'areaName'              =>   request('areaName'), Area Name Can't Be Changed
                            'password'           =>   Hash::make(request('password'))
                ]);                
            }

        return ['message' => 'Record Updated Successfully', 'id' => $id];
    }

    public function statusUpdate(Request $request, $id)
    {

        $this->validate(request(), [
                'statusid'               =>      'required',
                'status'                =>       'required'
        ]);

        $Status = 1;
        if(request('status') == 'Active'){
            $Status = 2;
        }

        \DB::table('users')
            ->where('id', $id)
            ->update(
                [
                    'status'            =>   $Status,
        ]);

        return ['message' => 'Client Status Updated Successfully', 'id' => $id, 'status' => true];

    }

}
