<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripCollaborator extends Model
{
    protected $fillable = [
        'trip_id',
        'user_id',
        'email',
        'role',
        'status',
        'token',
        'invited_by'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
