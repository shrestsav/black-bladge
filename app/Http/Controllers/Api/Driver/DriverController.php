<?php

namespace App\Http\Controllers\Api\Driver;

use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\Driver as DriverResource;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(new DriverResource(Auth::user()));
    }

    public function changeMainArea(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'area_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $updateMainArea = UserDetail::where('user_id', '=', Auth::id())->firstOrFail();
        
        $updateMainArea->update([
			'area_id' => $request->area_id
		]);
    	
    	return response()->json([
                "status" => "200",
                "message" => "Main Area Updated"
            ], 200);
    }
}
