<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\tripType as thiscontroller;
use App\Http\Resources\TripTypeResource;
use Carbon\Carbon;

class TripTypeController extends Controller
{
     public function index()
    {
        return TripTypeResource::collection(thiscontroller::all());
    }
}
