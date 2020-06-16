<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Card extends JsonResource
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
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'type'        => $this->type,
            'type_str'    => $this->type==3 ? 'Amex' : ($this->type==4 ? 'Visa' : ($this->type==5 ? 'Master' : 'Unknown')),
            'card_no'     => $this->card_no,
            'month_year'  => $this->month_year,
            'csv'         => $this->csv,
            'is_default'  => $this->is_default ? true : false,
            'created_at'  => $this->created_at
        ];
    }
}
