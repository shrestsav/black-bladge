<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DropLocation extends Model
{
    protected $fillable = [
        'order_id',
        'drop_location',
        'distance',
        'price',
        'type',
        'added_by'
    ];

    protected $casts = [
        'drop_location' => 'array'
    ];
}
