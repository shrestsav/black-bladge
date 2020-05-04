<?php

namespace App\Http\Controllers\Api\Driver;

use Auth;
use App\User;
use App\AppDefault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\Driver as DriverResource;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(new DriverResource(Auth::user()));
    }

    
}
