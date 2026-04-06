<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'itinerary_day_id',
        'user_id',
        'user_name',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itineraryDay()
    {
        return $this->belongsTo(ItineraryDay::class);
    }
}
