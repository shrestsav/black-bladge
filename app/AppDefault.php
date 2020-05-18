<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppDefault extends Model
{
    protected $fillable = [
		'VAT',
		'OTP_expiry',
		'driver_notes',
		'FAQ_link',
		'online_chat',
		'hotline_contact',
		'company_email',
		'company_logo',
		'TACS',
		'FAQS',
		'app_rows',
		'sys_rows',
		'referral_grant',
		'cost_per_km',
		'cost_per_min',
		'copyrights',
		'privacy_policies',
		'data_providers',
		'software_licences',
		'local_informations'
    ];
    
    protected $casts = [
        'driver_notes' 		 => 'array',
        'online_chat'  		 => 'array',
        'TACS' 		   		 => 'array',
        'FAQS' 		   		 => 'array',
        'copyrights' 		 => 'array',
        'privacy_policies' 	 => 'array',
        'data_providers' 	 => 'array',
        'software_licences'  => 'array',
        'local_informations' => 'array'
    ];
}
