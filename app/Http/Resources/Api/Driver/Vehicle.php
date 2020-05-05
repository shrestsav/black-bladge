<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class Vehicle extends JsonResource
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
            'id'                =>  $this->id,
            'model'             =>  $this->model,
            'vehicle_number'    =>  $this->vehicle_number,
            'brand'             =>  $this->brand,
            'description'       =>  $this->description,
            'photo_src'         =>  $this->photo_src
        ];
    }
}
