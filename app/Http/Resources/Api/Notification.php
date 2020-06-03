<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class Notification extends JsonResource
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
            'data'        => [
                "notifyType" => $this->data['notifyType'],
                "message"    => $this->data['message'],
                "url"        => $this->data['url'],
                "created_at" => $this->created_at
            ],
            'read_at'     => $this->read_at,
            'created_at'  => $this->created_at
        ];
    }
}
