<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItineraryEvent extends Model
{
    use SoftDeletes, \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'itinerary_day_id', 
        'time', 
        'activity', 
        'address',
        'sub_activities', 
        'note', 
        'map_query', 
        'latitude',
        'longitude',
        'sort_order'
    ];

    protected $casts = [
        'sub_activities' => 'array'
    ];

    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id');
    }
}
