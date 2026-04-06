<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripComment extends Model
{
    protected $fillable = ['trip_id', 'user_id', 'user_name', 'content'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
