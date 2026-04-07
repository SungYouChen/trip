<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItineraryDay extends Model
{
    use SoftDeletes, \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = ['trip_id', 'date', 'day_number', 'title', 'location', 'summary', 'accommodation', 'accommodation_details'];

    protected $casts = [
        'date' => 'date',
        'accommodation_details' => 'array',
    ];

    public function getDisplayDateAttribute()
    {
        if ($this->date) {
            return $this->date->format('m/d');
        }
        return 'Day ' . $this->day_number;
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function events()
    {
        return $this->hasMany(ItineraryEvent::class)->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(DayComment::class)->orderBy('created_at', 'asc');
    }
}
