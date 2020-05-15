<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DropLocation extends Model
{
    protected $fillable = [
        'order_id',
        'drop_location',
        'type',
        'added_by'
    ];

    protected $casts = [
        'drop_location' => 'array'
    ];
}
