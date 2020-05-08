<?php

namespace App\Http\Controllers\Api\Driver;

use Auth;
use App\User;
use App\Vehicle;
use App\AppDefault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\Driver as DriverResource;
use App\Http\Resources\Api\AppDefault as AppDefaultResource;
use App\Http\Resources\Api\Driver\Vehicle as VehicleResource;
use App\Http\Resources\Api\Driver\Order as OrderResource;

class DriverController extends Controller
{
    public function index()
    {
        $appDefaults = AppDefault::firstOrFail();

        $vehicles = Vehicle::all();

        $data = [
            'user'            =>  new DriverResource(Auth::user()),
            'configs'         =>  new AppDefaultResource($appDefaults),
            'vehicles'        =>  VehicleResource::collection($vehicles),
            'active_booking'  =>  new OrderResource(Auth::user()->activeDriverBooking()),
        ];

        return response()->json($data);
    }

    public function setVehicle($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        // check if already in use
        $exists = User::where('vehicle_id',$vehicle_id)->exists();

        if($exists){
            return response()->json([
                'message' => 'Forbidden, vehicle is already in use'
            ], 403);
        }

        Auth::user()->update([
            'vehicle_id' => $vehicle_id
        ]);

        return response()->json([
            'message' => 'Vehicle set'
        ]);
    }

    
}
