<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'image',
        'created_by'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Define the default values for attributes
    protected $attributes = [
        'status' => 'available'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
} 