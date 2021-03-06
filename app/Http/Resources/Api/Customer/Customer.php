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
        $totalTrip = $this->getUserTotalTrip();
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
            'total_trip'    => $totalTrip? (int)$totalTrip: 0,
            'referral_id'   => $this->details ? $this->details->referral_id : null,
        ];
    }
}
