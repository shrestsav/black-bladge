<?php

namespace App\Http\Controllers\Api\Driver;

use Auth;
use App\User;
use App\AppDefault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AppDefault as AppDefaultResource;
use App\Http\Resources\Api\Driver\Driver as DriverResource;

class DriverController extends Controller
{
    public function index()
    {
        $appDefaults = AppDefault::firstOrFail();

        $data = [
            'user'      => new DriverResource(Auth::user()),
            'configs'   =>  new AppDefaultResource($appDefaults)
        ];

        return response()->json($data);
    }

    
}
