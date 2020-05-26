<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DropLocation extends JsonResource
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
            'name'        => isset($this->drop_location['name']) ? $this->drop_location['name'] : null,
            'sub_name'    => isset($this->drop_location['sub_name']) ? $this->drop_location['sub_name'] : null,
            'latitude'    => isset($this->drop_location['latitude']) ? $this->drop_location['latitude'] : null,
            'longitude'   => isset($this->drop_location['longitude']) ? $this->drop_location['longitude'] : null,
            'info'        => isset($this->drop_location['info']) ? $this->drop_location['info'] : null,
            'type'        => $this->type==1 ? 'additional' : ($this->type==2 ? 'final' : 'unknown'),
            'created_at'  => $this->created_at
        ];
    }
}
