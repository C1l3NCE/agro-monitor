<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'crop',
        'area',
        'latitude',
        'longitude',
        'description',
        'geometry',
        'calculated_area',
        'ndvi_zones',
        'ndvi_avg',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
