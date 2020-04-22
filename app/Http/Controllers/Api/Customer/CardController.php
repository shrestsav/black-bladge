<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use Validator;
use App\PaymentCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cards = PaymentCard::where('user_id',Auth::id())->get();

        return response()->json($cards);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required:max:191',
            'type'       => 'required|digits:1',
            'card_no'    => 'required|digits:16',
            'month_year' => 'required|max:191',
            'csv'        => 'required|digits:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $card = PaymentCard::create([
            'user_id'     =>  Auth::id(),
            'name'        =>  $request->name,
            'type'        =>  $request->type,
            'card_no'     =>  $request->card_no,
            'month_year'  =>  $request->month_year,
            'csv'         =>  $request->csv,
        ]);
        
        return response()->json([
            "card"    => $card,
            "message" => "Card Saved Successfully"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $check = PaymentCard::findOrFail($id);

        if($check->user_id != Auth::id()){
            return response()->json([
                "message" => "Forbidden, it's not your card"
            ], 403);
        }

        $check->delete();

        return response()->json([
            "status" => "200",
            "message" => "Card Removed Successfully"
        ], 200);
    }
}
