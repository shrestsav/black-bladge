<?php

namespace App\Http\Resources\Api\Admin;

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
            'id'            => $this->id,
            'code'          => $this->code,
            'user_id'       => $this->user_id,
            'coupon_type'   => $this->coupon_type,
            'type'          => $this->type,
            'type_is'       => $this->type==1 ? 'Percentage' : ($this->type==2 ? 'Amount' : 'Not Mentioned'),
            'discount'      => $this->discount,
            'description'   => $this->description,
            'valid_from'    => $this->valid_from,
            'valid_to'      => $this->valid_to,
            'status'        => $this->status,
            'total_redeems' => $this->total_redeems,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
