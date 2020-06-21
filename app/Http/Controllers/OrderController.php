<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use App\OrderItem;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\Api\Admin\Order as OrderResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $orders = Order::with('customer','driver')->get();
      
      return $orders;
    }

    public function getOrders($status)
    {
      $statusArr = [];

      if($status==='Active')
        $statusArr = config('settings.active');
      else if($status==='Unassigned')
        $statusArr = config('settings.unassigned');
      else if($status==='Assigned')
        $statusArr = config('settings.assigned');
      else if($status==='Completed')
        $statusArr = config('settings.completed');

      $orders = Order::with('customer','driver');

      if($status==='Cancelled')
    		$orders->onlyTrashed();
      else
        $orders->whereIn('status',$statusArr);

      $orders = $orders->orderBy('created_at','DESC')
                      ->orderBy('status','ASC')
                      ->paginate(Session::get('rows'));

      return OrderResource::collection($orders);
    }

    public function searchOrders(Request $request, $status)
    {
      $statusArr = [];

      if($status==='Active')
        $statusArr = config('settings.active');
      else if($status==='Unassigned')
        $statusArr = config('settings.unassigned');
      else if($status==='Assigned')
        $statusArr = config('settings.assigned');
      else if($status==='Completed')
        $statusArr = config('settings.completed');
      
      $orders = Order::whereIn('status',$statusArr)
                      ->with('customer','driver');

      if($request->orderID){
        $orders->where('id','like','%'.$request->orderID.'%');
      }
      if($request->pick_location){
        $orders->where('pick_location->name','like','%'.$request->pick_location.'%');
      }
      if($request->drop_location){
        $orders->where('drop_location->name','like','%'.$request->drop_location.'%');
      }
      if($request->type)
        $orders->where('type',$request->type);
      if($request->orderStatus || $request->orderStatus=='0')
        $orders->where('status',$request->orderStatus);
      if($request->pick_date)
				$orders->whereDate('pick_timestamp',$request->pick_date);
				
      $orders = $orders->orderBy('orders.created_at','DESC')
                       ->orderBy('orders.status','ASC')
                       ->get();
                       
      // To search customer or driver, collects all data and then filters the fullname, will be major complication if large collection
      //Best Option: make a new field in table named slug where concatenate with '-' for fname and lname, and simply search there

      if($request->customer){
        $customer = $request->customer;
        $orders = $orders->filter(function ($item) use ($customer) {
            // replace stristr with your choice of matching function
            return false !== stristr($item->customer->full_name, $customer);
        });
        // $names = explode(" ", $request->customer);
        // $orders = $orders->where('customers.fullname','like','%'.$request->customer.'%');
               // ->where(function($query) use ($names) {
               //    $query->whereIn('customers.fname', $names);
               //    $query->orWhere(function($query) use ($names) {
               //        $query->whereIn('customers.lname', $names);
               //    });
               //  });
      }
      if($request->pick_driver){
        $pick_driver = $request->pick_driver;
        $orders = $orders->filter(function ($item) use ($pick_driver) {
          if($item->driver)
            return false !== stristr($item->driver->full_name, $pick_driver);
        });
      }

      $collection = collect([
        'data' =>  OrderResource::collection($orders)
      ]);
      
      return response()->json($collection);
    }

    public function getOrdersCount()
    {
      $active = config('settings.active');
      $unassigned = config('settings.unassigned');
      $assigned = config('settings.assigned');
      $completed = config('settings.completed');

      $activeBookings = Order::whereIn('status',$active)->count();
      $unassignedBookings = Order::whereIn('status',$unassigned)->count();
      $assignedBookings = Order::whereIn('status',$assigned)->count();
      $completedBookings = Order::whereIn('status',$completed)->count();
      $cancelledBookings = Order::onlyTrashed()->count();

      $collection = collect([
        'Active'      => $activeBookings,
        'Unassigned'  => $unassignedBookings,
        'Assigned'    => $assignedBookings,
        'Completed'   => $completedBookings,
        'Cancelled'   => $cancelledBookings
      ]);

      return response()->json($collection);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $validatedData = $request->validate([
        'customer_id' => 'required|numeric',
        'type' => 'required|numeric',
        'drop_location' => 'required|numeric',
        'pick_location' => 'required|numeric',
        'pick_date' => 'required|date',
        'pick_timerange' => 'required|string',
      ]);

      $order = Order::create($request->all());

      if($order){
        User::notifyNewOrder($order->id);
        return response()->json('Order Created Successfully');
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $booking = Order::with('details','customer','driver')->findOrFail($id);

      return new OrderResource($booking);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        return $request->all();
    }    

    /**
     * Just soft deleting bookings / cancel booking
     */
    public function cancelMultipleOrders(Request $request)
    {
      $orders = Order::whereIn('id',$request->orderIds)->delete();
      return response()->json(['message'=>'Orders have been cancelled']);
    }

    /**
     * deleting bookings permanently
     */
    public function deleteMultipleOrders(Request $request)
    {
        foreach($request->orderIds as $id){
          $order = Order::withTrashed()->where('id',$id)->first();
          if($order){
            $order->details()->delete();
            $order->dropLocations()->delete();
            $order->bookingExtendedTime()->delete();
            $order->bookingLogs()->delete();
            $order->forceDelete();
          }
        }
        return response()->json(['message'=>'Orders have been deleted']);
    }

    public function assignOrder(Request $request)
    {
      $validatedData = $request->validate([
        'driver_id' => 'required',
        'order_id' => 'required',
        'type' => 'required',
      ]);

      if($request->type=='pickAssign'){
            // $validatedData = $request->validate([
            //     'pick_date' => 'required',
            //     'pick_timerange' => 'required',
            // ]);
        $assign = Order::where('id','=',$request->order_id)
        ->update([
          'driver_id' => $request->driver_id, 
                                // 'pick_date' => $request->pick_date,
                                // 'pick_timerange' => $request->pick_timerange,
          'status' => 1
        ]);
        $orderDetails = OrderDetail::updateOrCreate(
          ['order_id' => $request->order_id],
          [
            'PAB' => Auth::id(),
            'PAT' => Date('Y-m-d h:i:s')
          ]
        );
        User::notifyAssignedForPickup($request->order_id);      
      }
      if($request->type=='dropAssign'){
        $validatedData = $request->validate([
          'drop_date' => 'required',
          'drop_timerange' => 'required',
        ]);
        $assign = Order::where('id','=',$request->order_id)
        ->update([
          'drop_driver_id' => $request->driver_id, 
          'drop_date' => $request->drop_date,
          'drop_timerange' => $request->drop_timerange,
          'status' => 5
        ]);
        $orderDetails = OrderDetail::updateOrCreate(
          ['order_id' => $request->order_id],
          [
            'DAB' => Auth::id(),
            'DAT' => Date('Y-m-d h:i:s')
          ]
        );
        User::notifyAssignedForDelivery($request->order_id);
      }

      return response()->json('Successfully Assigned');
    }


    public function getIndividualOrdersCount()
    {
      $collection = collect([
        'pending' => Order::where('status',0)->count(),
        'assigned' => Order::where('status',1)->count(),
        'invoice_generated' => Order::where('status',2)->count(),
        'invoice_confirmed' => Order::where('status',3)->count(),
        'on_work' => Order::where('status',4)->count(),
        'assigned_for_delivery' => Order::where('status',5)->count(),
        'picked_for_delivery' => Order::where('status',6)->count(),
        'delivered_by_driver' => Order::where('status',7)->count(),
      ]);
      return response()->json($collection);
    }

    public function filterCraps(){
      // $orders = Order::with('details')->get()->where('details',null);
      $orders = Order::where('status','>',0)->whereNull('driver_id')->get();
      return $orders;
    }
  }
