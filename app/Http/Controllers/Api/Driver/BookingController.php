<?php

namespace App\Http\Controllers\Api\Driver;

use Auth;
use App\User;
use App\Order;
use App\OrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\Order as OrderResource;

class BookingController extends Controller
{
    /**
     * List of new booking orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        $new = Order::where('status',0)
                        ->whereNull('driver_id')
                        ->with('customer')
                        ->orderBy('created_at','DESC')
                        ->simplePaginate(10);
        
        return OrderResource::collection($new);
    } 

    /**
     * List of active booking orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function active()
    {
        $active = Order::where('status','>',0)
                        ->where('driver_id', Auth::id())
                        ->with('customer')
                        ->orderBy('created_at','DESC')
                        ->simplePaginate(10);

        return OrderResource::collection($active);
    } 

    /**
     * Accept new orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accept($id)
    {
        $order = Order::findOrFail($id);

        if($order->status > 0){
            return response()->json([
                'message'=>'You cannot accept this order, It has already been accepted'
            ],403);
        }
        
        $order->update([
            'driver_id' => Auth::id(), 
            'status' => 1
        ]);
        $orderDetails = OrderDetail::updateOrCreate(
            ['order_id' => $order->id],
            [
                'PAB' => Auth::id(),
                'PAT' => Date('Y-m-d h:i:s'),
            ]
        );

        // User::notifyAcceptOrder($id);
        
        return response()->json(['message' => 'Successfully Accepted']);
    }

    /**
     * Cancel Accepted Order.
    **/
    public function cancelPickup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::findOrFail($request->order_id);

        if($order->status==1 && $order->driver_id==Auth::id() && $order->details->PAB==Auth::id()){

            $order->update([
                // 'driver_id' => null, 
                // 'pick_assigned_by' => null,
                // 'PAT' => null,
                'status' => 0
            ]);
            User::notifyCancelForPickup($request->order_id);
            return response()->json([
                'status'=>'200',
                'message'=>'Pickup Cancelled'
            ],200);
        }
        elseif($order->status==1 && $order->driver_id==Auth::id() && $order->details->PAB!=Auth::id()){
            return response()->json([
                'status'=>'403',
                'message'=>'Please contact your manager to cancel this pickup'
            ],403);
        }

        return response()->json([
                'status'=>'403',
                'message'=>'Something wrong with the request'
            ],403);
    }
}
