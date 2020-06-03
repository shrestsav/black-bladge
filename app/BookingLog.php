<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'user_type',
        'type',
        'remark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
