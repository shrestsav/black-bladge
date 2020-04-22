<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\Customer\Order as OrderResource;

class BookingController extends Controller
{
    public function list()
    {
        $bookings = Order::where('customer_id',Auth::id())->paginate(10);

        return response()->json($bookings);
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
            'pick_location_lat'  => 'required|numeric',
            'pick_location_long' => 'required|numeric',
            'drop_location_name' => 'required|string|max:100',
            'drop_location_lat'  => 'required|numeric',
            'drop_location_long' => 'required|numeric',
            'estimated_distance' => 'required|numeric',
            'payment_id'         => 'required|numeric',
            'promo_code'         => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::create([
            'customer_id'   =>  Auth::id(),
            'pick_location' => [
                'name'      => $data['pick_location_name'],
                'latitude'  => (int) $data['pick_location_lat'],
                'longitude' => (int) $data['pick_location_long']
            ],
            'drop_location' => [
                'name'      => $data['drop_location_name'],
                'latitude'  => (int) $data['drop_location_lat'],
                'longitude' => (int) $data['drop_location_long']
            ],
            'type'               => 1,
            'estimated_distance' => $data['estimated_distance'],
            'payment_id'         => $data['payment_id'],
        ]);

        return response()->json([
            "message" => "Instant Order Created Successfully"
        ], 200);
    }

    public function advancedBooking($data)
    {
        $validator = Validator::make($data, [
            'pick_timestamp'     => 'required|date_format:Y-m-d H:i:s|',
            'pick_location_name' => 'required|string',
            'pick_location_lat'  => 'required|numeric',
            'pick_location_long' => 'required|numeric',
            'booked_hours'       => 'required|numeric',
            'payment_id'         => 'required|numeric',
            'promo_code'         => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => '422',
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::create([
            'customer_id'   =>  Auth::id(),
            'pick_timestamp'   =>  $data['pick_timestamp'],
            'pick_location' => [
                'name'      => $data['pick_location_name'],
                'latitude'  => $data['pick_location_lat'],
                'longitude' => $data['pick_location_long']
            ],
            'type'          => 2,
            'booked_hours'  => $data['booked_hours'],
            'payment_id'    => $data['payment_id'],
        ]);

        return response()->json([
            "message" => "Advanced Order Created Successfully"
        ], 200);
    }

    public function new()
    {
        $bookings = Order::where('customer_id',Auth::id())
                       ->with('details','driver')
                       ->where('status',0)
                       ->orderBy('created_at','DESC')
                       ->first();

        return new OrderResource($bookings);
    }

    public function active()
    {
        $bookings = Order::where('customer_id',Auth::id())
                       ->with('details','driver')
                       ->where('status','>',0)
                       ->orderBy('created_at','DESC')
                       ->simplePaginate(10);

        return OrderResource::collection($bookings);
    }
}
