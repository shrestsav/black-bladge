<?php

namespace App\Http\Controllers\Api\Driver;

use Auth;
use Validator;
use App\User;
use App\Order;
use App\BookingLog;
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
        
        $vehicle_id = Auth::user()->vehicle_id;

        if(!$vehicle_id){
            return response()->json([
                'message'=>'Forbidden, first set your vehicle'
            ],403);
        }

        if($order->status > 0){
            return response()->json([
                'message'=>'You cannot accept this order, It has already been accepted'
            ],403);
        }
        
        $order->update([
            'driver_id'  => Auth::id(), 
            'vehicle_id' => $vehicle_id, 
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
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'remark'     => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if($order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden, you cannot cancel this booking'
            ],403);
        }

        if($order->status==1 && $order->driver_id==Auth::id() && $order->details->PAB==Auth::id()){

            $order->details->update([
                'PAB'  =>  null
            ]);

            $order->update([
                'driver_id' => null,
                'status' => 0
            ]);

            // store logs
            $log = BookingLog::create([
                'user_id'   =>  Auth::id(),
                'order_id'  =>  $order->id,
                'user_type' =>  'driver',
                'type'      =>  'assign_cancel',
                'remark'    =>  $request->remark,
            ]);
            
            // User::notifyCancelForPickup($request->order_id);
            return response()->json([
                'message'=>'Booking Pickup Cancelled'
            ]);
        }
        elseif($order->status==1 && $order->driver_id==Auth::id() && $order->details->PAB!=Auth::id()){
            return response()->json([
                'message'=>'Please contact your manager to cancel this pickup'
            ],403);
        }

        return response()->json([
            'message'=>'Something wrong with the request'
        ],400);
    }
}
