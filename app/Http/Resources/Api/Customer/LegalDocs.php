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
            'copyright'                   => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en'],
            'terms_and_conditions'        => $local=='ar' ? $this->TACS['ar'] : $this->TACS['en'],
            'privacy_policy'              => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en'],
            'data_providers'              => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en'],
            'software_licences'           => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en'],
            'local_information'           => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en'],
            'frequently_asked_questions'  => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en']
        ];
    }
}
