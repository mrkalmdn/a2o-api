<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventName extends Model
{
    /** @use HasFactory<\Database\Factories\EventNameFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'display_on_client',
    ];

    protected $casts = [
        'display_on_client' => 'boolean',
    ];
}
