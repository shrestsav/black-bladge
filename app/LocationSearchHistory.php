<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationSearchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'lattitude',
        'longitude',
        'favorite'
    ];

    protected $casts = [
        'favorite'  => 'int',
        'lattitude' => 'int',
        'longitude' => 'int',
    ];
}
