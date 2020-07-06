<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $totalDistance = $this->getUserTotalDistance();
        return [
            'id'            => $this->id,
            'fname'         => $this->fname,
            'lname'         => $this->lname,
            'username'      => $this->username,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'country'       => $this->country,
            'full_name'     => $this->full_name,
            'photo_src'     => $this->photo_src,
            'title'         => $this->gender,
            'total_trip'    => $totalDistance? number_format((float)$totalDistance,2): 0.00,
            'referral_id'   => $this->details ? $this->details->referral_id : null,
        ];
    }
}
