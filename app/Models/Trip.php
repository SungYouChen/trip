<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'cover_image',
        'flight_info',
        'user_id',
        'base_currency',
        'target_currency',
        'exchange_rate',
        'share_token',
        'is_public',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'flight_info' => 'array',
        'is_public' => 'boolean',
    ];

    public function days()
    {
        return $this->hasMany(ItineraryDay::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'trip_collaborators')
                    ->wherePivot('status', 'accepted')
                    ->withPivot('role', 'status', 'token', 'is_notified')
                    ->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(TripCollaborator::class);
    }
}
