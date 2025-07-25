<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NicknameLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'nickname',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}