<?php

namespace App;

use App\Order;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
    	'code',
		'description',
		'discount',
		'type',
		'coupon_type',
		'valid_from',
		'valid_to',
		'status'
    ];

    protected $appends = ['redeemed','total_redeems'];
    protected $hidden = ['redeemed','total_redeems'];

    public function userWithAccess()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function getRedeemedAttribute($value)
    {
    	if($this->coupon_type=3){
    		$redeemed = Order::where('customer_id',$this->user_id)
    						 ->where('promo_code',$this->code)
    						 ->exists();

    		if($redeemed){
    			return true;
    		}
    	}

        return false;
    }

    public function getTotalRedeemsAttribute($value)
    {
    	$redeems = 0;
    	if($this->coupon_type=1 || $this->coupon_type=2){
    		$redeems = Order::where('promo_code',$this->code)
    						 ->get()
    						 ->count();

    	}

        return $redeems;
	}
	
	// public function checkIfValidCoupon($code)
	// {
	// 	$ret = true;

	// 	$today = \Carbon\Carbon::now()->timezone(config('settings.timezone'))->toDateTimeString();
  
	// 	$coupon = Coupon::where('code', $code)
	// 					->where('status', 1)
	// 					->where('valid_from','<=',$today)
	// 					->where('valid_to','>=',$today);

	// 	if(!$coupon->exists()){
	// 		$ret = false;
	// 	}

	// 	$coupon = $coupon->first();

	// 	//If coupon is of type single use
	// 	if($coupon->coupon_type==1 || $coupon->coupon_type==3){
	// 		// check if already used
	// 		if(Order::where('customer_id',Auth::id())->where('promo_code',$code)->exists()){
	// 			$ret = false;
	// 		}
	// 	  }
		  
	// 	if($coupon->coupon_type==3 && $coupon->user_id){
	// 		if($coupon->user_id!=Auth::id()){
	// 			$ret = false;
	// 		}
	// 	}

	// 	return $ret;
	// }

	public function checkIfValidCoupon($code)
	{
		$ret = true;

		$today = \Carbon\Carbon::now()->timezone(config('settings.timezone'))->toDateTimeString();
		
		$valid_from = \Carbon\Carbon::parse($this->valid_from);
		$valid_to = \Carbon\Carbon::parse($this->valid_to);

		if($this->status != 1 || $valid_from > $today || $valid_to < $today){
			$ret = false;
		}
		// $coupon = Coupon::where('code', $code)
		// 				->where('status', 1)
		// 				->where('valid_from','<=',$today)
		// 				->where('valid_to','>=',$today);

		// if(!$coupon->exists()){
		// 	$ret = false;
		// }

		// $coupon = $coupon->first();

		//If coupon is of type single use
		if($this->coupon_type==1 || $this->coupon_type==3){
			// check if already used
			if(Order::where('customer_id',Auth::id())->where('promo_code',$code)->exists()){
				$ret = false;
			}
		  }
		  
		if($this->coupon_type==3 && $this->user_id){
			if($this->user_id!=Auth::id()){
				$ret = false;
			}
		}

		return $ret;
	}
}
