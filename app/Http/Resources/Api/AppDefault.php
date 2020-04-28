<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AppDefault extends JsonResource
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
            'pricing_unit' => 'DHS',
            'cost_per_km'  => $this->cost_per_km,
            'cost_per_min' => $this->cost_per_min,
        ];
    }
}
