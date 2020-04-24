<?php

namespace App\Http\Resources\Api\Customer;

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
            'type'                => $this->type,
            'order_type'          => $this->type == 1 ? 'Instant' : 'Advanced',
            'pick_location'       => $this->pick_location,
            'drop_location'       => $this->drop_location,
            'estimated_distance'  => $this->estimated_distance,
            'estimated_price'     => $this->estimated_price,
            'booked_at'           => $this->created_at,
            'driver_id'           => $this->when($this->status!=0, $this->driver_id),
            'driver_fname'        => $this->when($this->status!=0, $this->driver['fname']),
            'driver_lname'        => $this->when($this->status!=0, $this->driver['lname']),
            'driver_full_name'    => $this->when($this->status!=0, $this->driver['full_name']),
            'driver_phone'        => $this->when($this->status!=0, $this->driver['phone']),
            'driver_license'      => $this->when($this->status!=0, $this->driver['license_no']),
            'cancellation_reason' => $this->when($this->deleted_at, $this->cancellation_reason),
        ];
    }
}
