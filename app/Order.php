<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Order extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'status',
        'customer_id',
        'driver_id',
        'payment_id',
		'promo_code',
		'type',
		'pick_location',
		'pick_timestamp',
		'drop_location',
        'drop_timestamp',
        'booked_hours',
        'estimated_distance',
        'estimated_price',
        'payment',
        'cancellation_reason'
    ];

    protected $casts = [
        'status'        => 'int',
        'type'          => 'int',
        'pick_location' => 'array',
        'drop_location' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class,'driver_id');
    }

    public function details()
    {
        return $this->hasOne(OrderDetail::class);
    }
}
