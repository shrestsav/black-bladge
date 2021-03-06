<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use Illuminate\Http\Request;
use App\LocationSearchHistory;
use Validator;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function history()
    {
        $favorites = LocationSearchHistory::where('user_id',Auth::id())
                                          ->where('favorite',1)
                                          ->get();
                                          
        $histories = LocationSearchHistory::where('user_id',Auth::id())
                                        ->where('favorite',0)
                                        ->limit(10)
                                        ->orderBy('created_at','DESC')
                                        ->get();

        $data = collect([
            'favorites' =>  $favorites,
            'histories' =>  $histories   
        ]);

        return response()->json($data);
    }

    public function removeFavorite($fav_id)
    {
        $history = LocationSearchHistory::findOrFail($fav_id);

        if($history->user_id != Auth::id()){
            return response()->json([
                'message' => 'Forbidden, you cannot update this history'
            ],403);
        }
        
        $history->update([
            'favorite'  =>  0
        ]);

        return response()->json([
            'history' => $history,
            'message' => 'Remove from favorites'
        ]);
    }

    public function saveNearbyHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string',
            'sub_name'    => 'required|string',
            'lattitude'   => 'required|numeric',
            'longitude'   => 'required|numeric',
            'custom_name' => 'nullable|string|max:191',
            'details'     => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors'  => $validator->errors(),
            ], 422);
        }

        //Check if exists
        $exists = LocationSearchHistory::where('user_id',Auth::id())
                                       ->where('lattitude',$request->lattitude)
                                       ->where('longitude',$request->longitude);
        
        if($exists->exists()){
            $history = $exists->first();
            $history->update([
                'name'        =>  $request->name,
                'sub_name'    =>  $request->sub_name,
                'lattitude'   =>  $request->lattitude,
                'longitude'   =>  $request->longitude,
                'custom_name' =>  $request->custom_name,
                'details'     =>  $request->details,
                'favorite'    =>  1,
            ]);
        }
        else{
            $history = LocationSearchHistory::create([
                'user_id'     =>  Auth::id(),
                'name'        =>  $request->name,
                'sub_name'    =>  $request->sub_name,
                'lattitude'   =>  $request->lattitude,
                'longitude'   =>  $request->longitude,
                'custom_name' =>  $request->custom_name,
                'details'     =>  $request->details,
                'favorite'    =>  1,
            ]);
        }
        
        return response()->json([
            'history' => $history,
            'message' => 'Location History has been recorded'
        ]);
    }

    public function saveRecentHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'sub_name'  => 'required|string',
            'lattitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors'  => $validator->errors(),
            ], 422);
        }

        //Check if exists
        $exists = LocationSearchHistory::where('user_id',Auth::id())
                                       ->where('lattitude',$request->lattitude)
                                       ->where('longitude',$request->longitude);
        
        if($exists->exists()){
            $history = $exists->first();
        }
        else{
            $history = LocationSearchHistory::create([
                'user_id'   =>  Auth::id(),
                'name'      =>  $request->name,
                'sub_name'  =>  $request->sub_name,
                'lattitude' =>  $request->lattitude,
                'longitude' =>  $request->longitude,
                'favorite'  =>  0,
            ]);
        }
        
        return response()->json([
            'history' => $history,
            'message' => 'Location History has been recorded'
        ]);
    }

    public function saveFavoriteHistory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'custom_name' => 'nullable|string|max:191',
            'details'     => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $history = LocationSearchHistory::findOrFail($id);

        if($history->user_id != Auth::id()){
            return response()->json([
                'message' => 'Forbidden, you cannot update this history'
            ],403);
        }

        $history->update([
            'custom_name' =>  $request->custom_name,
            'details'     =>  $request->details,
            'favorite'    =>  1
        ]);

        return response()->json([
            'message' => 'Location History has been marked as favorite'
        ]);
    }
}
