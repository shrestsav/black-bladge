<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use Validator;
use App\PaymentCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\Card as PaymentCardResource;

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

        return PaymentCardResource::collection($cards);
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
            'card_no'    => 'required|digits:16|unique:payment_cards',
            'month_year' => 'required|max:191',
            'csv'        => 'required|digits:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        if(PaymentCard::where('user_id',Auth::id())->exists())
            $default = 0;
        else
            $default = 1;

        $card = PaymentCard::create([
            'user_id'     =>  Auth::id(),
            'name'        =>  $request->name,
            'type'        =>  $request->type,
            'card_no'     =>  $request->card_no,
            'month_year'  =>  $request->month_year,
            'csv'         =>  $request->csv,
            'default'     =>  $default,
        ]);
        
        return response()->json([
            "card"    => new PaymentCardResource($card),
            "message" => trans('response.card.saved'),
        ], 200);
    }

    public function setDefault($id)
    {
        $card = PaymentCard::findOrFail($id);

        if($card->user_id != Auth::id()){
            return response()->json([
                "message" => "Forbidden",
            ], 403);
        }

        //First set all card as not default
        $defaultCard = PaymentCard::where('user_id',Auth::id())
            ->where('default',1)
            ->update([
                'default' => 0
            ]);

        $card->update([
            'default' => 1
        ]);

        return response()->json([
            "card"    => new PaymentCardResource($card),
            "message" => "Card set as default",
        ]);
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
            "message" => "Card Removed Successfully"
        ], 200);
    }
}
