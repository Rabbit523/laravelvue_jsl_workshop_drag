<?php

namespace App\Http\Controllers\Api;

use App\preset_salaryvariation as thiscontroller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PresetSalaryVariationResource as thisresource;

class PresetSalaryvariationController extends Controller
{
    public function getVariations()
    {

    	return thisresource::collection(thiscontroller::all());	

    }
}
