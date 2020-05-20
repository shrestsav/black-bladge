<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingAddedTime extends Model
{
    protected $fillable = [
        'order_id',
        'minutes',
        'price',
        'added_by'
    ];
}
