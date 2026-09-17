<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'provider',
        'payment_id',
        'amount',
        'currency',
        'status',
        'payload',
        'paid_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}