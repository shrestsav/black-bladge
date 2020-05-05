<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'model',
        'vehicle_number',
        'brand',
        'photo',
        'description'
    ];

    protected $appends = ['photo_src'];

    public function getPhotoSrcAttribute()
    {
        return $this->photo ? asset('files/vehicles/'.$this->photo) : asset('/files/images/person.png');
    }
}
