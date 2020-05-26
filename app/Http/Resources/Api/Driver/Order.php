<?php

namespace App\Http\Resources\Api\Driver;

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
        
        $estimatedPrice = $this->estimated_price;
        $additionalPrice = $this->additionalBookedMinute()*$appDefaults->cost_per_min;
        $totalCost = $estimatedPrice - $additionalPrice; //Because in estimated_price field additional cost is already included while adding any additional time

        $couponDiscount = $this->coupon ? $this->coupon['discount'] : 0;
        $subTotal = $estimatedPrice - $couponDiscount;
        $VATPercentage = $appDefaults->VAT ? $appDefaults->VAT : 5;
        $VAT = ($VATPercentage/100)*$subTotal;
        $grandTotal = $VAT+$subTotal;
        
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
            'pricing_unit'         => 'DHS',
            'estimated_price'      => $estimatedPrice,
            'payment_method'       => $this->when($this->status==6, ($this->details['payment_type']==1) ? 'Cash on Delivery' : (($this->details['payment_type']==2) ? 'Card' : 'Cash on Delivery')),
            'total_cost'           => $totalCost,
            'additional_price'     => $additionalPrice,
            'coupon_discount'      => $couponDiscount,
            'sub_total'            => $subTotal,
            'VAT_percentage'       => $VATPercentage,
            'VAT'                  => $VAT,
            'grand_total'          => $grandTotal,
            
            //Customer Details
            'customer_id'          => $this->customer_id,
            'customer_fname'       => $this->customer['fname'],
            'customer_lname'       => $this->customer['lname'],
            'customer_photo_src'   => $this->customer['photo_src'],
            'customer_full_name'   => ucfirst(strtolower($this->customer['gender'])).'. '.$this->customer['full_name'],
            'customer_phone'       => $this->customer['phone'],
            'customer_photo_src'   => $this->customer['photo_src'],
        ];
    }
}
