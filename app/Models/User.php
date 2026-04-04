<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'avatar',
        'background_image',
        'bg_opacity',
        'bg_blur',
        'bg_style',
        'bg_width',
        'email',
        'password',
        'is_admin',
    ];

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function collaboratingTrips()
    {
        return $this->belongsToMany(Trip::class, 'trip_user')
                    ->withPivot('role', 'is_notified')
                    ->withTimestamps();
    }
}
