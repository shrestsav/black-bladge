<?php

namespace App\Http\Resources\Api\Driver;

use App\AppDefault;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'pick_location'        => $this->pick_location,
            'drop_location'        => $this->dropLocation(),
            'additional_locations' => $this->additionalLocations(),
            'booking_added_time'   => $this->bookingExtendedTime(),
            'estimated_distance'   => $this->estimated_distance,
            'estimated_price'      => $this->estimated_price,
            'VAT_percentage'       => $appDefaults->VAT ? $appDefaults->VAT : 5,
            'VAT'                  => $this->estimated_price ? (5/100)*$this->estimated_price : null,
            'VAT_price'            => $this->estimated_price ? ((5/100)*$this->estimated_price+$this->estimated_price) : null,
            'booked_at'            => $this->created_at,
            'customer_id'          => $this->customer_id,
            'customer_fname'       => $this->customer['fname'],
            'customer_lname'       => $this->customer['lname'],
            'customer_photo_src'   => $this->customer['photo_src'],
            'customer_full_name'   => ucfirst(strtolower($this->customer['gender'])).'. '.$this->customer['full_name'],
            'customer_phone'       => $this->customer['phone'],
            'booked_hours'         => $this->when($this->type==2, $this->booked_hours),
            'total_booked_min'     => $this->totalBookedMinute(),
        ];
    }
}
