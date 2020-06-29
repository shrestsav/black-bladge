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
        
        $invoice = $this->generateInvoice();

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
            'booked_at'            => $this->created_at,
            'promo_code'           => $this->promo_code,
            'booked_hours'         => $this->when($this->type==2, $this->booked_hours),
            'total_booked_min'     => $this->totalBookedMinute(),
            'cancellation_reason'  => $this->when($this->deleted_at, $this->cancellation_reason),

            //Invoicing
            'pricing_unit'         => config('settings.currency'),
            'estimated_price'      => number_format($invoice['estimated_price'],2),
            'payment_method'       => $this->when($this->status==6, ($this->details['payment_type']==1) ? 'Cash on Delivery' : (($this->details['payment_type']==2) ? 'Card' : 'Cash on Delivery')),
            'total_cost'           => number_format($invoice['initial_price'],2),
            'additional_price'     => number_format($invoice['additional_price'],2),
            'coupon_discount'      => number_format($invoice['coupon_discount'],2),
            'sub_total'            => number_format($invoice['sub_total'],2),
            'VAT_percentage'       => $invoice['VAT_percentage'],
            'VAT'                  => number_format($invoice['VAT'],2),
            'grand_total'          => number_format($invoice['grand_total'],2),
            'paid_amount'          => number_format($invoice['paid_amount'],2),
            'left_amount'          => number_format($invoice['left_amount'],2),
            'payment_complete'     => $invoice['payment_complete'],
            
            //Driver Details
            'driver_id'            => $this->when($this->driver_id, $this->driver_id),
            'driver_fname'         => $this->when($this->driver_id, $this->driver['fname']),
            'driver_lname'         => $this->when($this->driver_id, $this->driver['lname']),
            'driver_full_name'     => $this->when($this->driver_id, ucfirst(strtolower($this->driver['gender'])).'. '.$this->driver['full_name']),
            'driver_phone'         => $this->when($this->driver_id, $this->driver['phone']),
            'driver_license'       => $this->when($this->driver_id, $this->driver['license_no']),
            'driver_photo_src'     => $this->when($this->driver_id, $this->driver['photo_src']),
        ];
    }
}
