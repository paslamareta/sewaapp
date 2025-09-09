<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = [
        'emails',
        'subject',
        'message',
        'sent_at',
    ];

    // Biar otomatis casting jadi Carbon (datetime)
    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
