<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationSearchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'custom_name',
        'details',
        'lattitude',
        'longitude',
        'favorite'
    ];

    protected $casts = [
        'favorite'  => 'int',
        'lattitude' => 'float(10,2)',
        'longitude' => 'float',
    ];
}
