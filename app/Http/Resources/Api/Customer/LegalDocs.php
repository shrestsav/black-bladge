<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class LegalDocs extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $local = ($request->hasHeader('App-Lang')) ? $request->header('App-Lang') : 'en';

        return [
            'TAC'  => $local=='ar' ? $this->TACS['ar'] : $this->TACS['en'],
            'FAQ'  => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en']
        ];
    }
}
