<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use App\User;
use App\Order;
use App\Coupon;
use App\AppDefault;
use App\DropLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\Customer\Order as OrderResource;

class BookingController extends Controller
{
    public function list()
    {
        $bookings = Order::where('customer_id',Auth::id())->withTrashed()->paginate(10);

        return OrderResource::collection($bookings);
    }

    /**
     * Details of booking
     */
    public function details($id)
    {
        $booking = Order::findOrFail($id);

        if($booking->customer_id != Auth::id()){
            return response()->json([
                "message" => "Forbidden, you donot have any authorization to see details of this booking"
            ], 403);
        }

        return new OrderResource($booking);
    }

    /**
     * Create Booking Order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $type)
    {
        if($type=='instant')
            return $this->instantBooking($request->all());
        elseif($type=='advanced')
            return $this->advancedBooking($request->all());

        // User::notifyNewOrder($order->id);
        
        return response()->json([
            "message" => "You must've been lost"
        ], 404);
        
    }

    public function instantBooking($data)
    {
        $validator = Validator::make($data, [
            'pick_location_name' => 'required|string|max:100',
            'pick_location_sub_name' => 'required|string|max:100',
            'pick_location_lat'  => 'required|numeric',
            'pick_location_long' => 'required|numeric',
            'pick_location_info' => 'nullable|string|max:500',
            'drop_location_name' => 'required|string|max:100',
            'drop_location_sub_name' => 'required|string|max:100',
            'drop_location_lat'  => 'required|numeric',
            'drop_location_long' => 'required|numeric',
            'drop_location_info' => 'nullable|string|max:500',
            'estimated_distance' => 'required|numeric',
            'payment_id'         => 'required|numeric',
            'promo_code'         => 'nullable|string|min:4|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // if(Auth::user()->activeBooking()){
        //     return response()->json([
        //         'message' => 'Forbidden, Complete your active order or cancel it to make new one'
        //     ], 403);
        // }

        $pick_timstamp = \Carbon\Carbon::now()->timezone(config('settings.timezone'))->toDateTimeString();
        
        $checkExistingBooking = Auth::user()->checkExistingBooking($pick_timstamp);
        
        if($checkExistingBooking){
            return response()->json([
                'message' => 'Forbidden, You already have active booking at this time.',
                'order'   => new OrderResource($checkExistingBooking)
            ], 403);
        }

        $appDefaults = AppDefault::firstOrFail();

        if($data['promo_code']){
            $coupon = Coupon::where('code',$data['promo_code'])->firstOrFail();

            if(!$coupon->checkIfValidCoupon($data['promo_code'])){
                return response()->json([
                    'message' => trans('response.coupon.invalid')
                ], 403);
            }
        }

        $order = Order::create([
            'customer_id'   =>  Auth::id(),
            'status'        =>  0,
            'pick_location' => [
                'name'      => $data['pick_location_name'],
                'sub_name'  => $data['pick_location_sub_name'],
                'latitude'  => $data['pick_location_lat'],
                'longitude' => $data['pick_location_long'],
                'info'      => isset($data['pick_location_info']) ? $data['pick_location_info'] : null,
            ],
            'pick_timestamp'   =>  $pick_timstamp,
            'type'               => 1,
            'estimated_distance' => $data['estimated_distance'],
            'estimated_price'    => $data['estimated_distance']*$appDefaults->cost_per_km,
            'payment_id'         => $data['payment_id'],
            'promo_code'         => isset($coupon) ? $coupon->code : null,
        ]);
            
        $dropLocation = DropLocation::create([
            'order_id'      => $order->id,
            'type'          => 2,
            'added_by'      => Auth::id(),
            'price'         => $data['estimated_distance']*$appDefaults->cost_per_km,
            'distance'      => $data['estimated_distance'],
            'drop_location' => [
                'name'      => $data['drop_location_name'],
                'sub_name'  => $data['drop_location_sub_name'],
                'latitude'  => $data['drop_location_lat'],
                'longitude' => $data['drop_location_long'],
                'info'      => isset($data['drop_location_info']) ? $data['drop_location_info'] : null,
            ]
        ]);
        
        User::notifyNewBooking($order);

        return response()->json([
            "order"   => new OrderResource($order),
            "message" => "Instant Order Created Successfully"
        ], 200);
    }

    public function advancedBooking($data)
    {   
        $appDefaults = AppDefault::firstOrFail();

        $validator = Validator::make($data, [
            'pick_timestamp'         => 'required|date_format:Y-m-d H:i:s',
            'pick_location_name'     => 'required|string',
            'pick_location_sub_name' => 'required|string|max:100',
            'pick_location_lat'      => 'required|numeric',
            'pick_location_long'     => 'required|numeric',
            'pick_location_info'     => 'nullable|string|max:500',
            'booked_hours'           => 'required|numeric',
            'payment_id'             => 'required|numeric',
            'promo_code'             => 'nullable|string|min:4|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $checkExistingBooking = Auth::user()->checkExistingBooking($data['pick_timestamp'],$data['booked_hours']);
       
        if($checkExistingBooking){
            return response()->json([
                'message' => 'Forbidden, You already have active booking at this time.',
                'order'   => new OrderResource($checkExistingBooking)
            ], 403);
        }

        if($data['promo_code']){
            $coupon = Coupon::where('code',$data['promo_code'])->firstOrFail();

            if(!$coupon->checkIfValidCoupon($data['promo_code'])){
                return response()->json([
                    'message' => trans('response.coupon.invalid')
                ], 403);
            }
        }

        $order = Order::create([
            'customer_id'   =>  Auth::id(),
            'status'        =>  0,
            'pick_timestamp'   =>  $data['pick_timestamp'],
            'pick_location' => [
                'name'      => $data['pick_location_name'],
                'sub_name'  => $data['pick_location_sub_name'],
                'latitude'  => $data['pick_location_lat'],
                'longitude' => $data['pick_location_long'],
                'info'      => isset($data['pick_location_info']) ? $data['pick_location_info'] : null,
            ],
            'drop_timestamp'   => \Carbon\Carbon::parse($data['pick_timestamp'])->addHours($data['booked_hours']),
            'type'             => 2,
            'booked_hours'     => $data['booked_hours'],
            'payment_id'       => $data['payment_id'],
            'estimated_price'  => $data['booked_hours']*60*$appDefaults->cost_per_min,
            'promo_code'       => isset($coupon) ? $coupon->code : null,
        ]);
        
        User::notifyNewBooking($order);

        return response()->json([
            "order"   => new OrderResource($order),
            "message" => "Advanced Order Created Successfully"
        ], 200);
    }

    public function active()
    {
        $bookings = Auth::user()->activeBookingOrder();

        return new OrderResource($bookings);
    }

    public function activeList()
    {
        $bookings = Order::where('customer_id',Auth::id())
                        ->whereIn('status',config('settings.customer_active_booking_statuses'))
                        ->orderBy('type','ASC')
                        ->orderBy('pick_timestamp','ASC')
                        ->get();

        return OrderResource::collection($bookings);
    }

    public function completed()
    {
        $bookings = Order::where('customer_id',Auth::id())
                       ->with('details','driver')
                       ->where('status','>',4)
                       ->orderBy('created_at','DESC')
                       ->simplePaginate(10);

        return OrderResource::collection($bookings);
    }

    public function cancel(Request $request, $order_id)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_reason'  => 'required|string|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $order = Order::findOrFail($order_id);

        // User::notifyBookingCancelled($order);

        if(Auth::id()!=$order->customer_id){
            return response()->json([
                'message' => 'Forbidden! You donot have access to this order',
            ], 403);
        }

        $order->update([
            'cancellation_reason' => $request->cancellation_reason
        ]);
        
        $order->delete();

        return response()->json([
            'message' => 'Booking Cancelled',
        ], 200);
    }

    public function cancelled()
    {
        $bookings = Order::where('customer_id',Auth::id())->onlyTrashed()->paginate(10);

        return OrderResource::collection($bookings);
    }

    public function checkCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promo_code'  => 'required|string|min:4|max:15|exists:coupons,code'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => trans('response.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $coupon = Coupon::where('code',$request->promo_code)->firstOrFail();

        if(!$coupon->checkIfValidCoupon($request->promo_code)){
            return response()->json([
                'message' => trans('response.coupon.invalid')
            ], 403);
        }
      
        $discount = '';
        if($coupon->type==1)
            $discount = $coupon->discount.'%';
        elseif($coupon->type==2)
            $discount = config('settings.currency').' '.$coupon->discount;
        
        return response()->json([
            'message'     =>  trans('response.coupon.valid'),
            'code'        =>  $coupon->code,
            'discount'    =>  $discount,
            'valid_from'  =>  $coupon->valid_from,
            'valid_to'    =>  $coupon->valid_to
        ]);
    }
}
