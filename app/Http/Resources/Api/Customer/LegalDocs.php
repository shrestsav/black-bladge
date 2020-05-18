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
            'copyright'                   => $local=='ar' ? $this->copyrights['ar'] : $this->copyrights['en'],
            'terms_and_conditions'        => $local=='ar' ? $this->TACS['ar'] : $this->TACS['en'],
            'privacy_policy'              => $local=='ar' ? $this->privacy_policies['ar'] : $this->privacy_policies['en'],
            'data_providers'              => $local=='ar' ? $this->data_providers['ar'] : $this->data_providers['en'],
            'software_licences'           => $local=='ar' ? $this->software_licences['ar'] : $this->software_licences['en'],
            'local_information'           => $local=='ar' ? $this->local_informations['ar'] : $this->local_informations['en'],
            'frequently_asked_questions'  => $local=='ar' ? $this->FAQS['ar'] : $this->FAQS['en']
        ];
    }
}
