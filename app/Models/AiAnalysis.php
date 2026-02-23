<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'field_id',
        'type',
        'image_path',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(\App\Models\Field::class);
    }
}
