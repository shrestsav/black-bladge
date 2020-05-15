<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Api\DropLocation as DropLocationResource;
use Auth;

class Order extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'status',
        'customer_id',
        'driver_id',
        'vehicle_id',
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
        'status'             => 'int',
        'type'               => 'int',
        'pick_location'      => 'array',
        'drop_location'      => 'array',
        'booked_hours'       => 'float',
        'estimated_distance' => 'float',
        'estimated_price'    => 'float',
    ];

    protected $appends = ['end_booking_timestamp'];
    
    public function getEndBookingTimestampAttribute()
    {
        return $this->type==2 ? \Carbon\Carbon::parse($this->pick_timestamp)->addHours($this->booked_hours) : null;
    }

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

    public function dropLocations()
    {
        return $this->hasMany(DropLocation::class,'order_id');
    }

    public function dropLocation()
    {
        $dropLocation = $this->dropLocations()->where('type',2)->orderBy('created_at','DESC')->first();

        return new DropLocationResource($dropLocation);    
    }

    public function additionalLocations()
    {
        $dropLocations = $this->dropLocations()->where('type',1)->orderBy('created_at','ASC')->get();

        return DropLocationResource::collection($dropLocations);    
    }
}
