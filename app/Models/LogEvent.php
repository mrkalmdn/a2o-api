<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEvent extends Model
{
    /** @use HasFactory<\Database\Factories\LogEventFactory> */
    use HasFactory;

    protected $fillable = [
        'event_name_id',
        'market_id',
        'session_id',
        'data',
    ];
}
