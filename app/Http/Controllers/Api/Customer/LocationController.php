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
        $favorites = LocationSearchHistory::where('user_id',Auth::id())->where('favorite',1)->get();
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

    public function toggleFavorite($fav_id)
    {
        $history = LocationSearchHistory::findOrFail($fav_id);

        $oldState = $history->favorite;

        $history->update([
            'favorite'  =>  !$oldState
        ]);

        return response()->json([
            'history' => $history,
            'message' => 'Favorite State Changed'
        ]);
    }

    public function saveHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'lattitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $history = LocationSearchHistory::create([
            'user_id'   =>  Auth::id(),
            'name'      =>  $request->name,
            'lattitude' =>  $request->lattitude,
            'longitude' =>  $request->longitude
        ]);

        return response()->json([
            'message' => 'Location History has been recorded'
        ]);
    }
}
