<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_name',
        'latitude',
        'longitude',
        'weather_data',
        'searched_at',
    ];

    protected $casts = [
        'weather_data' => 'array',
        'searched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}