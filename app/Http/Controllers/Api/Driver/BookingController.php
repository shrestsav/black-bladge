<?php

namespace App\Http\Controllers\Api\Driver;

use Auth;
use App\User;
use App\Order;
use Validator;
use App\AppDefault;
use App\BookingLog;
use App\OrderDetail;
use App\DropLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Driver\Order as OrderResource;

class BookingController extends Controller
{
    /**
     * List of new booking orders.
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
     */
    public function active()
    {
        $active = Order::where('status','>',0)
                        ->where('status','<',6)
                        ->where('driver_id', Auth::id())
                        ->with('customer')
                        ->orderBy('created_at','DESC')
                        ->simplePaginate(10);

        return OrderResource::collection($active);
    } 

    /**
     * List of completed booking orders.
     */
    public function completed()
    {
        $active = Order::where('status','>',5)
                        ->where('driver_id', Auth::id())
                        ->with('customer')
                        ->orderBy('created_at','DESC')
                        ->simplePaginate(10);

        return OrderResource::collection($active);
    } 

    /**
     * Accept new orders.
     *
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
     * Start Trip to pickup customer.
     */
    public function startTripToPickLocation($id)
    {
        $order = Order::findOrFail($id);
        
        $exists = Order::where('driver_id',Auth::id())->whereIn('status',config('settings.driver_active_booking_statuses'))->exists();

        if($exists){
            return response()->json([
                'message'=>'You already have active booking, complete it first'
            ],403);
        }

        // check if driver already have active started trips
        if($order->status != 1 || $order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden, status or driver id problem'
            ],403);
        }
        
        $order->update([
            'status' => 2
        ]);
        
        $orderDetails = OrderDetail::updateOrCreate(
            ['order_id' => $order->id],
            [
                'STPL' => Date('Y-m-d h:i:s'),
            ]
        );

        // User::notifyAcceptOrder($id);
        
        return response()->json(['message' => 'Trip Started, Please proceed to customer Location']);
    }

    /**
     * Arrived at pick location
     */
    public function arrivedAtPickLocation($id)
    {
        $order = Order::findOrFail($id);

        if($order->status != 2 || $order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden, status or driver id problem'
            ],403);
        }
        
        $order->update([
            'status' => 3
        ]);
        
        $orderDetails = OrderDetail::updateOrCreate(
            ['order_id' => $order->id],
            [
                'AAPL' => Date('Y-m-d h:i:s'),
            ]
        );

        // User::notifyAcceptOrder($id);
        
        return response()->json(['message' => 'Arrived at pick location']);
    }

    /**
     * Start trip for destination
     */
    public function startTripForDestination($id)
    {
        $order = Order::findOrFail($id);

        if($order->status != 3 || $order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden, status or driver id problem'
            ],403);
        }
        
        $order->update([
            'status' => 4
        ]);
        
        // $orderDetails = OrderDetail::updateOrCreate(
        //     ['order_id' => $order->id],
        //     [
        //         'AAPL' => Date('Y-m-d h:i:s'),
        //     ]
        // );

        // User::notifyAcceptOrder($id);
        
        return response()->json(['message' => 'Started trip for drop location']);
    }

    /**
     * Arrived at Drop Location
     */
    public function arrivedAtDropLocation(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if($order->status != 4 || $order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden, status or driver id problem'
            ],403);
        }
        
        $validator = Validator::make($request->all(), [
            'drop_location_name'     => 'required|string|max:100',
            'drop_location_sub_name' => 'required|string|max:100',
            'drop_location_lat'      => 'required|numeric',
            'drop_location_long'     => 'required|numeric',
            'drop_location_info'     => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $dropLocation = DropLocation::create([
            'order_id'      => $order->id,
            'type'          => 2,
            'added_by'      => Auth::id(),
            'drop_location' => [
                'name'      => $request->drop_location_name,
                'sub_name'  => $request->drop_location_sub_name,
                'latitude'  => $request->drop_location_lat,
                'longitude' => $request->drop_location_long,
                'info'      => isset($request->drop_location_info) ? $request->drop_location_info : null,
            ]
        ]);

        $order->update([
            'status' => 5
        ]);

        $orderDetails = OrderDetail::updateOrCreate(
            ['order_id' => $order->id],
            [
                'DC' => Date('Y-m-d h:i:s'),
            ]
        );

        // User::notifyAcceptOrder($id);
        
        return response()->json(['message' => 'Reached Drop Location']);
    }

     /**
     * Payment Done and closed
     */
    public function paymentDone($id)
    {
        $order = Order::findOrFail($id);

        if($order->status != 5 || $order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden, status or driver id problem'
            ],403);
        }
        
        $order->update([
            'status' => 6
        ]);
        
        $orderDetails = OrderDetail::updateOrCreate(
            ['order_id' => $order->id],
            [
                'PT' => Date('Y-m-d h:i:s'),
            ]
        );

        // User::notifyAcceptOrder($id);
        
        return response()->json(['message' => 'Payment Done and Booking Completed']);
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

    /**
     * Change Drop Location.
    **/
    public function addDropLocation(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $appDefaults = AppDefault::firstOrFail();
        
        if($order->driver_id != Auth::id()){
            return response()->json([
                'message'=>'Forbidden'
            ],403);
        }

        $validator = Validator::make($request->all(), [
            'order_type'             => 'required|numeric',
            'type'                   => 'required|numeric',
            'drop_location_name'     => 'required|string|max:100',
            'drop_location_sub_name' => 'required|string|max:100',
            'drop_location_lat'      => 'required|numeric',
            'drop_location_long'     => 'required|numeric',
            'drop_location_info'     => 'nullable|string|max:500',
            'distance'               => 'required_if:order_type,==,1|numeric'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $distance = null;
        $price =  null;
        if($order->type==1)
        {
            $distance = $request->distance;
            $price =  $request->distance*$appDefaults->cost_per_km;
        }

        if($request->type==1){
            $dropLocation = DropLocation::create([
                'order_id'      => $order->id,
                'type'          => $request->type,
                'added_by'      => Auth::id(),
                'drop_location' => [
                    'name'      => $request->drop_location_name,
                    'sub_name'  => $request->drop_location_sub_name,
                    'latitude'  => $request->drop_location_lat,
                    'longitude' => $request->drop_location_long,
                    'info'      => isset($request->drop_location_info) ? $request->drop_location_info : null,
                ],
                'distance'      => $distance,
                'price'         => $price,
            ]);
        }
        elseif($request->type==2){

            // There will be only one final destination of type 2
            $dropLocation = DropLocation::updateOrCreate(
                [
                    'order_id' => $order->id, 
                    'type'     => 2],
                [
                    'added_by' => Auth::id(), 
                    'drop_location' => [
                        'name'      => $request->drop_location_name,
                        'sub_name'  => $request->drop_location_sub_name,
                        'latitude'  => $request->drop_location_lat,
                        'longitude' => $request->drop_location_long,
                        'info'      => isset($request->drop_location_info) ? $request->drop_location_info : null,
                    ],
                    'distance'      => $distance,
                    'price'         => $price,
                ]
            );
        }
        else{
            return response()->json([
                'message' => 'Forbidden, Drop location type error'
            ], 403);
        }

        if($order->type==1)
        {
            $order->updatePriceAndDistanceForInstant();
        }
        
        return response()->json([
            'message' => 'Drop location has been added',
            'order'   => new OrderResource($order),
        ]);
    }
}
