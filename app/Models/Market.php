<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    /** @use HasFactory<\Database\Factories\MarketFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'path',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function logServiceTitanJobs(): HasMany
    {
        return $this->hasMany(LogServiceTitanJob::class);
    }

    public function logEvents(): HasMany
    {
        return $this->hasMany(LogEvent::class);
    }
}
