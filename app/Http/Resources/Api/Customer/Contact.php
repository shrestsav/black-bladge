<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Contact extends JsonResource
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
            'address'   => 'Dubai',
            'email'     => 'info@blackbadge.com',
            'facebook'  => 'https://www.facebook.com/blackbadge',
            'instagram' => 'https://www.instagram.com/blackbadge',
            'linkedin'  => 'https://www.linkedin.com/blackbadge',
            'twitter'   => 'https://www.twitter.com/blackbadge'
        ];
    }
}
