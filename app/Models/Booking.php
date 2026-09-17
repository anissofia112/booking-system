<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'service_name',
        'booking_date',
        'booking_time',
        'status',
        'total_amount',
        'hold_expires_at',
    ];

    protected $casts = [
        'hold_expires_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // A slot is blocked if confirmed OR reserved with an active timer
    public function scopeBlocksSlot(Builder $query): Builder
    {
        return $query->where('status', 'confirmed')
            ->orWhere(function (Builder $q) {
                $q->where('status', 'reserved')
                  ->where('hold_expires_at', '>', now());
            });
    }
}