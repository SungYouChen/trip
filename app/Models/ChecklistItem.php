<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistItem extends Model
{
    use SoftDeletes, \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = ['type', 'category', 'name', 'trip_id', 'is_completed'];
    protected $casts = ['is_completed' => 'boolean'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
