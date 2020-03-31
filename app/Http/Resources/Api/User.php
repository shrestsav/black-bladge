<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'fname'       => $this->fname,
            'lname'       => $this->lname,
            'phone'       => $this->phone,
            'email'       => $this->email,
            'full_name'   => $this->full_name,
            'photo_src'   => $this->photo_src,
            'created_at'  => $this->created_at,
            'gender'      => $this->details->gender,
            'referral_id' => $this->details->referral_id
        ];
    }
}
