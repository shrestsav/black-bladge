<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Coupon extends JsonResource
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
            'code'        => $this->code,
            'type'        => $this->type==1 ? 'Percentage' : ($this->type==2 ? 'Amount' : 'Not Mentioned'),
            'discount'    => $this->discount,
            'description' => $this->description,
            'valid_from'  => $this->valid_from,
            'valid_to'    => $this->valid_to
        ];
    }
}
