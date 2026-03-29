<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'description',
        'amount',
        'category',
        'date',
        'trip_id',
        'is_base_currency',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    protected $casts = [
        'date' => 'date',
    ];
}
