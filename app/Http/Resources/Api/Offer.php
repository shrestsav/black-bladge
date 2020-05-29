<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class Offer extends JsonResource
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
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'status'           => $this->status,
            'status_str'       => $this->status ? 'Active' : 'Inactive',
            'display_type'     => $this->display_type,
            'display_type_str' => $this->display_type==1 ? 'Signup' : ($this->display_type==2 ? 'Homescreen' : 'Unknown'),
            'image_src'        => asset('/files/offer_banners/'.$this->image),
        ];
    }
}
