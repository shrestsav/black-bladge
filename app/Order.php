<?php

namespace App;

use Auth;
use App\AppDefault;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Api\DropLocation as DropLocationResource;

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

    public function coupon()
    {
        return $this->belongsTo(Coupon::class,'promo_code','code');
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

    public function bookingExtendedTime()
    {
        return $this->hasMany(BookingAddedTime::class,'order_id');
    }

    public function additionalBookedMinute()
    {
        $bookedTime = 0;

        foreach($this->bookingExtendedTime as $BAT){
            $bookedTime += $BAT->minutes;
        }

        return $bookedTime;
    }

    public function totalBookedMinute()
    {
        $customerBooked = $this->booked_hours ? $this->booked_hours*60 : 0;  //converting hours to minutes if exists
        
        $bookedTime = $this->additionalBookedMinute();

        return $bookedTime+$customerBooked;
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

    public function bookingRoute()
    {
        $pickLocation = $this->pick_location;
        $dropLocations = $this->dropLocations;
        $destination = DropLocationResource::collection($dropLocations->where('type',2));
        $additional = DropLocationResource::collection($dropLocations->where('type',1));
        
        $route = [$pickLocation];

        foreach($additional as $a){
            array_push($route,$a);
        }

        if(count($destination))
            array_push($route,$destination[0]);

        return $route;

    }

    //Call this method only if distance /CPM needs to be updated or added in instant bookings
    public function updatePriceAndDistanceForInstant()
    {
        $dropLocations = $this->dropLocations;
        $price = 0;
        $distance = 0;

        foreach($dropLocations as $dropLocation){
            $price += $dropLocation->price;
            $distance += $dropLocation->distance;
        }
        
        $this->update([
            'estimated_price'    => $price,
            'estimated_distance' => $distance,
        ]);

        return true;
    }

    public function updatePriceForInstant()
    {
        $appDefaults = AppDefault::firstOrFail();
        
        $bookingAddedTime = $this->bookingExtendedTime;
        $bookedTimePrice = 0;

        foreach($bookingAddedTime as $BAT){
            $bookedTimePrice += $BAT->price;
        }

        $totalDistance = $this->estimated_distance;
        $distancePrice = $this->estimated_distance*$appDefaults->cost_per_km;

        $this->update([
            'estimated_price' => $bookedTimePrice+$distancePrice
        ]);

        return true;
    }

    public function updatePriceForAdvance()
    {
        $appDefaults = AppDefault::firstOrFail();
        
        $bookingAddedTime = $this->bookingExtendedTime;
        $bookedTimePrice = 0;

        foreach($bookingAddedTime as $BAT){
            $bookedTimePrice += $BAT->price;
        }

        $clientBookedHours = $this->booked_hours; //this one is on hours
        $clientBookedPrice = $clientBookedHours*60*$appDefaults->cost_per_min;

        $this->update([
            'estimated_price' => $bookedTimePrice+$clientBookedPrice
        ]);

        return true;
    }
}
