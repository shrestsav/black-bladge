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
            'id'        => $this->id,
            'fname'     => $this->fname,
            'lname'     => $this->lname,
            'username'  => $this->username,
            'phone'     => $this->phone,
            'country'   => $this->country,
            'full_name' => $this->full_name,
            'photo_src' => $this->photo_src,
        ];
    }
}
