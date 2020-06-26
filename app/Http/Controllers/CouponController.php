<?php

namespace App\Http\Controllers;

use App\Order;
use App\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coupons = Coupon::orderBy('valid_to','ASC');
        if($request->coupon_type){
            $coupons->where('coupon_type',$request->coupon_type);
        }else{
            $coupons->whereIn('coupon_type',[1,2]);
        }
        if($request->status!=""){
            $coupons->where('status',$request->status);
        }
        if($request->active_date){
            $dateArray = explode(',',$request->active_date);

            if($dateArray[0] !=''){
                $coupons->where('valid_from', '>' ,Carbon::createFromFormat('D M d Y H:i:s e+', $dateArray[0])->toDateTimeString());
            }
            if($dateArray[1] !=''){
                $coupons->where('valid_to', '<' ,Carbon::createFromFormat('D M d Y H:i:s e+', $dateArray[1])->toDateTimeString());
            }
        }
        $coupons = $coupons->paginate(config('settings.rows'));
        $coupons->setCollection( $coupons->getCollection()->makeVisible('total_redeems'));
        return response()->json($coupons);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function referralCoupons()
    {
        $coupons = Coupon::where('coupon_type',3)->with('userWithAccess:id,fname,lname')->orderBy('id','DESC')->paginate(config('settings.rows'));
        $coupons->setCollection( $coupons->getCollection()->makeVisible('redeemed'));
        return response()->json($coupons);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function redeemedOrders($code)
    {
        $orders = Order::where('coupon',$code)
                        ->with('customer:id,fname,lname')
                        ->orderBy('customer_id','DESC')
                        ->orderBy('id','DESC')
                        ->get();

        return response()->json($orders);
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
            'code' => 'required|alpha_num|unique:coupons|string|min:4|max:15',
            'status' => 'required|numeric',
            'description' => 'required',
            'discount' => 'required|numeric',
            'type' => 'required|numeric',
            'coupon_type' => 'required|numeric',
            'valid_from_to' => 'required',
        ]);

        $coupon = new Coupon();
        $coupon->code = strtoupper($request->code);
        $coupon->status = $request->status;
        $coupon->type = $request->type;
        $coupon->coupon_type = $request->coupon_type;
        $coupon->discount = $request->discount;
        $coupon->description = $request->description;
        $coupon->valid_from = $request->valid_from_to[0];
        $coupon->valid_to = $request->valid_from_to[1];
        $coupon->save();

        return response()->json('Successfully Added Coupon');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            // 'code' => 'required|unique:coupons|string|min:7|max:7',
            // 'type' => 'required|numeric',
            'description' => 'required',
            // 'discount' => 'required|numeric',
            'status' => 'required|numeric',
        ]);

        $coupon = Coupon::findOrFail($id);
        $coupon->update([
            // 'code' => strtoupper($request->code),
            // 'type' => $request->type,
            // 'discount' => $request->discount,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json('Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);

        $coupon->delete();
        return response()->json('Coupon Deleted');
    }

}
