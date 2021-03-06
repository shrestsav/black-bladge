<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
    	'order_id',
    	'PAB',
    	'PAT',
		'STPL',
		'AAPL',
		'DC',
		'payment_type',
		'payment_id',
		'PT',
		'PDR',
		'paid_amount'
    ];

    protected $appends = ['payment_type_name'];

    public function getPaymentTypeNameAttribute()
    {
    	$type = 'Not Mentioned';

        if($this->payment_type==1)
        	$type = 'Cash on Delivery';
        elseif($this->payment_type==2)
        	$type = 'Card';
        elseif($this->payment_type==3)
        	$type = 'Paypal';
        
        return $type;
    } 
}
