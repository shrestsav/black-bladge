<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCard extends Model
{
    protected $fillable = [
    	'user_id',
    	'name',
    	'type',
    	'card_no',
    	'month_year',
    	'csv',
    	'is_default'
	];
	

	protected $casts = [
        'card_no' => 'string',
        'type' => 'string',
    ];
}
