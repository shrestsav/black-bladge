<?php

namespace App\Http\Resources\Order;

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
        return [
            'id'                  => $this->id,
            'status'              => $this->status,
            'status_is'           => config('settings.orderStatuses')[$this->status],
            'type'                => $this->type,
            'order_type'          => $this->type == 1 ? 'Instant' : 'Advanced',
            'pick_location'       => $this->pick_location,
            'pick_timestamp'      => $this->pick_timestamp,
            'drop_location'       => $this->drop_location,
            'drop_timestamp'      => $this->drop_timestamp,
            'estimated_distance'  => $this->estimated_distance,
            'estimated_price'     => $this->estimated_price,
            'booked_at'           => $this->created_at,
            'customer_id'         => $this->customer_id,
            'customer_fname'      => $this->customer['fname'],
            'customer_lname'      => $this->customer['lname'],
            'customer_full_name'  => $this->customer['full_name'],
            'customer_phone'      => $this->customer['phone'],
            
            //Advanced Specific
            'driver_id'           => $this->when($this->driver_id, $this->driver_id),
            'driver_fname'        => $this->when($this->driver_id, $this->driver['fname']),
            'driver_lname'        => $this->when($this->driver_id, $this->driver['lname']),
            'driver_full_name'    => $this->when($this->driver_id, $this->driver['full_name']),
            'driver_phone'        => $this->when($this->driver_id, $this->driver['phone']),
            'driver_license'      => $this->when($this->driver_id, $this->driver['license_no']),

            'booked_hours'        => $this->when($this->type==2, $this->booked_hours),
            
            'cancellation_reason' => $this->when($this->deleted_at, $this->cancellation_reason),
            'created_at'           => $this->created_at,
        ];
    }
}
