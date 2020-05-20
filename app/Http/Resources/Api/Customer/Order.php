<?php

namespace App\Http\Resources\Api\Customer;

use App\AppDefault;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\BookingAddedTime as BookingAddedTimeResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appDefaults = AppDefault::first();
        
        return [
            'id'                   => $this->id,
            'status'               => $this->status,
            'status_str'           => config('settings.orderStatuses')[$this->status],
            'type'                 => $this->type,
            'order_type'           => $this->type == 1 ? 'Instant' : 'Advanced',
            'pick_timestamp'       => $this->pick_timestamp,
            'pick_location'        => $this->pick_location,
            'drop_timestamp'       => $this->when($this->type==2, $this->drop_timestamp),
            'drop_location'        => $this->dropLocation(),
            'additional_locations' => $this->additionalLocations(),
            'booking_added_time'   => BookingAddedTimeResource::collection($this->bookingExtendedTime),
            'estimated_distance'   => $this->estimated_distance,
            'estimated_price'      => $this->estimated_price,
            'VAT_percentage'       => $appDefaults->VAT ? $appDefaults->VAT : 5,
            'VAT'                  => $this->estimated_price ? (5/100)*$this->estimated_price : null,
            'VAT_price'            => $this->estimated_price ? ((5/100)*$this->estimated_price+$this->estimated_price) : null,
            'booked_at'            => $this->created_at,
            'promo_code'           => $this->promo_code,

            'driver_id'            => $this->when($this->driver_id, $this->driver_id),
            'driver_fname'         => $this->when($this->driver_id, $this->driver['fname']),
            'driver_lname'         => $this->when($this->driver_id, $this->driver['lname']),
            'driver_full_name'     => $this->when($this->driver_id, $this->driver['full_name']),
            'driver_phone'         => $this->when($this->driver_id, $this->driver['phone']),
            'driver_license'       => $this->when($this->driver_id, $this->driver['license_no']),

            'booked_hours'         => $this->when($this->type==2, $this->booked_hours),
            
            'total_booked_min'     => $this->totalBookedMinute(),
            
            'cancellation_reason'  => $this->when($this->deleted_at, $this->cancellation_reason),
        ];
    }
}
