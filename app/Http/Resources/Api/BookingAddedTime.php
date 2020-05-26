<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingAddedTime extends JsonResource
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
            'minutes'     => $this->minutes,
            'price'       => $this->price,
            'created_at'  => $this->created_at
        ];
    }
}
