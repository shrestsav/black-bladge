<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class Driver extends JsonResource
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
            'id'             => $this->id,
            'fname'          => $this->fname,
            'lname'          => $this->lname,
            'username'       => $this->username,
            'phone'          => $this->phone,
            'email'          => $this->email,
            'country'        => $this->country,
            'full_name'      => $this->full_name,
            'photo_src'      => $this->photo_src,
            'title'          => $this->gender,
            'license_no'     => $this->license_no,
            'vehicle_id'     => $this->vehicle_id,
            'vehicle_number' => $this->vehicle ? $this->vehicle['vehicle_number'] : null,
        ];
    }
}
